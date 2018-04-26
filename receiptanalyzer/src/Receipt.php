<?php

declare(strict_types = 1);

namespace asylgrp\receiptanalyzer;

use byrokrat\amount\Amount;

class Receipt
{
    /**
     * @var string
     */
    private $contact;

    /**
     * @var string
     */
    private $receiver;

    /**
     * @var string
     */
    private $period;

    /**
     * @var Cell
     */
    private $cell;

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

    public function getAmount(): Amount
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
        foreach ($needles as $needle) {
            foreach ($this->getTags() as $tag) {
                if (strcasecmp($needle, $tag) == 0) {
                    continue 2;
                }
            }

            return false;
        }

        return true;
    }
}
