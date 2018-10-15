<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Matchable;

use asylgrp\matchmaker\Matchable\Matchable;
use asylgrp\matchmaker\Matchable\MatchableInterface;
use byrokrat\amount\Amount;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MatchableSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('', '', new \DateTimeImmutable, new Amount('0'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Matchable::CLASS);
    }

    function it_implements_matchable_interface()
    {
        $this->shouldHaveType(MatchableInterface::CLASS);
    }

    function it_contains_an_id()
    {
        $this->beConstructedWith('foobar', '', new \DateTimeImmutable, new Amount('0'));
        $this->getId()->shouldReturn('foobar');
    }

    function it_contains_a_description()
    {
        $this->beConstructedWith('', 'desc', new \DateTimeImmutable, new Amount('0'));
        $this->getDescription()->shouldReturn('desc');
    }

    function it_contains_a_date()
    {
        $this->beConstructedWith('', '', $date = new \DateTimeImmutable, new Amount('0'));
        $this->getDate()->shouldReturn($date);
    }

    function it_contains_an_amount()
    {
        $this->beConstructedWith('', '', new \DateTimeImmutable, $amount = new Amount('0'));
        $this->getAmount()->shouldReturn($amount);
    }

    function it_contains_related_ids()
    {
        $this->beConstructedWith('', '', new \DateTimeImmutable, new Amount('0'), ['A', 'B']);
        $this->getRelatedIds()->shouldReturn(['A', 'B']);
    }

    function it_contains_matchables()
    {
        $this->getMatchables()->shouldBeArray();
    }
}
