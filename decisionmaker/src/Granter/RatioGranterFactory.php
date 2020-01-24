<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Granter;

use asylgrp\decisionmaker\PayoutRequestCollection;
use Money\Money;

/**
 * Factory for creating ratio granters
 */
final class RatioGranterFactory implements GranterFactoryInterface
{
    public function createGranter(Money $availableFunds, PayoutRequestCollection $payouts): GranterInterface
    {
        $notGranted = $payouts->getNotGrantedAmount();

        if ($notGranted->isZero()) {
            return new RatioGranter(0.0);
        }

        $ratio = (float)$availableFunds->getAmount() / (float)$notGranted->getAmount();

        if ($ratio > 1.0) {
            $ratio = 1.0;
        }

        return new RatioGranter($ratio);
    }
}
