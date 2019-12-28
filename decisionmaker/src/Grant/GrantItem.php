<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Grant;

use byrokrat\amount\Amount;

/**
 * Value object that holds data on a specfic granted amount
 */
class GrantItem
{
    private Amount $amount;
    private string $description;

    public function __construct(Amount $amount, string $description)
    {
        $this->amount = $amount;
        $this->description = $description;
    }

    public function getGrantedAmount(): Amount
    {
        return $this->amount;
    }

    public function getGrantDescription(): string
    {
        return $this->description;
    }
}
