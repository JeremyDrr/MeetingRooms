<?php

namespace App\Controller;

use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(BookingRepository $repository, EntityManagerInterface $manager): Response
    {

        $resas = $repository->findAll();

        foreach($resas as $resa) {
            $currentDatetime = new \DateTime('now');
            if ($currentDatetime >= $resa->getEndDate()) {
                $manager->remove($resa);
                $manager->flush();
            }
        }

        return $this->render('index.html.twig', [

        ]);
    }
}
