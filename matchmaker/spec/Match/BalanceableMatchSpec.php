<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Match;

use asylgrp\matchmaker\Match\BalanceableMatch;
use asylgrp\matchmaker\Match\MatchInterface;
use asylgrp\matchmaker\Matchable\MatchableInterface;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BalanceableMatchSpec extends ObjectBehavior
{
    function let(MatchableInterface $matchedA, MatchableInterface $matchedB)
    {
        $this->beConstructedWith($matchedA, $matchedB);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BalanceableMatch::CLASS);
    }

    function it_implements_the_match_interface()
    {
        $this->shouldHaveType(MatchInterface::CLASS);
    }

    function it_returnes_matched($matchedA, $matchedB)
    {
        $this->getMatched()->shouldIterateAs([$matchedA, $matchedB]);
    }

    function it_finds_balanced_sets($matchedA, $matchedB)
    {
        $matchedA->getAmount()->willReturn(Money::SEK('100'));
        $matchedB->getAmount()->willReturn(Money::SEK('-100'));
        $this->shouldBeBalanced();
    }

    function it_finds_non_balanced_sets($matchedA, $matchedB)
    {
        $matchedA->getAmount()->willReturn(Money::SEK('100'));
        $matchedB->getAmount()->willReturn(Money::SEK('-50'));
        $this->shouldNotBeBalanced();
    }

    function it_finds_empty_balanced_sets()
    {
        $this->beConstructedWith();
        $this->shouldBeBalanced();
    }

    function it_finds_balanceable_sets($matchedA, $matchedB)
    {
        $matchedA->getAmount()->willReturn(Money::SEK('100'));
        $matchedB->getAmount()->willReturn(Money::SEK('-100'));
        $this->shouldNotBeBalanceable();
    }

    function it_finds_non_balancable_sets($matchedA, $matchedB)
    {
        $matchedA->getAmount()->willReturn(Money::SEK('100'));
        $matchedB->getAmount()->willReturn(Money::SEK('-50'));
        $this->shouldBeBalanceable();
    }

    function it_interprets_balanced_as_success($matchedA, $matchedB)
    {
        $matchedA->getAmount()->willReturn(Money::SEK('100'));
        $matchedB->getAmount()->willReturn(Money::SEK('-100'));
        $this->shouldBeSuccess();
    }

    function it_interprets_non_balanced_as_success($matchedA, $matchedB)
    {
        $matchedA->getAmount()->willReturn(Money::SEK('100'));
        $matchedB->getAmount()->willReturn(Money::SEK('-50'));
        $this->shouldBeSuccess();
    }
}
