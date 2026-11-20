<?php

namespace App\Services\Kasir;

class SerahTerimaKasService
{
    public function difference(int $mustHandOver, int $actual): int
    {
        return $actual - $mustHandOver;
    }
}
