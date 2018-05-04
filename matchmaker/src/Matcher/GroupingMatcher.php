<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Match\MatchInterface;
use asylgrp\matchmaker\Match\MatchFactoryInterface;
use asylgrp\matchmaker\Match\BalanceableFactory;

/**
 * Match needle against grouped matchables
 */
class GroupingMatcher implements MatcherInterface
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
     * @var Grouper
     */
    private $grouper;

    /**
     * @var MatchFactoryInterface
     */
    private $matchFactory;

    public function __construct(
        DateComparator $dateComparator,
        AmountComparator $amountComparator,
        Grouper $grouper = null,
        MatchFactoryInterface $matchFactory = null
    ) {
        $this->dateComparator = $dateComparator;
        $this->amountComparator = $amountComparator;
        $this->grouper = $grouper ?: new Grouper($dateComparator, new AmountComparator(1.0), 2, 5);
        $this->matchFactory = $matchFactory ?: new BalanceableFactory;
    }

    public function match(MatchableInterface $needle, array $haystack): ?MatchInterface
    {
        foreach ($this->grouper->findGroups($haystack) as $group) {
            if ($this->dateComparator->equals($needle->getDate(), $group->getDate())
                && $this->amountComparator->equals($needle->getAmount(), $group->getAmount())
            ) {
                return $this->matchFactory->createMatch($needle, ...$group->getMatchables());
            }
        }

        return null;
    }
}
