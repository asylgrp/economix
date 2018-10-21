<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Filter;

use asylgrp\matchmaker\Filter\Success;
use asylgrp\matchmaker\Filter\ResultInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SuccessSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Success::CLASS);
    }

    function it_is_a_result()
    {
        $this->shouldHaveType(ResultInterface::CLASS);
    }

    function it_is_successful()
    {
        $this->shouldBeSuccess();
    }

    function it_contains_a_message()
    {
        $this->beConstructedWith('foobar');
        $this->getMessage()->shouldReturn('foobar');
    }
}
