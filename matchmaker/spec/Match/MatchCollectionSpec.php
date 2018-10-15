<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Match;

use asylgrp\matchmaker\Match\MatchCollection;
use asylgrp\matchmaker\Match\MatchCollectionInterface;
use asylgrp\matchmaker\Match\MatchInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MatchCollectionSpec extends ObjectBehavior
{
    function let(MatchInterface $matchA, MatchInterface $matchB)
    {
        $this->beConstructedWith($matchA, $matchB);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MatchCollection::CLASS);
    }

    function it_implements_interface()
    {
        $this->shouldHaveType(MatchCollectionInterface::CLASS);
    }

    function it_can_return_matches($matchA, $matchB)
    {
        $this->getMatches()->shouldIterateAs([$matchA, $matchB]);
    }

    function it_is_iterable($matchA, $matchB)
    {
        $this->getIterator()->shouldIterateAs([$matchA, $matchB]);
    }

    function it_can_return_successful_matches($matchA, $matchB)
    {
        $matchA->isSuccess()->willReturn(true);
        $matchB->isSuccess()->willReturn(false);
        $this->getSuccessful()->shouldIterateAs([$matchA]);
    }

    function it_can_return_failed_matches($matchA, $matchB)
    {
        $matchA->isSuccess()->willReturn(true);
        $matchB->isSuccess()->willReturn(false);
        $this->getFailures()->shouldIterateAs([$matchB]);
    }

    function it_can_return_balanceable_matches($matchA, $matchB)
    {
        $matchA->isBalanceable()->willReturn(true);
        $matchB->isBalanceable()->willReturn(false);
        $this->getBalanceables()->shouldIterateAs([$matchA]);
    }
}
