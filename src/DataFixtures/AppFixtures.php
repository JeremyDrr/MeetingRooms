<?php

namespace App\DataFixtures;

use App\Entity\Booking;
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
        $rooms = [];
        for($i = 1; $i < 15; $i++){
            $room = new Room();
            $room->setName('M'. strtoupper($faker->randomLetter) .$faker->randomDigit)
            ->setSeats($faker->randomDigitNotNull)
            ->setHasProjector($faker->boolean);

            $manager->persist($room);
            $rooms[] = $room;
        }

        //Gestion des réservations
        for($i = 1; $i < 20; $i++){
            $booking = new Booking();
            $booking->setUser($users[mt_rand(0, count($users) -1)])
                ->setTitle($faker->sentence)
                ->setRoom($rooms[mt_rand(0, count($rooms) -1)])
                ->setStartDate($faker->dateTimeInInterval('-0 days', '+2 days'))
                ->setEndDate($faker->dateTimeInInterval('-3 days', '+1 days'))
                ->setRecurrent($faker->boolean(50));
            $manager->persist($booking);
        }

        $manager->flush();
    }
}
