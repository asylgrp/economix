<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Grant;

use Money\Money;

/**
 * Value object that holds data on a specfic granted amount
 */
class GrantItem
{
    private Money $amount;
    private string $description;

    public function __construct(Money $amount, string $description)
    {
        $this->amount = $amount;
        $this->description = $description;
    }

    public function getGrantedAmount(): Money
    {
        return $this->amount;
    }

    public function getGrantDescription(): string
    {
        return $this->description;
    }
}
