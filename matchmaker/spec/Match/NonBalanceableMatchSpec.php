<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Match;

use asylgrp\matchmaker\Match\NonBalanceableMatch;
use asylgrp\matchmaker\Matchable\MatchableInterface;
use byrokrat\amount\Amount;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NonBalanceableMatchSpec extends ObjectBehavior
{
    function let(MatchableInterface $matchedA, MatchableInterface $matchedB)
    {
        $this->beConstructedWith($matchedA, $matchedB);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(NonBalanceableMatch::CLASS);
    }

    function it_is_never_balanceable($matchedA, $matchedB)
    {
        $matchedA->getAmount()->willReturn(new Amount('100'));
        $matchedB->getAmount()->willReturn(new Amount('-50'));
        $this->shouldNotBeBalanceable();
    }

    function it_interprets_balanced_as_success($matchedA, $matchedB)
    {
        $matchedA->getAmount()->willReturn(new Amount('100'));
        $matchedB->getAmount()->willReturn(new Amount('-100'));
        $this->shouldBeSuccess();
    }

    function it_interprets_non_balanced_as_failure($matchedA, $matchedB)
    {
        $matchedA->getAmount()->willReturn(new Amount('100'));
        $matchedB->getAmount()->willReturn(new Amount('-50'));
        $this->shouldNotBeSuccess();
    }

    function it_interprets_zero_amonut_as_success($matchedA, $matchedB)
    {
        $matchedA->getAmount()->willReturn(new Amount('0'));
        $matchedB->getAmount()->willReturn(new Amount('0'));
        $this->shouldBeSuccess();
    }
}
