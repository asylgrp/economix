<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Matchable;

use Money\Money;

/**
 * A set of matchables that can be matched together
 */
final class MatchableGroup implements MatchableInterface
{
    /**
     * @var MatchableInterface[]
     */
    private array $matchables;

    public function __construct(MatchableInterface ...$matchables)
    {
        $this->matchables = $matchables;
    }

    public function getMatchables(): array
    {
        return $this->matchables;
    }

    public function getId(): string
    {
        return '';
    }

    public function getDescription(): string
    {
        return '';
    }

    public function getRelatedIds(): array
    {
        return [];
    }

    public function getAmount(): Money
    {
        $amount = null;

        foreach ($this->matchables as $matchable) {
            $amount = $amount ? $amount->add($matchable->getAmount()) : $matchable->getAmount();
        }

        if (is_null($amount)) {
            throw new \LogicException('Unable to calculate amount of empty group');
        }

        return $amount;
    }

    public function getDate(): \DateTimeImmutable
    {
        $earliest = null;
        $latest = null;

        foreach ($this->matchables as $matchable) {
            $date = $matchable->getDate();
            if (is_null($earliest) || $date < $earliest) {
                $earliest = $date;
            }
            if (is_null($latest) || $date > $latest) {
                $latest = $date;
            }
        }

        if (is_null($earliest) || is_null($latest)) {
            throw new \LogicException('Unable to calculate date of empty group');
        }

        $toAdd = round($earliest->diff($latest, true)->format('%a') / 2);

        return $earliest->add(new \DateInterval("P{$toAdd}D"));
    }
}
