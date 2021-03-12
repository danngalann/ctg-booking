<?php

namespace App\Controller;

use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/admin") */
class BackendHomeController extends AbstractController
{
    private EntityManagerInterface $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="backend_home")
     */
    public function index(): Response
    {
        $bookings = $this->em->getRepository(Booking::class)->findAll();
        return $this->render('backend_home/index.html.twig',[
            "bookings" => $bookings
        ]);
    }
}
