<?php

declare(strict_types = 1);

namespace asylgrp\receiptanalyzer;

use Money\Money;

class Receipt
{
    private string $contact;
    private string $receiver;
    private string $period;
    private Cell $cell;

    public function __construct(string $contact, string $receiver, string $period, Cell $cell)
    {
        $this->contact = $contact;
        $this->receiver = $receiver;
        $this->period = $period;
        $this->cell = $cell;
    }

    public function getVerificationNr(): string
    {
        return $this->cell->getVerificationNr();
    }

    public function getContactName(): string
    {
        return $this->contact;
    }

    public function getReceiverName(): string
    {
        return $this->receiver;
    }

    public function getPeriod(): string
    {
        return $this->period;
    }

    public function getAmount(): Money
    {
        return $this->cell->getAmount();
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->cell->getTags();
    }

    public function isTaggedWith(string ...$needles): bool
    {
        return $this->cell->isTaggedWith(...$needles);
    }
}
