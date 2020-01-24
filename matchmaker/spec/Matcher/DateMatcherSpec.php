<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Matcher\DateMatcher;
use asylgrp\matchmaker\Matcher\DateComparator;
use asylgrp\matchmaker\Matcher\MatcherInterface;
use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Match\NonBalanceableMatch;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DateMatcherSpec extends ObjectBehavior
{
    function let(DateComparator $dateComp)
    {
        $this->beConstructedWith($dateComp);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DateMatcher::CLASS);
    }

    function it_implements_matcher_interface()
    {
        $this->shouldHaveType(MatcherInterface::CLASS);
    }

    function it_can_find_match($dateComp, MatchableInterface $needle, MatchableInterface $matching)
    {
        $needle->getDate()->willReturn($needleDate = new \DateTimeImmutable);
        $matching->getDate()->willReturn($matchingDate = new \DateTimeImmutable);

        $needle->getAmount()->willReturn(Money::SEK('100'));
        $matching->getAmount()->willReturn(Money::SEK('-100'));

        $dateComp->equals($needleDate, $matchingDate)->willReturn(true)->shouldBeCalled();

        $this->match($needle, [$matching])->shouldBeLike(
            new NonBalanceableMatch($needle->getWrappedObject(), $matching->getWrappedObject())
        );
    }

    function it_fails_on_no_date_match($dateComp, MatchableInterface $needle, MatchableInterface $notMatching)
    {
        $needle->getDate()->willReturn($needleDate = new \DateTimeImmutable);
        $notMatching->getDate()->willReturn($noMatchingDate = new \DateTimeImmutable);

        $dateComp->equals($needleDate, $noMatchingDate)->willReturn(false);

        $this->match($needle, [$notMatching])->shouldBeLike(null);
    }

    function it_fails_on_amounts_not_inversed($dateComp, MatchableInterface $needle, MatchableInterface $notMatching)
    {
        $needle->getDate()->willReturn($needleDate = new \DateTimeImmutable);
        $notMatching->getDate()->willReturn($matchingDate = new \DateTimeImmutable);

        $needle->getAmount()->willReturn(Money::SEK('100'));
        $notMatching->getAmount()->willReturn(Money::SEK('100'));

        $dateComp->equals($needleDate, $matchingDate)->willReturn(true);

        $this->match($needle, [$notMatching])->shouldBeLike(null);
    }

    function it_finds_first_match(
        $dateComp,
        MatchableInterface $needle,
        MatchableInterface $notMatching,
        MatchableInterface $matching
    ) {
        $needle->getDate()->willReturn($needleDate = new \DateTimeImmutable);
        $notMatching->getDate()->willReturn($noMatchingDate = new \DateTimeImmutable);
        $matching->getDate()->willReturn($matchingDate = new \DateTimeImmutable);

        $needle->getAmount()->willReturn(Money::SEK('100'));
        $notMatching->getAmount()->willReturn(Money::SEK('-100'));
        $matching->getAmount()->willReturn(Money::SEK('-100'));

        $dateComp->equals($needleDate, $noMatchingDate)->willReturn(false);
        $dateComp->equals($needleDate, $matchingDate)->willReturn(true);

        $this->match($needle, [$notMatching, $matching])->shouldBeLike(
            new NonBalanceableMatch($needle->getWrappedObject(), $matching->getWrappedObject())
        );
    }
}
