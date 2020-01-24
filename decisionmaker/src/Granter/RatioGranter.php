<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Granter;

use asylgrp\decisionmaker\Grant\GrantInterface;
use asylgrp\decisionmaker\Grant\Grant;
use Money\Money;

/**
 * Add a specified ratio of not granted amount to each grant
 */
final class RatioGranter implements GranterInterface
{
    private float $ratio;

    public function __construct(float $ratio)
    {
        if ($ratio > 1.0 || $ratio < 0.0) {
            throw new \LogicException("Invalid ratio $ratio");
        }

        $this->ratio = $ratio;
    }

    public function grant(GrantInterface $grant): GrantInterface
    {
        $amount = $grant->getNotGrantedAmount()->multiply($this->ratio, Money::ROUND_DOWN);

        if ($amount->isZero()) {
            return $grant;
        }

        return new Grant(
            $grant,
            $amount,
            "Added ratio: {$grant->getNotGrantedAmount()->getAmount()} * {$this->ratio}"
        );
    }
}
