<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker;

use asylgrp\matchmaker\MatchMaker;
use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Match\MatchInterface;
use asylgrp\matchmaker\Match\MatchCollection;
use asylgrp\matchmaker\Matcher\MatcherInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MatchMakerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MatchMaker::CLASS);
    }

    function it_defaults_to_no_matches(MatchableInterface $matchable)
    {
        $this->match($matchable)->shouldBeLike(new MatchCollection);
    }

    function it_removes_matched_from_queue(
        MatchableInterface $matchableA,
        MatchableInterface $matchableB,
        MatcherInterface $matcher,
        MatchInterface $matchA,
        MatchInterface $matchB
    ) {
        $this->beConstructedWith($matcher);
        $matchA->getMatched()->willReturn([]);
        $matchB->getMatched()->willReturn([]);
        $matcher->match($matchableA, [$matchableB])->willReturn($matchA)->shouldBeCalled();
        $matcher->match($matchableB, [])->willReturn($matchB)->shouldBeCalled();
        $returnedMatchCollection = $this->match($matchableA, $matchableB);
        $returnedMatchCollection->shouldContainMatch($matchA);
        $returnedMatchCollection->shouldContainMatch($matchB);
    }

    function it_tries_non_matched_matchables_with_next_matcher(
        MatchableInterface $matchable,
        MatcherInterface $matcherA,
        MatcherInterface $matcherB,
        MatchInterface $match
    ) {
        $this->beConstructedWith($matcherA, $matcherB);
        $matcherA->match($matchable, [])->willReturn(null)->shouldBeCalled();
        $matcherB->match($matchable, [])->willReturn(null)->shouldBeCalled();
        $this->match($matchable);
    }

    function it_can_match_with_previously_unmatched(
        MatchableInterface $matchableA,
        MatchableInterface $matchableB,
        MatcherInterface $matcher
    ) {
        $this->beConstructedWith($matcher);
        $matcher->match($matchableA, [$matchableB])->willReturn(null)->shouldBeCalled();
        $matcher->match($matchableB, [$matchableA])->willReturn(null)->shouldBeCalled();
        $this->match($matchableA, $matchableB);
    }

    function it_preserves_the_order_of_previously_unmatched(
        MatchableInterface $matchableA,
        MatchableInterface $matchableB,
        MatchableInterface $matchableC,
        MatcherInterface $matcher
    ) {
        $this->beConstructedWith($matcher);
        $matcher->match($matchableA, [$matchableB, $matchableC])->willReturn(null)->shouldBeCalled();
        $matcher->match($matchableB, [$matchableA, $matchableC])->willReturn(null)->shouldBeCalled();
        $matcher->match($matchableC, [$matchableA, $matchableB])->willReturn(null)->shouldBeCalled();
        $this->match($matchableA, $matchableB, $matchableC);
    }

    function it_removes_matched_item_from_queue(
        MatchableInterface $matchableA,
        MatchableInterface $matchableB,
        MatchableInterface $matchableC,
        MatcherInterface $matcher,
        MatchInterface $match
    ) {
        $this->beConstructedWith($matcher);
        $match->getMatched()->willReturn([$matchableA, $matchableB]);
        $matcher->match($matchableA, [$matchableB, $matchableC])->willReturn($match)->shouldBeCalled();
        $matcher->match($matchableC, [])->willReturn(null)->shouldBeCalled();
        $this->match($matchableA, $matchableB, $matchableC);
    }

    function it_removes_matched_item_from_previously_unmatched(
        MatchableInterface $matchableA,
        MatchableInterface $matchableB,
        MatchableInterface $matchableC,
        MatcherInterface $matcher,
        MatchInterface $match
    ) {
        $this->beConstructedWith($matcher);
        $match->getMatched()->willReturn([$matchableA, $matchableB]);
        $matcher->match($matchableA, [$matchableB, $matchableC])->willReturn(null)->shouldBeCalled();
        $matcher->match($matchableB, [$matchableA, $matchableC])->willReturn($match)->shouldBeCalled();
        $matcher->match($matchableC, [])->willReturn(null)->shouldBeCalled();
        $this->match($matchableA, $matchableB, $matchableC);
    }

    public function getMatchers(): array
    {
        return [
            'containMatch' => function ($foundMatches, $expectedMatch) {
                foreach ($foundMatches->getMatches() as $match) {
                    if ($match == $expectedMatch) {
                        return true;
                    }
                }
                return false;
            }
        ];
    }
}
