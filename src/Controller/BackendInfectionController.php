<?php

namespace App\Controller;

use App\Entity\Infection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/admin") */
class BackendInfectionController extends AbstractController
{

    private EntityManagerInterface $em;


    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /** @Route("/infection", name="infection") */
    public function index(): Response
    {
        return $this->render('backend/infection/index.html.twig', [
            'infections' => $this->em->getRepository(Infection::class)->findBy([], ["diagnosedOn" => "DESC"]),
        ]);
    }

    /** @Route("/infection/{infectionId}/contacts", name="get_report") */
    public function getReport(string $infectionId)
    {
        $infection = $this->em->getRepository(Infection::class)->find($infectionId);
        $contacts = $this->getContacts($infection);

        return $this->render('backend/infection/report.html.twig', [
            'infection' => $infection,
            'contacts' => $contacts,
        ]);
    }


    public function getContacts(Infection $infection)
    {
        return $this->em->getRepository(Infection::class)->contacts($infection);
    }
}