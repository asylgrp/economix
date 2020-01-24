<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker;

use asylgrp\decisionmaker\ContactPerson\ContactPersonInterface;
use asylgrp\decisionmaker\Grant\Claim;
use Lcobucci\Clock\Clock;
use Money\Money;

/**
 * Generate a fresh payout request
 */
class PayoutRequestFactory
{
    private Clock $clock;

    public function __construct(Clock $clock)
    {
        $this->clock = $clock;
    }

    public function requestPayout(ContactPersonInterface $contactPerson, Money $amount, string $desc): PayoutRequest
    {
        if (!$contactPerson->isActive()) {
            throw new \LogicException('Unable to request payout to non-active contact person.');
        }

        return new PayoutRequest(
            $contactPerson,
            new Claim($this->clock->now(), $amount, $desc)
        );
    }
}
