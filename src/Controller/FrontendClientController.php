<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

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
    public function booking(string $bookingName, Request $request): Response
    {

        $bookings = $this->em->getRepository(Booking::class)->nextFromToday($bookingName);
        $isBookingFull = false;

        if(!$bookings) {
            return $this->render('_errors/no_bookings.html.twig',
            [
                "bookingName" => $bookingName
            ]);
        }

        $booking = $bookings[0];
        if($booking->getMaxClients() && $booking->getClients()->count() === $booking->getMaxClients()) {
            $isBookingFull = true;
        }

        $cookie = $request->cookies->get("client");
        $client = null;
        if($cookie) {
            $client = $this->em->getRepository(Client::class)->findOneBy([
                "cookie" => $request->cookies->get("client")
            ]);
        }

        return $this->render('frontend/booking.html.twig', [
            "booking" => $booking,
            "client" => $client,
            "isBookingFull" => $isBookingFull
        ]);
    }

    /**
     * @Route("/{bookingName}", name="frontend_client_book", methods={"POST"})
     */
    public function doBooking(string $bookingName, Request $request): JsonResponse
    {
        $clientName = $this->normalizeName($request->request->get("clientName"));
        $clientSurname = $this->normalizeName($request->request->get("clientSurname"));
        $clientPhone = trim($request->request->get("clientPhone"));

        if($clientName === '' || $clientSurname === '' || $clientPhone === '') {
            return new JsonResponse([
                "message" => "¿Has rellenado todos los datos?"
            ], Response::HTTP_BAD_REQUEST);
        }

        if(!$this->isValidPhone($clientPhone)) {
            return new JsonResponse([
                "message" => "El número de teléfono no tiene un formato válido. 
                Por favor introduce el número sin espacios ni guiones.
                Ejemplos: '625985878', '+34695569863'"
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
        }

        if($booking->getMaxClients() && count($booking->getClients()) === $booking->getMaxClients()) {
            return new JsonResponse([
                "message" => "No quedan plazas disponibles para esta hora :("
            ], Response::HTTP_BAD_REQUEST);
        }
        $booking->addClient($client);
        $this->em->persist($booking);

        $uuid = Uuid::v4();
        $client->setCookie($uuid);

        $this->em->persist($client);
        $this->em->flush();

        $res = new JsonResponse(null, Response::HTTP_OK);
        $cookie = Cookie::create("client", $uuid, strtotime("+1 month"));
        $res->headers->setCookie($cookie);

        return $res;
    }

    /** @Route("/{bookingName}/check-duplicate", name="frontend_check_booking", methods={"POST"}) */
    public function isDupe(string $bookingName, Request $request): JsonResponse
    {
        $clientName = $this->normalizeName($request->request->get("clientName"));
        $clientSurname = $this->normalizeName($request->request->get("clientSurname"));
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

    private function normalizeName(string $name): string
    {
        return ucwords(
            strtolower(
                trim(
                    $name
                )
            )
        );
    }

    private function isValidPhone(string $phone): bool
    {
        return preg_match("/^[\+]?\d{7,}$/", $phone);
    }


}
