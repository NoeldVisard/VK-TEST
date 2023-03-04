<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LabyrinthController extends AbstractController
{
    #[Route('/labyrinth', name: 'app_labyrinth')]
    public function index(): Response
    {
        return $this->render('labyrinth/index.html.twig', [
            'controller_name' => 'LabyrinthController',
        ]);
    }

}
