<?php

namespace App\Controller;

use App\Entity\Infection;
use App\Util\PdfManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/** @Route("/admin") */
class BackendInfectionController extends AbstractController
{
    private EntityManagerInterface $em;
    private PdfManager $pdfManager;
    private Environment $twig;

    public function __construct(
        EntityManagerInterface $em,
        PdfManager $pdfManager,
        Environment $twig
    )
    {
        $this->em = $em;
        $this->pdfManager = $pdfManager;
        $this->twig = $twig;
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

    /** @Route("/infection/{infectionId}/download/{reportType}", name="download_report") */
    public function downloadReport(string $infectionId, string $reportType)
    {
        $infection = $this->em->getRepository(Infection::class)->find($infectionId);
        $contacts = $this->getContacts($infection);

        $filename = "informe_" . $infection->getDiagnosedOn()->format('d_m_Y');
        $this->pdfManager->initialize($filename);

        $html = $this->twig->render(
            'pdf/contact_report.html.twig',
            [
                "contacts" => $contacts,
                "infection_date" => $infection->getDiagnosedOn(),
                "full" => $reportType === "full"
            ]
        );

        $this->pdfManager->loadHtml($html);
        $this->pdfManager->download();
    }

    private function getContacts(Infection $infection)
    {
        return $this->em->getRepository(Infection::class)->contacts($infection);
    }
}
