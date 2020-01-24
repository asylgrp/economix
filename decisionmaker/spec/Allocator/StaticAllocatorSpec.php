<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Allocator;

use asylgrp\decisionmaker\Allocator\StaticAllocator;
use asylgrp\decisionmaker\Allocator\AllocatorInterface;
use asylgrp\decisionmaker\Granter\GranterInterface;
use asylgrp\decisionmaker\PayoutRequest;
use asylgrp\decisionmaker\PayoutRequestCollection;
use asylgrp\decisionmaker\Grant\GrantInterface;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class StaticAllocatorSpec extends ObjectBehavior
{
    function let(GranterInterface $granter)
    {
        $this->beConstructedWith($granter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(StaticAllocator::CLASS);
    }

    function it_implements_allocator_interface()
    {
        $this->shouldHaveType(AllocatorInterface::CLASS);
    }

    function it_allocates(
        $granter,
        PayoutRequestCollection $collection,
        PayoutRequest $oldPayout,
        PayoutRequest $newPayout,
        GrantInterface $oldGrant,
        GrantInterface $newGrant
    ) {
        $collection->getIterator()->willReturn(new \ArrayIterator([$oldPayout->getWrappedObject()]));
        $oldPayout->getGrant()->willReturn($oldGrant);
        $granter->grant($oldGrant)->willReturn($newGrant);
        $oldPayout->withGrant($newGrant)->willReturn($newPayout);

        $this->allocate(Money::SEK('0'), $collection)->shouldBeLike(
            new PayoutRequestCollection([$newPayout->getWrappedObject()])
        );
    }
}
