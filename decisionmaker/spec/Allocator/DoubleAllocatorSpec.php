<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Allocator;

use asylgrp\decisionmaker\Allocator\DoubleAllocator;
use asylgrp\decisionmaker\Allocator\AllocatorInterface;
use asylgrp\decisionmaker\PayoutRequestCollection;
use byrokrat\amount\Amount;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DoubleAllocatorSpec extends ObjectBehavior
{
    function let(AllocatorInterface $allocA, AllocatorInterface $allocB)
    {
        $this->beConstructedWith($allocA, $allocB);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DoubleAllocator::CLASS);
    }

    function it_implements_allocator_interface()
    {
        $this->shouldHaveType(AllocatorInterface::CLASS);
    }

    function it_passes_collection_and_amount(
        $allocA,
        $allocB,
        PayoutRequestCollection $first,
        PayoutRequestCollection $second,
        PayoutRequestCollection $third
    ) {
        $allocA->allocate(new Amount('100'), $first)->willReturn($second);

        $second->getGrantedAmount()->willReturn(new Amount('60'));

        $allocB->allocate(new Amount('40'), $second)->willReturn($third);

        $this->allocate(new Amount('100'), $first)->shouldReturn($third);
    }
}
