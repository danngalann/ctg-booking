<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/admin") */
class BackendHomeController extends AbstractController
{
    /**
     * @Route("/", name="backend_home")
     */
    public function index(): Response
    {
        return $this->render('backend_home/index.html.twig');
    }
}
