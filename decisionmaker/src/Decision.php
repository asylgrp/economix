<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker;

use byrokrat\amount\Amount;

/**
 * Decision to disburse funds
 */
class Decision
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var \DateTimeImmutable
     */
    private $date;

    /**
     * @var Amount
     */
    private $allocatedAmount;

    /**
     * @var PayoutRequestCollection
     */
    private $payouts;

    public function __construct(
        string $id,
        \DateTimeImmutable $date,
        Amount $allocatedAmount,
        PayoutRequestCollection $payouts
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->allocatedAmount = $allocatedAmount;
        $this->payouts = $payouts;
    }

    /**
     * Get decision id
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get decision date
     */
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * Get amount allocation was based on
     */
    public function getAllocatedAmount(): Amount
    {
        return $this->allocatedAmount;
    }

    /**
     * Get payout requests in decision
     */
    public function getPayoutRequests(): PayoutRequestCollection
    {
        return $this->payouts;
    }
}
