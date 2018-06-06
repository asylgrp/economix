<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker;

use asylgrp\decisionmaker\Allocator\AllocatorInterface;
use asylgrp\decisionmaker\Utils\SystemClock;
use byrokrat\amount\Amount;

/**
 * Main entry point for generating decisions
 */
class DecisionMaker
{
    /**
     * @var AllocatorInterface
     */
    private $allocator;

    /**
     * @var SystemClock
     */
    private $clock;

    /**
     * @var PayoutRequestHasher
     */
    private $payoutRequestHasher;

    public function __construct(
        AllocatorInterface $allocator,
        SystemClock $clock = null,
        PayoutRequestHasher $payoutRequestHasher = null
    ) {
        $this->allocator = $allocator;
        $this->clock = $clock ?: new SystemClock;
        $this->payoutRequestHasher = $payoutRequestHasher ?: new PayoutRequestHasher;
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
