<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Matchable;

use asylgrp\matchmaker\Matchable\Matchable;
use asylgrp\matchmaker\Matchable\MatchableInterface;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MatchableSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('', '', new \DateTimeImmutable, Money::SEK('0'));
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
        $this->beConstructedWith('foobar', '', new \DateTimeImmutable, Money::SEK('0'));
        $this->getId()->shouldReturn('foobar');
    }

    function it_contains_a_description()
    {
        $this->beConstructedWith('', 'desc', new \DateTimeImmutable, Money::SEK('0'));
        $this->getDescription()->shouldReturn('desc');
    }

    function it_contains_a_date()
    {
        $this->beConstructedWith('', '', $date = new \DateTimeImmutable, Money::SEK('0'));
        $this->getDate()->shouldReturn($date);
    }

    function it_contains_an_amount()
    {
        $this->beConstructedWith('', '', new \DateTimeImmutable, $amount = Money::SEK('0'));
        $this->getAmount()->shouldReturn($amount);
    }

    function it_contains_related_ids()
    {
        $this->beConstructedWith('', '', new \DateTimeImmutable, Money::SEK('0'), ['A', 'B']);
        $this->getRelatedIds()->shouldReturn(['A', 'B']);
    }

    function it_contains_matchables()
    {
        $this->getMatchables()->shouldBeArray();
    }
}
