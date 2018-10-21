<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Filter;

use asylgrp\matchmaker\Filter\LogicalOrFilter;
use asylgrp\matchmaker\Filter\FilterInterface;
use asylgrp\matchmaker\Filter\ResultInterface;
use asylgrp\matchmaker\Filter\Failure;
use asylgrp\matchmaker\Match\MatchCollectionInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LogicalOrFilterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(LogicalOrFilter::CLASS);
    }

    function it_is_a_filter()
    {
        $this->shouldHaveType(FilterInterface::CLASS);
    }

    function it_fails_if_wrapped_filters_fail(
        FilterInterface $filterA,
        FilterInterface $filterB,
        ResultInterface $result,
        MatchCollectionInterface $matches
    ) {
        $this->beConstructedWith($filterA, $filterB);

        $result->isSuccess()->willReturn(false);
        $result->getMessage()->willReturn('FOO');

        $filterA->evaluate($matches)->willReturn($result);
        $filterB->evaluate($matches)->willReturn($result);

        $this->evaluate($matches)->shouldBeLike(new Failure("FOO\nFOO"));
    }

    function it_pass_through_first_successful_result(
        FilterInterface $filterA,
        FilterInterface $filterB,
        ResultInterface $resultA,
        ResultInterface $resultB,
        MatchCollectionInterface $matches
    ) {
        $this->beConstructedWith($filterA, $filterB);

        $resultA->isSuccess()->willReturn(false);
        $resultA->getMessage()->willReturn('');
        $resultB->isSuccess()->willReturn(true);

        $filterA->evaluate($matches)->willReturn($resultA);
        $filterB->evaluate($matches)->willReturn($resultB);

        $this->evaluate($matches)->shouldReturn($resultB);
    }
}
