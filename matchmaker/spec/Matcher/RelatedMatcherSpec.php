<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Matcher\RelatedMatcher;
use asylgrp\matchmaker\Matcher\MatcherInterface;
use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Match\BalanceableMatch;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RelatedMatcherSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RelatedMatcher::CLASS);
    }

    function it_implements_matcher_interface()
    {
        $this->shouldHaveType(MatcherInterface::CLASS);
    }

    function it_returns_null_on_no_relation(MatchableInterface $itemA, MatchableInterface $itemB)
    {
        $itemA->getRelatedIds()->willReturn(['foo', 'bar']);
        $itemB->getId()->willReturn('baz');
        $this->match($itemA, [$itemB])->shouldReturn(null);
    }

    function it_finds_a_related_item(MatchableInterface $itemA, MatchableInterface $itemB)
    {
        $itemA->getRelatedIds()->willReturn(['foo']);
        $itemB->getId()->willReturn('foo');
        $itemB->getRelatedIds()->willReturn([]);

        $this->match($itemA, [$itemB])->shouldBeLike(
            new BalanceableMatch($itemA->getWrappedObject(), $itemB->getWrappedObject())
        );
    }

    function it_recursively_finds_relations(
        MatchableInterface $itemA,
        MatchableInterface $itemB,
        MatchableInterface $itemC
    ) {
        $itemA->getRelatedIds()->willReturn(['B']);
        $itemB->getId()->willReturn('B');
        $itemB->getRelatedIds()->willReturn(['C']);
        $itemC->getId()->willReturn('C');
        $itemC->getRelatedIds()->willReturn([]);

        $this->match($itemA, [$itemB, $itemC])->shouldBeLike(
            new BalanceableMatch($itemA->getWrappedObject(), $itemB->getWrappedObject(), $itemC->getWrappedObject())
        );
    }

    function it_returns_only_unique_values(
        MatchableInterface $itemA,
        MatchableInterface $itemB,
        MatchableInterface $itemC
    ) {
        $itemA->getRelatedIds()->willReturn(['B', 'C']);
        $itemB->getId()->willReturn('B');
        $itemB->getRelatedIds()->willReturn(['C']);
        $itemC->getId()->willReturn('C');
        $itemC->getRelatedIds()->willReturn([]);

        $this->match($itemA, [$itemB, $itemC])->shouldBeLike(
            new BalanceableMatch($itemA->getWrappedObject(), $itemB->getWrappedObject(), $itemC->getWrappedObject())
        );
    }

    function it_can_handle_circular_references(
        MatchableInterface $itemA,
        MatchableInterface $itemB,
        MatchableInterface $itemC
    ) {
        $itemA->getRelatedIds()->willReturn(['B']);
        $itemB->getId()->willReturn('B');
        $itemB->getRelatedIds()->willReturn(['C']);
        $itemC->getId()->willReturn('C');
        $itemC->getRelatedIds()->willReturn(['B']);

        $this->match($itemA, [$itemB, $itemC])->shouldBeLike(
            new BalanceableMatch($itemA->getWrappedObject(), $itemB->getWrappedObject(), $itemC->getWrappedObject())
        );
    }
}
