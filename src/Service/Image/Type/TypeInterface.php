<?php

declare(strict_types=1);

namespace App\Service\Image\Type;

interface TypeInterface
{
    public function handler(string $url): array;
    public function getUrlImage($item): string;
}
