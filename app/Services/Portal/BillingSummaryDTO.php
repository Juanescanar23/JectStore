<?php

declare(strict_types=1);

namespace App\Services\Portal;

final class BillingSummaryDTO
{
    public function __construct(private readonly array $data)
    {
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
