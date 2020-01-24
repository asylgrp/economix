<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Granter;

use asylgrp\decisionmaker\PayoutRequestCollection;
use Money\Money;

/**
 * Factory for creating fixed granters
 */
final class FixedGranterFactory implements GranterFactoryInterface
{
    private ?Money $maxAmount;

    public function __construct(Money $maxAmount = null)
    {
        $this->maxAmount = $maxAmount;
    }

    public function createGranter(Money $availableFunds, PayoutRequestCollection $payouts): GranterInterface
    {
        $nrOfClaims = count($payouts);

        if ($this->maxAmount && $this->maxAmount->multiply($nrOfClaims)->lessThanOrEqual($availableFunds)) {
            return new FixedGranter($this->maxAmount);
        }

        return new FixedGranter(
            $availableFunds->divide($nrOfClaims, Money::ROUND_DOWN)
        );
    }
}
