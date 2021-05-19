<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\Room;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $faker = Factory::create("fr_FR");

        //Gestion du rôle administrateur
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        //Création d'un compte administrateur
        $admin = new User();
        $admin->setFirstName('Jeremy')
            ->setLastName('Admin')
            ->setEmail('jeremy.admin@ut-capitole.fr')
            ->setDescription('Bonsoir, ceci est le compte administrateur de l\'application MeetingRooms. MeetingRooms est une solution web visant à simplifier et automatiser la réservation de salles de réunion au sein d\'un établissement.')
            ->setHash($this->encoder->encodePassword($admin, 'password'))
            ->addRole($adminRole);
        $manager->persist($admin);

        //Gestion des utilisateurs
        $users = [];
        $genres = ['male', 'female'];
        for($i = 1; $i <= 10; $i++) {
            $user = new User();
            $genre = $faker->randomElement($genres);
            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';
            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;
            $hash = $this->encoder->encodePassword($user, 'password');
            $user->setFirstName($faker->firstname($genre))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                ->setHash($hash)
                ->setPicture($picture);
            $manager->persist($user);
            $users[] = $user;
        }

        //Gestion des salles
        for($i = 0; $i < 15; $i++){
            $room = new Room();
            $room->setName($faker->firstName);

            $manager->persist($room);
        }

        $manager->flush();
    }
}
