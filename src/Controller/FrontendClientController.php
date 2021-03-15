<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/reservar") */
class FrontendClientController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/{bookingName}", name="frontend_client", methods={"GET"})
     */
    public function booking(string $bookingName): Response
    {

        $booking = $this->em->getRepository(Booking::class)->nextFromToday($bookingName)[0];
        $isBookingFull = false;

        if(!$booking) {
            return $this->render('_errors/404.html.twig');
        }

        if($booking->getMaxClients() && $booking->getClients()->count() === $booking->getMaxClients()) {
            $isBookingFull = true;
        }

        // TODO: Recover client data from cookie id
        return $this->render('frontend/booking.html.twig', [
            "bookingName" => $bookingName,
            "isBookingFull" => $isBookingFull
        ]);
    }

    /**
     * @Route("/{bookingName}", name="frontend_client_book", methods={"POST"})
     */
    public function doBooking(string $bookingName, Request $request): JsonResponse
    {
        $clientName = trim($request->request->get("clientName"));
        $clientSurname = trim($request->request->get("clientSurname"));
        $clientPhone = trim($request->request->get("clientPhone"));

        if($clientName === '' || $clientSurname === '' || $clientPhone === '') {
            return new JsonResponse([
                "message" => "¿Has rellenado todos los datos?"
            ], Response::HTTP_BAD_REQUEST);
        }

        /** @var Booking $booking */
        $booking = $this->em->getRepository(Booking::class)->nextFromToday($bookingName)[0];

        $client = $this->em->getRepository(Client::class)->findOneBy([
            "name" => $clientName,
            "surname" => $clientSurname,
            "phone" => $clientPhone
        ]);

        // Create new client if it doesn't exist in DB
        if(!$client) {
            $client = Client::create(
                $clientName,
                $clientSurname,
                $clientPhone
            );

            $this->em->persist($client);
        }

        if($booking->getMaxClients() && count($booking->getClients()) === $booking->getMaxClients()) {
            return new JsonResponse([
                "message" => "No quedan plazas disponibles para esta hora :("
            ], Response::HTTP_BAD_REQUEST);
        }
        $booking->addClient($client);

        $this->em->persist($booking);
        $this->em->flush();

        // TODO: Create and send UUID to be stored as a cookie
        return new JsonResponse(null, Response::HTTP_OK);
    }

    /** @Route("/{bookingName}/check-duplicate", name="frontend_check_booking", methods={"POST"}) */
    public function isDupe(string $bookingName, Request $request): JsonResponse
    {
        $clientName = trim($request->request->get("clientName"));
        $clientSurname = trim($request->request->get("clientSurname"));
        $clientPhone = trim($request->request->get("clientPhone"));

        /** @var Booking $booking */
        $booking = $this->em->getRepository(Booking::class)->nextFromToday($bookingName)[0];

        /** @var Client $client */
        $client = $this->em->getRepository(Client::class)->findOneBy([
            "name" => $clientName,
            "surname" => $clientSurname,
            "phone" => $clientPhone
        ]);

        if (!$client) {
            return new JsonResponse(false, Response::HTTP_OK);
        }

        if ($booking->hasClient($client)) {
            return new JsonResponse(true, Response::HTTP_OK);
        }

        return new JsonResponse(false, Response::HTTP_OK);
    }

    /** @Route("/{bookingName}/delete-client", name="frontend_delete_booking", methods={"POST"}) */
    public function deleteBookingClient(string $bookingName, Request $request): JsonResponse
    {
        $clientName = trim($request->request->get("clientName"));
        $clientSurname = trim($request->request->get("clientSurname"));
        $clientPhone = trim($request->request->get("clientPhone"));

        try {
            /** @var Booking $booking */
            $booking = $this->em->getRepository(Booking::class)->nextFromToday($bookingName)[0];

            /** @var Client $client */
            $client = $this->em->getRepository(Client::class)->findOneBy([
                "name" => $clientName,
                "surname" => $clientSurname,
                "phone" => $clientPhone
            ]);

            $booking->removeClient($client);
            $this->em->persist($booking);
            $this->em->flush();
        } catch (\Exception $e) {
            return new JsonResponse([
                "message" => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(null, Response::HTTP_OK);
    }


}
