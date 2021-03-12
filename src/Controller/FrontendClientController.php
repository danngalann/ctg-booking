<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontendClientController extends AbstractController
{
    /**
     * @Route("/", name="frontend_client")
     */
    public function index(): Response
    {
        return $this->render('frontend_client/index.html.twig');
    }
}