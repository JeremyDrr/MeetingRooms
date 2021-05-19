<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Form\RoomType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    /**
     * @Route("/booking", name="booking_index")
     */
    public function index(BookingRepository $repository): Response
    {

        $resas = $repository->findAll();

        return $this->render('booking/index.html.twig', [
            'resas' => $resas,
        ]);
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

            $manager->persist($booking);
            $manager->flush();

            //TODO: Add flash
            return $this->redirectToRoute('booking_index');
        }
        return $this->render('booking/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
