<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Granter;

use asylgrp\decisionmaker\PayoutRequestCollection;
use byrokrat\amount\Amount;

/**
 * Factory for creating ratio granters
 */
final class RatioGranterFactory implements GranterFactoryInterface
{
    public function createGranter(Amount $availableFunds, PayoutRequestCollection $payouts): GranterInterface
    {
        $notGranted = $payouts->getNotGrantedAmount();

        if ($notGranted->isZero()) {
            return new RatioGranter(0.0);
        }

        $ratio = $availableFunds->divideBy($notGranted)->getFloat();

        if ($ratio > 1.0) {
            return new RatioGranter(1.0);
        }

        return new RatioGranter($ratio);
    }
}
