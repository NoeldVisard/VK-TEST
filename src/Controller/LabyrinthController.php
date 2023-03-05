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
    // Нужно было создать для бизнес-логики отдельный класс, но у меня не было времени на это...
    public array $lbr;
    public array $start;
    public array $finish;
    public array $moves;
    public array $visited;
    public array $cost;
    public string $shortestPath = "";

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

        $this->lbr = $labyrinthService->parseLabyrinth($data);
        $this->start = explode(" ", $data["start"]); // ["0", "1"]
        $this->finish = explode(" ", $data["finish"]); // ["0", "9"]
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

        $this->moves = [[0, 1], [0, -1], [1, 0], [-1, 0]]; // возможные направления движения
        $this->visited = array_fill(0, count($this->lbr), array_fill(0, count($this->lbr[0]), false)); // матрица посещённых вершин
        $this->visited[$this->start[0]][$this->start[1]] = true;
        $this->cost = array_fill(0, count($this->lbr), array_fill(0, count($this->lbr[0]), INF)); // матрица стоимости пути из стартовой вершины
        $this->cost[$this->start[0]][$this->start[1]] = $this->lbr[$this->start[0]][$this->start[1]];
        $currentPoint = [$this->start[0], $this->start[1]];
        $currentCost = $this->lbr[$this->start[0]][$this->start[1]];
        $path = "($currentPoint[0]; $currentPoint[1]), ";

        // при разветвлении пути добавлялась лишняя точка лабиринта в путь, пофикшено с добавлением направления
        $directions = $this->correctDirections($currentPoint);
        foreach ($directions as $direction) {
            $this->fillOutCostMatrix($currentPoint, $currentCost, $path, $direction);
        }

        return new Response("<h4>Shortest path: $this->shortestPath</h4>");
    }

    private function fillOutCostMatrix(array $currentPoint, int $currentCost, string $path, array $move)
    {
//        if (isset($this->lbr[$currentPoint[0] + $move[0]][$currentPoint[1] + $move[1]])) {
            $nextPoint = [$currentPoint[0] + $move[0], $currentPoint[1] + $move[1]];

//            // если стена
//            if ($this->lbr[$nextPoint[0]][$nextPoint[1]] == 0)
//                continue;

            // если нет, тогда ставим как пройденную вершину
            $this->visited[$nextPoint[0]][$nextPoint[1]] = true;
            $currentCost += $this->lbr[$nextPoint[0]][$nextPoint[1]];

            // если новое найденное расстояние до вершины меньше, чем раньше найденное
            if ($this->cost[$nextPoint[0]][$nextPoint[1]] > $currentCost) {
                $this->cost[$nextPoint[0]][$nextPoint[1]] = $currentCost;
                $path .= "($nextPoint[0]; $nextPoint[1]), "; VarDumper::dump($path);
                //  если нашли финиш
                if ($nextPoint[0] == $this->finish[0] & $nextPoint[1] == $this->finish[1]) {
                    $this->shortestPath = $path;
                    echo "Possible path: " . $path . "<br>";
//                    continue;
                }

                $directions = $this->correctDirections($nextPoint);

                // рекурсивный вызов
                foreach ($directions as $direction) {
                    $this->fillOutCostMatrix($nextPoint, $currentCost, $path, $direction);
                }
            } // иначе ничего не вызываем, тк кратчайшее расстояние до точки уже найдено
//        }
    }

    private function correctDirections(array $currentPoint): array
    {
        $directions = [];
        foreach ($this->moves as $move) {
            if (isset($this->lbr[$currentPoint[0] + $move[0]][$currentPoint[1] + $move[1]])
                && $this->lbr[$currentPoint[0] + $move[0]][$currentPoint[1] + $move[1]] != 0) {
                $directions[] = $move;
            }
        }
        return $directions;
    }
}
