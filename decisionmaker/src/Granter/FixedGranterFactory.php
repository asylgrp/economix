<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Granter;

use asylgrp\decisionmaker\PayoutRequestCollection;
use byrokrat\amount\Amount;
use byrokrat\amount\Rounder\RoundDown;

/**
 * Factory for creating fixed granters
 */
final class FixedGranterFactory implements GranterFactoryInterface
{
    private ?Amount $maxAmount;

    public function __construct(Amount $maxAmount = null)
    {
        $this->maxAmount = $maxAmount;
    }

    public function createGranter(Amount $availableFunds, PayoutRequestCollection $payouts): GranterInterface
    {
        $nrOfClaims = count($payouts);

        if ($this->maxAmount && $this->maxAmount->multiplyWith($nrOfClaims)->isLessThanOrEquals($availableFunds)) {
            return new FixedGranter($this->maxAmount);
        }

        return new FixedGranter(
            $availableFunds->divideBy($nrOfClaims)->roundTo(0, new RoundDown)
        );
    }
}
