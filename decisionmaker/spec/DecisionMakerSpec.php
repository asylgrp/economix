<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker;

use asylgrp\decisionmaker\DecisionMaker;
use asylgrp\decisionmaker\Allocator\AllocatorInterface;
use asylgrp\decisionmaker\PayoutRequestCollection;
use asylgrp\decisionmaker\Decision;
use asylgrp\decisionmaker\PayoutRequestHasher;
use byrokrat\amount\Currency\SEK;
use Lcobucci\Clock\Clock;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DecisionMakerSpec extends ObjectBehavior
{
    function let(
        AllocatorInterface $allocator,
        Clock $clock,
        PayoutRequestHasher $payoutRequestHasher
    ) {
        $this->beConstructedWith($allocator, $clock, $payoutRequestHasher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DecisionMaker::CLASS);
    }

    function it_creates_decisions(
        $allocator,
        $clock,
        $payoutRequestHasher,
        PayoutRequestCollection $collection
    ) {
        $funds = new SEK('1000');
        $payouts = [];
        $date = new \DateTimeImmutable;

        $allocator->allocate($funds, new PayoutRequestCollection($payouts))->willReturn($collection);
        $payoutRequestHasher->hash($collection)->willReturn('hash');
        $clock->now()->willReturn($date);

        $this->createDecision($funds, $payouts, 'foo')->shouldBeLike(
            new Decision('hash', 'foo', $date, $funds, $collection->getWrappedObject())
        );
    }
}
