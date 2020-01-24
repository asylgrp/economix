<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Matchable;

use asylgrp\matchmaker\Matchable\MatchableGroup;
use asylgrp\matchmaker\Matchable\MatchableInterface;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MatchableGroupSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MatchableGroup::CLASS);
    }

    function it_implements_matchable_interface()
    {
        $this->shouldHaveType(MatchableInterface::CLASS);
    }

    function it_contains_empty_id()
    {
        $this->getId()->shouldReturn('');
    }

    function it_contains_empty_description()
    {
        $this->getDescription()->shouldReturn('');
    }

    function it_contains_no_relations()
    {
        $this->getRelatedIds()->shouldReturn([]);
    }

    function it_contains_matchables(MatchableInterface $matchableA, MatchableInterface $matchableB)
    {
        $this->beConstructedWith($matchableA, $matchableB);
        $this->getMatchables()->shouldIterateAs([$matchableA, $matchableB]);
    }

    function it_returns_amount_summary(MatchableInterface $matchableA, MatchableInterface $matchableB)
    {
        $matchableA->getAmount()->willReturn(Money::SEK('100'));
        $matchableB->getAmount()->willReturn(Money::SEK('50'));
        $this->beConstructedWith($matchableA, $matchableB);
        $this->getAmount()->shouldBeLike(Money::SEK('150'));
    }

    function it_cannot_get_amount_from_empty_group()
    {
        $this->shouldThrow(\LogicException::CLASS)->during('getAmount');
    }

    function it_returns_date_in_the_middle(
        MatchableInterface $matchableA,
        MatchableInterface $matchableB,
        MatchableInterface $matchableC
    ) {
        $matchableA->getDate()->willReturn(new \DateTimeImmutable('20180101'));
        $matchableB->getDate()->willReturn(new \DateTimeImmutable('20180104'));
        $matchableC->getDate()->willReturn(new \DateTimeImmutable('20180105'));
        $this->beConstructedWith($matchableA, $matchableB, $matchableC);
        $this->getDate()->shouldBeLike(new \DateTimeImmutable('20180103'));
    }

    function it_cannot_get_date_from_empty_group()
    {
        $this->shouldThrow(\LogicException::CLASS)->during('getDate');
    }
}
