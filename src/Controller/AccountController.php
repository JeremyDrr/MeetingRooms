<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\ForgetPassword;
use App\Form\ForgetPasswordType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="account_login")
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function login(AuthenticationUtils $utils): Response
    {

        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        if($this->getUser() != null)
            return $this->redirectToRoute('homepage');

        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * @Route("/logout", name="account_logout")
     */
    public function logout(){

    }

    /**
     * @Route("/register", name="account_register")
     * @IsGranted("ROLE_ADMIN")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){

        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, md5(uniqid()));
            $user->setHash($hash);

            //Génération du token de d'activation du compte
            $user->setActivationToken(md5(uniqid()));
            $user->setResetToken(md5(uniqid()));

            $manager->persist($user);
            $manager->flush();

            /**        //Création de l'email
            $email = (new TemplatedEmail())
            ->from('meetingrooms@ut-capitole.fr')
            ->to($user->getEmail())
            ->subject("Activation de votre compte MeetingRooms")
            ->htmlTemplate("emails/activation.html.twig")
            ->context([
            'firstName' => $user->getFirstName(),
            'token' => $user->getActivationToken()
            ])
            ;

            $mailer->send($email);
             **/

            return $this->redirectToRoute('account_login');
        }
        return $this->render('account/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/activation/{token}", name="activation")
     * @param $token
     * @param UserRepository $repo
     * @return RedirectResponse
     */
    public function activation($token, UserRepository $repo){
        $user = $repo->findOneBy(['activationToken' => $token]);

        if(!$user){
            //Erreur 404
            throw $this->createNotFoundException('Cet utilisateur n\'existe pas');
        }

        //Supprimer le token
        $user->setActivationToken(null);
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($user);
        $entityManager->flush();

        $loginToken = new UsernamePasswordToken(
            $user,
            $user->getHash(),
            'main',
            $user->getRoles()
        );

        $this->get('security.token_storage')->setToken($loginToken);
        $this->get('session')->set('_security_main', serialize($token));

        //Envoie un flash
        //TODO: Add flash

        return $this->redirectToRoute('reset_password', array('token' => $user->getResetToken()));
    }

    /**
     * @Route("/account/edit-profile", name="account_edit")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $manager){
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('account_index');
            //TODO: Add flash
        }

        return $this->render('account/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager){

        $user = $this->getUser();

        $passwordUpdate = new PasswordUpdate();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getHash() )){
                $form->get('oldPassword')->addError(new FormError("Le mot de passe rentré n'est pas votre mot de passe actuel"));
            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);
                $user->setHash($hash);

                $manager->persist($user);
                $manager->flush();

                //TODO: Add flash

                return $this->redirectToRoute('homepage');
            }

        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     */
    public function myAccount(){

        return $this->render('account/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/forget-password", name="account_forget_password")
     * @param Request $request
     * @param UserRepository $repo
     * @param TokenGeneratorInterface $tokenGenerator
     * @return Response
     */
    public function forgetPassword(Request $request, UserRepository $repo, TokenGeneratorInterface $tokenGenerator): Response{

        $form = $this->createForm(ForgetPasswordType::class);

        // On traite le formulaire
        $form->handleRequest($request);

        // Si le formulaire est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les données
            $donnees = $form->getData();

            // On cherche un utilisateur ayant cet e-mail
            $user = $repo->findOneByEmail($donnees['email']);

            // Si l'utilisateur n'existe pas
            if ($user === null) {
                // On envoie une alerte disant que l'adresse e-mail est inconnue
                //TODO: Add flash Adresse email inconnue

                // On retourne sur la page de connexion
                return $this->redirectToRoute('account_login');
            }

            // On génère un token
            $token = $tokenGenerator->generateToken();

            // On essaie d'écrire le token en base de données
            try{
                $user->setResetToken($token);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                //TODO: Add flash $e->getMessage()
                return $this->redirectToRoute('account_login');
            }

            // On génère l'URL de réinitialisation de mot de passe
            $url = $this->generateUrl('reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);

            /**           // On génère l'e-mail
            $message = (new TemplatedEmail())
            ->from('meetingrooms@ut-capitole.fr')
            ->to($user->getEmail())
            ->subject("Demande de réinitialisation de mot de passe")
            ->htmlTemplate('emails/forgetpassword.html.twig')
            ->context([
            'firstName' => $user->getFirstName(),
            'url' => $url,
            'token' => $user->getResetToken()
            ]
            )
            ;

            // On envoie l'e-mail
            $mailer->send($message);
             **/
            // On crée le message flash de confirmation
            //TODO: Add flash Email de réinitialisation envoyé

            // On redirige vers la page de login
            return $this->redirectToRoute('account_login');
        }

        // On envoie le formulaire à la vue
        return $this->render('account/forgetpassword.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/reset-password/{token}", name="reset_password")
     * @param Request $request
     * @param string $token
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     */
    public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $encoder){

        $resetPassword = new ResetPassword();
        $manager = $this->getDoctrine()->getManager();

        $form = $this->createForm(ResetPasswordType::class, $resetPassword, ['tokenValue' => $token]);
        $form->handleRequest($request);

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['resetToken' => $token]);

        if($user === null){
            //TODO: Add flash Token inconnu
            return $this->redirectToRoute('account_login');
        }

        if($form->isSubmitted() && $form->isValid()){
            $password = $resetPassword->getPassword();
            $hash = $encoder->encodePassword($user, $password);
            $user->setHash($hash);
            $user->setResetToken(null);

            $manager->persist($user);
            $manager->flush();

            //TODO: Add flash Mot de passe mis à jour
            return $this->redirectToRoute('account_login');
        }else{
            return $this->render('account/reset_password.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }
}
