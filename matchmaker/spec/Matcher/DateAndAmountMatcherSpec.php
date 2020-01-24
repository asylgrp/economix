<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Matcher\DateAndAmountMatcher;
use asylgrp\matchmaker\Matcher\MatcherInterface;
use asylgrp\matchmaker\Matcher\DateComparator;
use asylgrp\matchmaker\Matcher\AmountComparator;
use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Match\MatchInterface;
use asylgrp\matchmaker\Match\MatchFactoryInterface;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DateAndAmountMatcherSpec extends ObjectBehavior
{
    function let(DateComparator $dateComp, AmountComparator $amountComp, MatchFactoryInterface $matchFactory)
    {
        $this->beConstructedWith($dateComp, $amountComp, $matchFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DateAndAmountMatcher::CLASS);
    }

    function it_implements_matcher_interface()
    {
        $this->shouldHaveType(MatcherInterface::CLASS);
    }

    function it_can_find_match(
        $dateComp,
        $amountComp,
        $matchFactory,
        MatchableInterface $itemA,
        MatchableInterface $itemB,
        MatchInterface $match
    ) {
        $itemA->getAmount()->willReturn($amountA = Money::SEK('1'));
        $itemB->getAmount()->willReturn($amountB = Money::SEK('2'));
        $itemA->getDate()->willReturn($dateA = new \DateTimeImmutable);
        $itemB->getDate()->willReturn($dateB = new \DateTimeImmutable);

        $dateComp->equals($dateA, $dateB)->willReturn(true)->shouldBeCalled();
        $amountComp->equals($amountA, $amountB)->willReturn(true)->shouldBeCalled();

        $matchFactory->createMatch($itemA, $itemB)->willReturn($match);

        $this->match($itemA, [$itemB])->shouldReturn($match);
    }

    function it_fails_on_no_date_match($dateComp, $amountComp, MatchableInterface $itemA, MatchableInterface $itemB)
    {
        $itemA->getAmount()->willReturn($amountA = Money::SEK('1'));
        $itemB->getAmount()->willReturn($amountB = Money::SEK('2'));
        $itemA->getDate()->willReturn($dateA = new \DateTimeImmutable);
        $itemB->getDate()->willReturn($dateB = new \DateTimeImmutable);

        $dateComp->equals($dateA, $dateB)->willReturn(false);
        $amountComp->equals($amountA, $amountB)->willReturn(true);

        $this->match($itemA, [$itemB])->shouldBeLike(null);
    }

    function it_fails_on_no_amount_match($dateComp, $amountComp, MatchableInterface $itemA, MatchableInterface $itemB)
    {
        $itemA->getAmount()->willReturn($amountA = Money::SEK('1'));
        $itemB->getAmount()->willReturn($amountB = Money::SEK('2'));
        $itemA->getDate()->willReturn($dateA = new \DateTimeImmutable);
        $itemB->getDate()->willReturn($dateB = new \DateTimeImmutable);

        $dateComp->equals($dateA, $dateB)->willReturn(true);
        $amountComp->equals($amountA, $amountB)->willReturn(false);

        $this->match($itemA, [$itemB])->shouldBeLike(null);
    }

    function it_finds_first_match(
        $dateComp,
        $amountComp,
        $matchFactory,
        MatchableInterface $itemA,
        MatchableInterface $itemB,
        MatchableInterface $itemC,
        MatchInterface $match
    ) {
        $itemA->getAmount()->willReturn($amountA = Money::SEK('1'));
        $itemB->getAmount()->willReturn($amountB = Money::SEK('2'));
        $itemC->getAmount()->willReturn($amountC = Money::SEK('3'));
        $itemA->getDate()->willReturn($dateA = new \DateTimeImmutable);
        $itemB->getDate()->willReturn($dateB = new \DateTimeImmutable);
        $itemC->getDate()->willReturn($dateC = new \DateTimeImmutable);

        $dateComp->equals($dateA, $dateB)->willReturn(true);
        $amountComp->equals($amountA, $amountB)->willReturn(false);

        $dateComp->equals($dateA, $dateC)->willReturn(true);
        $amountComp->equals($amountA, $amountC)->willReturn(true);

        $matchFactory->createMatch($itemA, $itemC)->willReturn($match);

        $this->match($itemA, [$itemB, $itemC])->shouldBeLike($match);
    }
}
