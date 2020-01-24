<?php

declare(strict_types = 1);

namespace decisionmaker\spec\asylgrp\decisionmaker\Allocator;

use asylgrp\decisionmaker\Allocator\NullAllocator;
use asylgrp\decisionmaker\Allocator\LazyAllocator;
use asylgrp\decisionmaker\Allocator\DoubleAllocator;
use asylgrp\decisionmaker\Allocator\StaticAllocator;
use asylgrp\decisionmaker\Allocator\AllocatorBuilder;
use asylgrp\decisionmaker\Granter\FixedGranter;
use asylgrp\decisionmaker\Granter\RatioGranter;
use asylgrp\decisionmaker\Granter\FixedGranterFactory;
use asylgrp\decisionmaker\Granter\RatioGranterFactory;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AllocatorBuilderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AllocatorBuilder::CLASS);
    }

    function it_defaults_to_null_allocator()
    {
        $this->getAllocator()->shouldBeLike(new NullAllocator);
    }

    function it_can_add_lazy_fixed_allocator()
    {
        $this->addLazyFixed(Money::SEK('1000'));
        $this->getAllocator()->shouldBeLike(new DoubleAllocator(
            new NullAllocator,
            new LazyAllocator(new FixedGranterFactory(Money::SEK('1000')))
        ));
    }

    function it_can_add_lazy_ratio_allocator()
    {
        $this->addLazyRatio();
        $this->getAllocator()->shouldBeLike(new DoubleAllocator(
            new NullAllocator,
            new LazyAllocator(new RatioGranterFactory)
        ));
    }

    function it_can_add_static_fixed_allocator()
    {
        $this->addStaticFixed(Money::SEK('100'));
        $this->getAllocator()->shouldBeLike(new DoubleAllocator(
            new NullAllocator,
            new StaticAllocator(new FixedGranter(Money::SEK('100')))
        ));
    }

    function it_can_add_static_ratio_allocator()
    {
        $this->addStaticRatio(0.5);
        $this->getAllocator()->shouldBeLike(new DoubleAllocator(
            new NullAllocator,
            new StaticAllocator(new RatioGranter(0.5))
        ));
    }
}
