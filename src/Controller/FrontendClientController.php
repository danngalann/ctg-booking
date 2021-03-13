<?php

namespace App\Controller;

use App\DataFixtures\UserFixtures;
use App\Entity\Booking;
use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/** @Route("/reservar") */
class FrontendClientController extends AbstractController
{
    private EntityManagerInterface $em;
    private UserPasswordEncoderInterface $encoder;

    /**
     * FrontendClientController constructor.
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->encoder = $encoder;
    }


    /**
     * @Route("/make", name="make", methods={"GET"})
     */
    public function makeUser(){
        $d = new UserFixtures($this->encoder);
        $d->load($this->em);
    }

    /**
     * @Route("/{bookingName}", name="frontend_client", methods={"GET"})
     */
    public function index(string $bookingName): Response
    {

        $booking = $this->em->getRepository(Booking::class)->findOneBy(["name" => $bookingName]);

        if(!$booking) {
            return $this->render('_errors/404.html.twig');
        }

        // TODO: Recover client data from cookie id
        return $this->render('frontend/index.html.twig', [
            "bookingName" => $bookingName
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

        // TODO: Check booking has available places
        $booking->addClient($client);

        $this->em->persist($booking);
        $this->em->flush();

        // TODO: Create and send UUID to be stored as a cookie
        return new JsonResponse(null, Response::HTTP_OK);
    }


}
