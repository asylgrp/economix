<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Match;

use asylgrp\matchmaker\Match\MatchCollection;
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

    function it_can_return_matches($matchA, $matchB)
    {
        $this->getMatches()->shouldIterateAs([$matchA, $matchB]);
    }
}
