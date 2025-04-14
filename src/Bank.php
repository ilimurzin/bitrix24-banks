<?php

declare(strict_types=1);

namespace App;

final readonly class Bank
{
    public function __construct(
        public string $bic,
        public string $name,
    ) {}
}
