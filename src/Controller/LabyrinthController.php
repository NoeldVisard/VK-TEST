<?php

namespace App\Controller;

use App\Service\LabyrinthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;

class LabyrinthController extends AbstractController
{
    #[Route('/labyrinth', name: 'app_labyrinth')]
    public function index(): Response
    {
        return $this->render('labyrinth/index.html.twig', [
            'controller_name' => 'LabyrinthController',
        ]);
    }

    #[Route('/labyrinth/action', name: 'labyrinth_action')]
    public function action(Request $request, LabyrinthService $labyrinthService): Response
    {
        $data = $request->request->all();

        $lbr = $labyrinthService->parseLabyrinth($data);
        $start = $data["start"];
        $finish = $data["finish"];

        $lbr = [
            [0, 1, 0, 0, 0, 2, 3, 2, 0, 1],
            [1, 1, 2, 1, 0, 1, 0, 1, 1, 1],
            [1, 0, 0, 3, 4, 5, 0, 0, 0, 1],
            [1, 1, 0, 0, 0, 0, 0, 0, 0, 1],
            [0, 1, 1, 1, 1, 1, 1, 1, 1, 1],
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        ];
        return new Response('Путь');
    }
}
