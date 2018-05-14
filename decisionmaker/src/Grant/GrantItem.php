<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Grant;

use byrokrat\amount\Amount;

/**
 * Value object that holds data on a specfic granted amount
 */
class GrantItem
{
    /**
     * @var Amount
     */
    private $amount;

    /**
     * @var string
     */
    private $description;

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
