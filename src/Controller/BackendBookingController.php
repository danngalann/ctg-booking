<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Booking as BookingAlias;
use App\Entity\Client;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/booking") */
class BackendBookingController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /** @Route("/create", name="add_booking", methods={"GET"}) */
    public function add(): Response
    {
        return $this->render('backend/booking/create_edit.html.twig');
    }

    /** @Route("/create", name="create_booking", methods={"POST"}) */
    public function create(Request $request): JsonResponse{
        $name = $request->request->get('name');
        $slots = $request->request->get('slots');
        $date = $request->request->get('date');
        $startTime = $request->request->get('startTime');
        $endTime = $request->request->get('endTime');

        $slots = $slots === "" ? null : (int)$slots;

        try {
            // Convert string data to DateTime objects
            $startTime = $this->parseDateTime($date, $startTime);
            $endTime = $this->parseDateTime($date, $endTime);
            $date = $this->parseDateTime($date);

            $booking = Booking::create(
                $name,
                $date,
                $startTime,
                $endTime,
                $slots
            );

            $this->em->persist($booking);
            $this->em->flush();

        } catch (Exception $e) {
            return new JsonResponse([
                "message" => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, Response::HTTP_OK);
    }
    // TODO: Edit booking

    /**
     * @Route("/delete-client", name="delete_booking_client", methods={"POST"})
     */
    public function deleteBookingClient(Request $request): JsonResponse
    {
        try {
            $client = $this->em->getRepository(Client::class)->find($request->request->get("clientId"));
            $booking = $this->em->getRepository(BookingAlias::class)->find($request->request->get("bookingId"));

            $booking->removeClient($client);
            $this->em->persist($booking);
            $this->em->flush();
        }
        catch (Exception $e){
            return new JsonResponse([
                "message" => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, Response::HTTP_OK);

    }

    private function parseDateTime(string $date, string $time = "00:00") {
        $format = "d/m/Y H:i";
        $date .= " " . $time;
        return DateTime::createFromFormat($format, $date);
    }
}
