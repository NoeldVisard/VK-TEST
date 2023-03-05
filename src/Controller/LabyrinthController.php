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
        $start = explode(" ", $data["start"]); // ["0", "1"]
        $finish = explode(" ", $data["finish"]); // ["0", "9"]
//        $lbr = [
//            [0, 1, 0, 0, 0, 2, 3, 2, 0, 1],
//            [1, 1, 2, 1, 0, 1, 0, 1, 1, 1],
//            [1, 0, 0, 3, 4, 5, 0, 0, 0, 1],
//            [1, 1, 0, 0, 0, 0, 0, 0, 0, 1],
//            [0, 1, 1, 1, 1, 1, 1, 1, 1, 1],
//            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
//            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
//            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
//            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
//            [0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
//        ]; // test labyrinth array

        $moves = [[0, 1], [0, -1], [1, 0], [-1, 0]]; // возможные направления движения
        $visited = array_fill(0, count($lbr), array_fill(0, count($lbr[0]), false)); // матрица посещённых вершин
        $visited[$start[0]][$start[1]] = true;
        $cost = array_fill(0, count($lbr), array_fill(0, count($lbr[0]), INF)); // матрица стоимости пути из стартовой вершины
        $cost[$start[0]][$start[1]] = $lbr[$start[0]][$start[1]];
        $finish = false;
        $currentPoint = [$start[0], $start[1]];
        $currentCost = $lbr[$start[0]][$start[1]];

        while (!$finish) {
            foreach ($moves as $move) {
                if (isset($lbr[$currentPoint[0] + $move[0]][$currentPoint[1] + $move[1]])) {
                    $nextPoint = [$currentPoint[0] + $move[0], $currentPoint[1] + $move[1]];
                    //  если нашли финиш
                    if ($nextPoint[0] == $finish[0] & $nextPoint[1] == $finish[1]) {
                        $finish = true;
                        break;
                    }
                    
                    if ($lbr[$nextPoint[0]][$nextPoint[1]] == 0) // если стена
                        continue;

                    // если нет, тогда ставим как пройденную вершину
                    $visited[$nextPoint[0]][$nextPoint[1]] = true;
                    $currentCost += $lbr[$nextPoint[0]][$nextPoint[1]];
                    // если новое найденное расстояние до вершины меньше, чем раньше найденное
                    if ($cost[$nextPoint[0]][$nextPoint[1]] > $currentCost) {
                        $cost[$nextPoint[0]][$nextPoint[1]] = $currentCost;
                    }
                    
                    // рекурсивный вызов
                }
            }
        }

        return new Response('Путь');
    }

    private function fillOutCostMatrix()
    {

    }
}
