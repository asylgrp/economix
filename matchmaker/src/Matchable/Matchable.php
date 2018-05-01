<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Matchable;

use byrokrat\amount\Amount;

/**
 * Simple matchable implementation
 */
class Matchable implements MatchableInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string[]
     */
    private $related;

    /**
     * @var Amount
     */
    private $amount;

    /**
     * @var \DateTimeImmutable
     */
    private $date;

    /**
     * @param string[] $related
     */
    public function __construct(string $id, \DateTimeImmutable $date, Amount $amount, array $related = [])
    {
        $this->id = $id;
        $this->date = $date;
        $this->amount = $amount;
        $this->related = $related;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRelatedIds(): array
    {
        return $this->related;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }
}
