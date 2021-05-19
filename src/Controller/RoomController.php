<?php

namespace App\Controller;

use App\Entity\Room;
use App\Form\RoomType;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RoomController extends AbstractController
{

    /**
     * @Route("/rooms", name="rooms")
     */
    public function index(RoomRepository $repo){
        $rooms = $repo->findAll();

        return $this->render('rooms/index.html.twig', [
            'rooms' => $rooms
        ]);
    }

    /**
     * @Route("/rooms/new", name="rooms_new")
     */
    public function add(Request $request, EntityManagerInterface $manager): Response
    {

        $room = new Room();

        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($room);
            $manager->flush();

            //TODO: Add flash
            return $this->redirectToRoute('rooms');
        }
        return $this->render('rooms/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/rooms/edit/{id}", name="rooms_edit")
     */
    public function edit(Request $request, Room $room, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(RoomType::class, $room);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($room);
            $manager->flush();

            //TODO: Add flash
            return $this->redirectToRoute('rooms');
        }
        return $this->render('rooms/edit.html.twig', [
            'form' => $form->createView(),
            'room' => $room
        ]);
    }

    /**
     * @Route("/rooms/delete/{id}", name="rooms_delete")
     */
    public function delete(Request $request, Room $room, EntityManagerInterface $manager): Response
    {

            $manager->remove($room);
            $manager->flush();

            //TODO: Add flash
            return $this->redirectToRoute('rooms');

    }

}
