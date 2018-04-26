<?php

declare(strict_types = 1);

namespace descparser\spec\asylgrp\descparser;

use asylgrp\descparser\Grammar;
use asylgrp\descparser\Tree\DateNode;
use asylgrp\descparser\Tree\NameNode;
use asylgrp\descparser\Tree\RelationNode;
use asylgrp\descparser\Tree\TagNode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class GrammarSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Grammar::CLASS);
    }

    function it_can_parse_empty_string()
    {
        $this->parse('')->shouldReturn([]);
    }

    function it_can_parser_simple_tags()
    {
        $this->parse('#tag')->shouldBeLike([new TagNode('tag')]);
    }

    function it_supports_basic_chars()
    {
        $this->parse('#aA09åÅäÄöÖ\'"_-')->shouldBeLike([new TagNode('aA09åÅäÄöÖ\'"_-')]);
    }

    function it_supports_qouted_strings_with_spaces()
    {
        $this->parse('#"a b"')->shouldBeLike([new TagNode('a b')]);
    }

    function it_can_parser_simple_names()
    {
        $this->parse('@name')->shouldBeLike([new NameNode('name')]);
    }

    function it_can_parser_simple_relation()
    {
        $this->parse('rel:relation')->shouldBeLike([new RelationNode('relation')]);
    }

    function it_can_parse_simple_date()
    {
        $this->parse('20170426')->shouldBeLike([new DateNode('20170426')]);
    }

    function it_can_parse_complex_date()
    {
        $this->parse('26/4-17')->shouldBeLike([new DateNode('26/4-17')]);
    }

    function it_ignores_unknown_text()
    {
        $this->parse('this is ignored @name and this too...')->shouldBeLike([new NameNode('name')]);
    }

    function it_ignores_uncompleted_nodes()
    {
        $this->parse('@ # rel:')->shouldBeLike([]);
    }

    function it_can_parse_multiple_nodes()
    {
        $this->parse('Some text 1234 @foo #bar ignored #baz rel:77')->shouldBeLike([
            new DateNode('1234'),
            new NameNode('foo'),
            new TagNode('bar'),
            new TagNode('baz'),
            new RelationNode('77'),
        ]);
    }
}
