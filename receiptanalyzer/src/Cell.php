<?php

declare(strict_types = 1);

namespace asylgrp\receiptanalyzer;

use Money\Money;

class Cell
{
    /**
     * @var string
     */
    private $vernum;

    /**
     * @var Money
     */
    private $amount;

    /**
     * @var string[]
     */
    private $tags;

    /**
     * @param string[] $tags
     */
    public function __construct(string $vernum, Money $amount, array $tags)
    {
        $this->vernum = $vernum;
        $this->amount = $amount;
        $this->tags = $tags;
    }

    public function getVerificationNr(): string
    {
        return $this->vernum;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Check if cell is tagged with one ore more tags
     */
    public function isTaggedWith(string ...$needles): bool
    {
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
