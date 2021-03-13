<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class FrontEndHomeController extends AbstractController
{
    /**
     * @Route("/", name="frontend_home", methods={"GET"})
     */
    public function index(): RedirectResponse
    {
        return new RedirectResponse('/admin');
    }

}