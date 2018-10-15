<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Granter;

use asylgrp\decisionmaker\Grant\GrantInterface;
use asylgrp\decisionmaker\Grant\Grant;
use byrokrat\amount\Amount;

/**
 * Add a fixed amount to each grant
 */
final class FixedGranter implements GranterInterface
{
    /**
     * @var Amount
     */
    private $amount;

    public function __construct(Amount $amount)
    {
        $this->amount = $amount;
    }

    public function grant(GrantInterface $grant): GrantInterface
    {
        return new Grant(
            $grant,
            $this->amount,
            "Added a fixed grant of {$this->amount}"
        );
    }
}
