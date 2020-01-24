<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Granter;

use asylgrp\decisionmaker\Grant\GrantInterface;
use asylgrp\decisionmaker\Grant\Grant;
use Money\Money;

/**
 * Add a fixed amount to each grant
 */
final class FixedGranter implements GranterInterface
{
    private Money $amount;

    public function __construct(Money $amount)
    {
        $this->amount = $amount;
    }

    public function grant(GrantInterface $grant): GrantInterface
    {
        return new Grant(
            $grant,
            $this->amount,
            "Added a fixed grant of {$this->amount->getAmount()}"
        );
    }
}
