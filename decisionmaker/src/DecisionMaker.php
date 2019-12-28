<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker;

use asylgrp\decisionmaker\Allocator\AllocatorInterface;
use byrokrat\amount\Amount;
use Lcobucci\Clock\Clock;

/**
 * Main entry point for generating decisions
 */
class DecisionMaker
{
    private AllocatorInterface $allocator;
    private Clock $clock;
    private PayoutRequestHasher $payoutRequestHasher;

    public function __construct(AllocatorInterface $allocator, Clock $clock, PayoutRequestHasher $hasher = null)
    {
        $this->allocator = $allocator;
        $this->clock = $clock;
        $this->payoutRequestHasher = $hasher ?: new PayoutRequestHasher;
    }

    /**
     * @param PayoutRequest[] $payouts
     */
    public function createDecision(Amount $funds, array $payouts, string $signature): Decision
    {
        $allocatedPayouts = $this->allocator->allocate($funds, new PayoutRequestCollection($payouts));

        return new Decision(
            $this->payoutRequestHasher->hash($allocatedPayouts),
            $signature,
            $this->clock->now(),
            $funds,
            $allocatedPayouts
        );
    }
}
