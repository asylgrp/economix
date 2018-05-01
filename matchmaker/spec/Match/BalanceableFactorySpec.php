<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Match;

use asylgrp\matchmaker\Match\BalanceableFactory;
use asylgrp\matchmaker\Match\MatchFactoryInterface;
use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Match\BalanceableMatch;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class BalanceableFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(BalanceableFactory::CLASS);
    }

    function it_implements_match_factory_interface()
    {
        $this->shouldHaveType(MatchFactoryInterface::CLASS);
    }

    function it_creates_balanceable_matches(MatchableInterface $matchableA, MatchableInterface $matchableB)
    {
        $this->createMatch($matchableA, $matchableB)->shouldBeLike(
            new BalanceableMatch($matchableA->getWrappedObject(), $matchableB->getWrappedObject())
        );
    }
}
