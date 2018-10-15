<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Matcher\GroupingMatcher;
use asylgrp\matchmaker\Matcher\MatcherInterface;
use asylgrp\matchmaker\Matcher\DateComparator;
use asylgrp\matchmaker\Matcher\AmountComparator;
use asylgrp\matchmaker\Matcher\Grouper;
use asylgrp\matchmaker\Match\MatchFactoryInterface;
use asylgrp\matchmaker\Match\MatchInterface;
use asylgrp\matchmaker\Matchable\MatchableInterface;
use byrokrat\amount\Amount;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GroupingMatcherSpec extends ObjectBehavior
{
    function let(DateComparator $dateComp, AmountComparator $amountComp, Grouper $grouper, MatchFactoryInterface $factory)
    {
        $this->beConstructedWith($dateComp, $amountComp, $grouper, $factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GroupingMatcher::CLASS);
    }

    function it_implements_matcher_interface()
    {
        $this->shouldHaveType(MatcherInterface::CLASS);
    }

    function it_finds_matches(
        $dateComp,
        $amountComp,
        $grouper,
        $factory,
        MatchableInterface $matchable,
        MatchableInterface $group,
        MatchInterface $match
    ) {
        $grouper->findGroups([$matchable])->willReturn([$group]);

        $date = new \DateTimeImmutable;
        $matchable->getDate()->willReturn($date);
        $group->getDate()->willReturn($date);
        $dateComp->equals($date, $date)->willReturn(true);

        $matchable->getAmount()->willReturn(new Amount('0'));
        $group->getAmount()->willReturn(new Amount('0'));
        $amountComp->equals(new Amount('0'), new Amount('0'))->willReturn(true);

        $group->getMatchables()->willReturn([$matchable]);

        $factory->createMatch($matchable, $matchable)->willReturn($match);

        $this->match($matchable, [$matchable])->shouldReturn($match);
    }

    function it_fails_if_date_does_mot_match(
        $dateComp,
        $amountComp,
        $grouper,
        MatchableInterface $matchable,
        MatchableInterface $group
    ) {
        $grouper->findGroups([$matchable])->willReturn([$group]);

        $date = new \DateTimeImmutable;
        $matchable->getDate()->willReturn($date);
        $group->getDate()->willReturn($date);
        $dateComp->equals($date, $date)->willReturn(false);

        $matchable->getAmount()->willReturn(new Amount('0'));
        $group->getAmount()->willReturn(new Amount('0'));
        $amountComp->equals(new Amount('0'), new Amount('0'))->willReturn(true);

        $this->match($matchable, [$matchable])->shouldReturn(null);
    }

    function it_fails_if_amount_does_mot_match(
        $dateComp,
        $amountComp,
        $grouper,
        MatchableInterface $matchable,
        MatchableInterface $group
    ) {
        $grouper->findGroups([$matchable])->willReturn([$group]);

        $date = new \DateTimeImmutable;
        $matchable->getDate()->willReturn($date);
        $group->getDate()->willReturn($date);
        $dateComp->equals($date, $date)->willReturn(true);

        $matchable->getAmount()->willReturn(new Amount('0'));
        $group->getAmount()->willReturn(new Amount('0'));
        $amountComp->equals(new Amount('0'), new Amount('0'))->willReturn(false);

        $this->match($matchable, [$matchable])->shouldReturn(null);
    }
}
