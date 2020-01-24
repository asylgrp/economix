<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Allocator;

use asylgrp\decisionmaker\Allocator\NullAllocator;
use asylgrp\decisionmaker\Allocator\AllocatorInterface;
use asylgrp\decisionmaker\PayoutRequestCollection;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class NullAllocatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NullAllocator::CLASS);
    }

    function it_implements_allocator_interface()
    {
        $this->shouldHaveType(AllocatorInterface::CLASS);
    }

    function it_simply_returns_collection(PayoutRequestCollection $collection)
    {
        $this->allocate(Money::SEK('0'), $collection)->shouldReturn($collection);
    }
}
