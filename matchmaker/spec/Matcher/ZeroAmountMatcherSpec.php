<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Matcher\ZeroAmountMatcher;
use asylgrp\matchmaker\Matcher\MatcherInterface;
use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Match\NonBalanceableMatch;
use byrokrat\amount\Amount;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ZeroAmountMatcherSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ZeroAmountMatcher::CLASS);
    }

    function it_implements_matcher_interface()
    {
        $this->shouldHaveType(MatcherInterface::CLASS);
    }

    function it_matches_zero_amount(MatchableInterface $matchable)
    {
        $matchable->getAmount()->willReturn(new Amount('0'));
        $this->match($matchable, [])->shouldBeLike(new NonBalanceableMatch($matchable->getWrappedObject()));
    }

    function it_skips_non_zero(MatchableInterface $matchable)
    {
        $matchable->getAmount()->willReturn(new Amount('1'));
        $this->match($matchable, [])->shouldReturn(null);
    }
}
