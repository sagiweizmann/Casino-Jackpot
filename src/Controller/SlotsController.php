<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SlotsController extends AbstractController {
    #[Route('/')]
    public function main(): Response
    {
        return $this->render('slots/index.html.twig');
    }
}