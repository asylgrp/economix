<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Matchable\MatchableGroup;

/**
 * Find valid matchable groups
 */
class Grouper
{
    /**
     * @var DateComparator
     */
    private $dateComparator;

    /**
     * @var AmountComparator
     */
    private $amountComparator;

    /**
     * @var int
     */
    private $minGroupSize;

    /**
     * @var int
     */
    private $maxGroupSize;

    public function __construct(
        DateComparator $dateComparator,
        AmountComparator $amountComparator,
        int $minGroupSize,
        int $maxGroupSize
    ) {
        $this->dateComparator = $dateComparator;
        $this->amountComparator = $amountComparator;
        $this->minGroupSize = $minGroupSize;
        $this->maxGroupSize = $maxGroupSize;
    }

    /**
     * @param  MatchableInterface[] $matchables
     * @return MatchableInterface[]
     */
    public function findGroups(array $matchables): array
    {
        $groups = [];

        foreach ($this->getPowerSet($matchables) as $combination) {
            if ($this->isValidCombination($combination)) {
                $groups[] = new MatchableGroup(...$combination);
            }
        }

        return $groups;
    }

    /**
     * @param  MatchableInterface[] $combination
     * @return bool
     */
    private function isValidCombination(array $combination): bool
    {
        if (count($combination) < $this->minGroupSize || count($combination) > $this->maxGroupSize) {
            return false;
        }

        while ($toTest = array_pop($combination)) {
            foreach ($combination as $target) {
                if (!$this->dateComparator->equals($toTest->getDate(), $target->getDate())
                    || $this->amountComparator->equals($toTest->getAmount(), $target->getAmount())
                ) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param  MatchableInterface[] $matchables
     * @return MatchableInterface[][]
     */
    private function getPowerSet(array $matchables): array
    {
        $combinations = [[]];

        foreach ($matchables as $matchable) {
            foreach ($combinations as $combination) {
                array_push($combinations, array_merge([$matchable], $combination));
            }
        }

        return $combinations;
    }
}
