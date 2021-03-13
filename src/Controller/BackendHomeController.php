<?php

namespace App\Controller;

use App\Entity\Booking;
use DateTime;
use Doctrine\Common\Collections\Criteria;
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
        $todayBookings = $this->getFromToday($bookings);

        return $this->render('backend/index.html.twig',[
            "bookings" => $bookings,
            "todayBookings" => $todayBookings
        ]);
    }

    private function getFromToday($bookings)
    {
        $bookings = array_filter($bookings, static function ($booking) {
            $today = new DateTime();
            $today->setTime(0,0,0);
            return $booking->getDate() == $today;
        });

        return $bookings;
    }
}
