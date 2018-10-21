<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Filter;

use asylgrp\matchmaker\Filter\Failure;
use asylgrp\matchmaker\Filter\ResultInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FailureSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Failure::CLASS);
    }

    function it_is_a_result()
    {
        $this->shouldHaveType(ResultInterface::CLASS);
    }

    function it_is_successful()
    {
        $this->shouldNotBeSuccess();
    }

    function it_contains_a_message()
    {
        $this->beConstructedWith('foobar');
        $this->getMessage()->shouldReturn('foobar');
    }
}
