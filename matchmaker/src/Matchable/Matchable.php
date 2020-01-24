<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Matchable;

use Money\Money;

/**
 * Simple matchable implementation
 */
final class Matchable implements MatchableInterface
{
    private string $id;
    private string $desc;

    /**
     * @var array<string>
     */
    private array $related;
    private Money $amount;
    private \DateTimeImmutable $date;

    /**
     * @param string[] $related
     */
    public function __construct(
        string $id,
        string $desc,
        \DateTimeImmutable $date,
        Money $amount,
        array $related = []
    ) {
        $this->id = $id;
        $this->desc = $desc;
        $this->date = $date;
        $this->amount = $amount;
        $this->related = $related;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->desc;
    }

    public function getRelatedIds(): array
    {
        return $this->related;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getMatchables(): array
    {
        return [$this];
    }
}
