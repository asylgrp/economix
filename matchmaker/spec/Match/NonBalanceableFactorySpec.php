<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Match;

use asylgrp\matchmaker\Match\NonBalanceableFactory;
use asylgrp\matchmaker\Match\MatchFactoryInterface;
use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Match\NonBalanceableMatch;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NonBalanceableFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NonBalanceableFactory::CLASS);
    }

    function it_implements_match_factory_interface()
    {
        $this->shouldHaveType(MatchFactoryInterface::CLASS);
    }

    function it_creates_balanceable_matches(MatchableInterface $matchableA, MatchableInterface $matchableB)
    {
        $this->createMatch($matchableA, $matchableB)->shouldBeLike(
            new NonBalanceableMatch($matchableA->getWrappedObject(), $matchableB->getWrappedObject())
        );
    }
}
