<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker;

use Money\Money;

/**
 * Decision to disburse funds
 */
class Decision
{
    private string $id;
    private string $signature;
    private \DateTimeImmutable $date;
    private Money $allocatedAmount;
    private PayoutRequestCollection $payouts;

    public function __construct(
        string $id,
        string $signature,
        \DateTimeImmutable $date,
        Money $allocatedAmount,
        PayoutRequestCollection $payouts
    ) {
        $this->id = $id;
        $this->signature = $signature;
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
     * Get decision signature
     */
    public function getSignature(): string
    {
        return $this->signature;
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
    public function getAllocatedAmount(): Money
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
