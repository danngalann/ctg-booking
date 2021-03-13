<?php

namespace App\Controller;

use App\Entity\Booking as BookingAlias;
use App\Entity\Client;
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
}
