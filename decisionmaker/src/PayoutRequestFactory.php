<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker;

use asylgrp\decisionmaker\ContactPerson\ContactPersonInterface;
use asylgrp\decisionmaker\Grant\Claim;
use asylgrp\decisionmaker\Utils\SystemClock;
use byrokrat\amount\Amount;

/**
 * Generate a fresh payout request
 */
class PayoutRequestFactory
{
    /**
     * @var SystemClock
     */
    private $clock;

    public function __construct(SystemClock $clock = null)
    {
        $this->clock = $clock ?: new SystemClock;
    }

    public function requestPayout(ContactPersonInterface $contactPerson, Amount $amount, string $desc): PayoutRequest
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
