<?php


namespace App\Controller;

use App\Entity\Booking;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/cron") */
class CronController extends AbstractController
{

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /** @Route("/make-bookings", name="make_bookings") */
    public function makeNextWeekBookings(): JsonResponse {

        $nextMonday = new DateTime();
        $nextMonday->modify("next monday");
        $nextMonday->setTime(0,0,0);

        $nextWednesday = new DateTime();
        $nextWednesday->modify("next wednesday");
        $nextWednesday->setTime(0,0,0);

        $nextFriday = new DateTime();
        $nextFriday->modify("next friday");
        $nextFriday->setTime(0,0,0);


        $dates = [$nextMonday, $nextWednesday, $nextFriday];

        foreach ($dates as $date){
            $bookings = [
                $this->makeBooking("Tercera", $date, 19, 20),
                $this->makeBooking("Cuarta", $date, 20, 21),
                $this->makeBooking("Quinta", $date, 21, 22)
            ];

            foreach ($bookings as $booking) {
                if (!$this->bookingAlreadyCreated($booking)) {
                    $this->em->persist($booking);
                    $this->em->flush();
                }
            }
        }

        return new JsonResponse(null, Response::HTTP_OK);
    }

    private function makeBooking(string $name, DateTime $date, int $startHour, int $endHour){
        $startTime = clone $date;
        $startTime->setTime($startHour,00,00);

        $endTime = clone $date;
        $endTime->setTime($endHour,00,00);

        return Booking::create(
            $name,
            $date,
            $startTime,
            $endTime
        );
    }

    private function bookingAlreadyCreated(Booking $booking) {
        $dbBooking = $this->em->getRepository(Booking::class)->findOneBy([
            "name" => $booking->getName(),
            "date" => $booking->getDate()
        ]);

        return $dbBooking !== null;
    }

}