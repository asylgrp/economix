<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Grant;

use byrokrat\amount\Amount;

/**
 * Claim implementation of the grant interface
 */
final class Claim implements GrantInterface
{
    /**
     * @var \DateTimeImmutable
     */
    private $date;

    /**
      * @var Amount
      */
    private $amount;

    /**
     * @var string
     */
    private $description;

    public function __construct(\DateTimeImmutable $date, Amount $amount, string $description)
    {
        $this->date = $date;
        $this->amount = $amount;
        $this->description = $description;
    }

    public function getClaimDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getClaimedAmount(): Amount
    {
        return $this->amount;
    }

    public function getClaimDescription(): string
    {
        return $this->description;
    }

    public function getGrantedAmount(): Amount
    {
        return $this->amount->subtract($this->amount);
    }

    public function getNotGrantedAmount(): Amount
    {
        return $this->getClaimedAmount();
    }

    public function getGrantItems(): \Generator
    {
        yield from [];
    }
}
