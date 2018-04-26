<?php

declare(strict_types = 1);

namespace descparser\spec\asylgrp\descparser\Tree;

use asylgrp\descparser\Tree\DateNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DateNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('');
        $this->shouldHaveType(DateNode::CLASS);
    }

    function it_contains_value()
    {
        $this->beConstructedWith('value');
        $this->getValue()->shouldReturn('value');
    }
}
