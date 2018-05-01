<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Match\MatchInterface;
use asylgrp\matchmaker\Match\MatchFactoryInterface;
use asylgrp\matchmaker\Match\BalanceableFactory;

/**
 * Match based on both date and amount
 */
class DateAndAmountMatcher implements MatcherInterface
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
     * @var MatchFactoryInterface
     */
    private $matchFactory;

    public function __construct(
        DateComparator $dateComparator,
        AmountComparator $amountComparator,
        MatchFactoryInterface $matchFactory = null
    ) {
        $this->dateComparator = $dateComparator;
        $this->amountComparator = $amountComparator;
        $this->matchFactory = $matchFactory ?: new BalanceableFactory;
    }

    public function match(MatchableInterface $needle, array $haystack): ?MatchInterface
    {
        foreach ($haystack as $toMatch) {
            if ($this->dateComparator->equals($needle->getDate(), $toMatch->getDate())
                && $this->amountComparator->equals($needle->getAmount(), $toMatch->getAmount())
            ) {
                return $this->matchFactory->createMatch($needle, $toMatch);
            }
        }

        return null;
    }
}
