<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Matcher\AmountMatcher;
use asylgrp\matchmaker\Matcher\AmountComparator;
use asylgrp\matchmaker\Matcher\MatcherInterface;
use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Match\BalanceableMatch;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AmountMatcherSpec extends ObjectBehavior
{
    function let(AmountComparator $amountComp)
    {
        $this->beConstructedWith($amountComp);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AmountMatcher::CLASS);
    }

    function it_implements_matcher_interface()
    {
        $this->shouldHaveType(MatcherInterface::CLASS);
    }

    function it_can_find_match($amountComp, MatchableInterface $needle, MatchableInterface $matching)
    {
        $needle->getAmount()->willReturn($needleAmount = Money::SEK('1'));
        $matching->getAmount()->willReturn($matchingAmount = Money::SEK('2'));

        $needle->getDate()->willReturn(new \DateTimeImmutable);
        $matching->getDate()->willReturn(new \DateTimeImmutable);

        $amountComp->equals($needleAmount, $matchingAmount)->willReturn(true)->shouldBeCalled();

        $this->match($needle, [$matching])->shouldBeLike(
            new BalanceableMatch($needle->getWrappedObject(), $matching->getWrappedObject())
        );
    }

    function it_fails_on_no_amount_match($amountComp, MatchableInterface $needle, MatchableInterface $notMatching)
    {
        $needle->getAmount()->willReturn($needleAmount = Money::SEK('1'));
        $notMatching->getAmount()->willReturn($notMatchingAmount = Money::SEK('2'));

        $needle->getDate()->willReturn(new \DateTimeImmutable);
        $notMatching->getDate()->willReturn(new \DateTimeImmutable);

        $amountComp->equals($needleAmount, $notMatchingAmount)->willReturn(false);

        $this->match($needle, [$notMatching])->shouldBeLike(null);
    }

    function it_finds_first_match(
        $amountComp,
        MatchableInterface $needle,
        MatchableInterface $notMatching,
        MatchableInterface $matching
    ) {
        $needle->getAmount()->willReturn($needleAmount = Money::SEK('1'));
        $notMatching->getAmount()->willReturn($notMatchingAmount = Money::SEK('2'));
        $matching->getAmount()->willReturn($matchingAmount = Money::SEK('3'));

        $needle->getDate()->willReturn(new \DateTimeImmutable);
        $notMatching->getDate()->willReturn(new \DateTimeImmutable);
        $matching->getDate()->willReturn(new \DateTimeImmutable);

        $amountComp->equals($needleAmount, $notMatchingAmount)->willReturn(false);
        $amountComp->equals($needleAmount, $matchingAmount)->willReturn(true);

        $this->match($needle, [$notMatching, $matching])->shouldBeLike(
            new BalanceableMatch($needle->getWrappedObject(), $matching->getWrappedObject())
        );
    }
}
