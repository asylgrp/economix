<?php

declare(strict_types = 1);

namespace descparser\spec\asylgrp\descparser\Tree;

use asylgrp\descparser\Tree\RelationNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RelationNodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('');
        $this->shouldHaveType(RelationNode::CLASS);
    }

    function it_contains_value()
    {
        $this->beConstructedWith('value');
        $this->getValue()->shouldReturn('value');
    }
}
