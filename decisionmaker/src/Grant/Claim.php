<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Grant;

use Money\Money;

/**
 * Claim implementation of the grant interface
 */
final class Claim implements GrantInterface
{
    private \DateTimeImmutable $date;
    private Money $amount;
    private string $description;

    public function __construct(\DateTimeImmutable $date, Money $amount, string $description)
    {
        $this->date = $date;
        $this->amount = $amount;
        $this->description = $description;
    }

    public function getClaimDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getClaimedAmount(): Money
    {
        return $this->amount;
    }

    public function getClaimDescription(): string
    {
        return $this->description;
    }

    public function getGrantedAmount(): Money
    {
        return $this->amount->subtract($this->amount);
    }

    public function getNotGrantedAmount(): Money
    {
        return $this->getClaimedAmount();
    }

    public function getGrantItems(): \Generator
    {
        yield from [];
    }
}
