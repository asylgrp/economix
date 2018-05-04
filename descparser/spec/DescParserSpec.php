<?php

declare(strict_types = 1);

namespace descparser\spec\asylgrp\descparser;

use asylgrp\descparser\DescParser;
use asylgrp\descparser\Grammar;
use asylgrp\descparser\Result;
use asylgrp\descparser\Tree\Node;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DescParserSpec extends ObjectBehavior
{
    const CURRENT_YEAR = '2018';

    function let(Grammar $grammar)
    {
        $this->beConstructedWith(self::CURRENT_YEAR, $grammar);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DescParser::CLASS);
    }

    function it_constructs_result_objects($grammar, Node $node1, Node $node2)
    {
        $grammar->parse('foobar')->willReturn([$node1, $node2]);
        $this->parse('foobar')->shouldBeLike(
            new Result(self::CURRENT_YEAR, $node1->getWrappedObject(), $node2->getWrappedObject())
        );
    }
}
