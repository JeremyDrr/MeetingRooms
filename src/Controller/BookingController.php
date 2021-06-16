<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Form\RoomType;
use App\Repository\BookingRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    /**
     * @Route("/booking", name="booking_index")
     */
    public function index(BookingRepository $repository, EntityManagerInterface $manager): Response
    {

        $resas = $repository->findAll();

        $rdvs = [];

        foreach($resas as $event){
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getStartDate()->format('Y-m-d H:i:s'),
                'end' => $event->getEndDate()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle()
            ];
        }

        $data = json_encode($rdvs);

        return $this->render('booking/index.html.twig',
                compact('data')


        );
    }

    /**
     * @Route("/booking/list", name="booking_list")
     * @IsGranted("ROLE_ADMIN")
     */
    public function list(BookingRepository $repository): Response
    {

        $resas = $repository->findAll();



        return $this->render('booking/list.html.twig', [
            'resas' => $resas
            ]
        );
    }

    /**
     * @Route("/booking/new", name="booking_new")
     * @IsGranted("ROLE_USER")
     */
    public function add(Request $request, EntityManagerInterface $manager): Response
    {

        $booking = new Booking();

        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $booking->setUser($this->getUser());

            if($booking->getRecurrent() == true){
                $nextBooking = new Booking();

                $startD = $booking->getStartDate()->format('d-m-Y h:i');
                $endD = $booking->getEndDate()->format('d-m-Y h:i');

                $nextBooking->setTitle($booking->getTitle() . " - Pré-réservé")
                    ->setRoom($booking->getRoom())
                    ->setStartDate((new DateTime($startD))->modify('+7 days'))
                    ->setEndDate((new DateTime($endD))->modify('+7 days'))
                    ->setUser($this->getUser())
                    ->setRecurrent(false);

                $manager->persist($nextBooking);
            }

                $manager->persist($booking);
                $manager->flush();

                //TODO: Add flash
                return $this->redirectToRoute('booking_index');

        }
        return $this->render('booking/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/booking/edit/{id}", name="booking_edit")
     * @param Request $request
     * @param Booking $booking
     * @param EntityManagerInterface $manager
     * @return Response
     * @Security("is_granted('ROLE_ADMIN') or user === booking.getUser()", message="Vous ne pouvez pas éditer cette résvervation")
     */
    public function edit(Request $request, Booking $booking,EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($booking);
            $manager->flush();

            //TODO: Add flash
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }
        return $this->render('booking/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/booking/delete/{id}", name="booking_delete")
     * @Security("is_granted('ROLE_ADMIN') or user === booking.getUser()", message="Vous ne pouvez pas éditer cette réservation")
     */
    public function delete(Request $request, Booking $booking,EntityManagerInterface $manager): Response
    {

        $manager->remove($booking);
        $manager->flush();

        //TODO: Add flash
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}