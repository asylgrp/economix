<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Matcher\SingleMatcher;
use asylgrp\matchmaker\Matcher\MatcherInterface;
use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Match\NonBalanceableMatch;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SingleMatcherSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SingleMatcher::CLASS);
    }

    function it_implements_matcher_interface()
    {
        $this->shouldHaveType(MatcherInterface::CLASS);
    }

    function it_can_find_match(MatchableInterface $itemA, MatchableInterface $itemB)
    {
        $this->match($itemA, [$itemB])->shouldBeLike(
            new NonBalanceableMatch($itemA->getWrappedObject())
        );
    }
}
