<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\VarDumper\VarDumper;

class LabyrinthService
{
    public function parseLabyrinth(array $data): array
    {
        $result = [];
        for ($i = 0; $i < 10; $i++) {
            $result[] = explode(" ", $data[$i]);
        }
        return $result;
    }
}