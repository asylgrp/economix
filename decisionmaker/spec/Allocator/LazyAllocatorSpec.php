<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Allocator;

use asylgrp\decisionmaker\Allocator\LazyAllocator;
use asylgrp\decisionmaker\Allocator\AllocatorInterface;
use asylgrp\decisionmaker\Granter\GranterInterface;
use asylgrp\decisionmaker\Granter\GranterFactoryInterface;
use asylgrp\decisionmaker\PayoutRequest;
use asylgrp\decisionmaker\PayoutRequestCollection;
use asylgrp\decisionmaker\Grant\GrantInterface;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LazyAllocatorSpec extends ObjectBehavior
{
    function let(GranterFactoryInterface $granterFactory)
    {
        $this->beConstructedWith($granterFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LazyAllocator::CLASS);
    }

    function it_implements_allocator_interface()
    {
        $this->shouldHaveType(AllocatorInterface::CLASS);
    }

    function it_allocates_using_a_lazyly_created_granter(
        $granterFactory,
        GranterInterface $granter,
        PayoutRequestCollection $collection,
        PayoutRequest $oldPayout,
        PayoutRequest $newPayout,
        GrantInterface $oldGrant,
        GrantInterface $newGrant
    ) {
        $amount = Money::SEK('0');
        $granterFactory->createGranter($amount, $collection)->willReturn($granter);

        $collection->getIterator()->willReturn(new \ArrayIterator([$oldPayout->getWrappedObject()]));
        $oldPayout->getGrant()->willReturn($oldGrant);
        $granter->grant($oldGrant)->willReturn($newGrant);
        $oldPayout->withGrant($newGrant)->willReturn($newPayout);

        $this->allocate(Money::SEK('0'), $collection)->shouldBeLike(
            new PayoutRequestCollection([$newPayout->getWrappedObject()])
        );
    }
}
