<?php

declare(strict_types = 1);

namespace descparser\spec\asylgrp\descparser;

use asylgrp\descparser\Result;
use asylgrp\descparser\Tree\DateNode;
use asylgrp\descparser\Tree\NameNode;
use asylgrp\descparser\Tree\RelationNode;
use asylgrp\descparser\Tree\TagNode;
use PhpSpec\ObjectBehavior;
use PhpSpec\Exception\Example\FailureException;
use Prophecy\Argument;

class ResultSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith('');
        $this->shouldHaveType(Result::CLASS);
    }

    function it_can_find_tags(DateNode $date, TagNode $tag1, TagNode $tag2)
    {
        $tag1->getValue()->willReturn('foo');
        $tag2->getValue()->willReturn('bar');
        $this->beConstructedWith('', $date, $tag1, $tag2);
        $this->getTags()->shouldIterateAs(['foo', 'bar']);
    }

    function it_can_find_names(DateNode $date, TagNode $tag, NameNode $name)
    {
        $name->getValue()->willReturn('foo');
        $this->beConstructedWith('', $date, $tag, $name);
        $this->getNames()->shouldIterateAs(['foo']);
    }

    function it_can_find_relations(DateNode $date, RelationNode $rel1, RelationNode $rel2)
    {
        $rel1->getValue()->willReturn('foo');
        $rel2->getValue()->willReturn('bar');
        $this->beConstructedWith('', $date, $rel1, $rel2);
        $this->getRelations()->shouldIterateAs(['foo', 'bar']);
    }

    function it_can_parse_dates_based_on_current_year(DateNode $date)
    {
        $date->getValue()->willReturn('26/4');
        $this->beConstructedWith('2018', $date);
        $this->getDate()->shouldReturnDate('20180426');
    }

    function it_can_parse_dates_based_on_current_year_with_leading_ceros(DateNode $date)
    {
        $date->getValue()->willReturn('01/04');
        $this->beConstructedWith('2018', $date);
        $this->getDate()->shouldReturnDate('20180401');
    }

    function it_can_parse_dates_without_current_year(DateNode $date)
    {
        $date->getValue()->willReturn('26/4-17');
        $this->beConstructedWith('', $date);
        $this->getDate()->shouldReturnDate('20170426');
    }

    function it_can_parse_digit_only_dates(DateNode $date)
    {
        $date->getValue()->willReturn('20170426');
        $this->beConstructedWith('', $date);
        $this->getDate()->shouldReturnDate('20170426');
    }

    function it_can_parse_dashed_dates(DateNode $date)
    {
        $date->getValue()->willReturn('2017-04-26');
        $this->beConstructedWith('', $date);
        $this->getDate()->shouldReturnDate('20170426');
    }

    function it_returnes_null_on_no_match(DateNode $date)
    {
        $date->getValue()->willReturn('this-is-not-a-valid-date');
        $this->beConstructedWith('', $date);
        $this->getDate()->shouldReturn(null);
    }

    function it_returnes_null_on_no_date_node()
    {
        $this->beConstructedWith('');
        $this->getDate()->shouldReturn(null);
    }

    function it_throws_exception_on_multiple_valid_dates(DateNode $date1, DateNode $date2)
    {
        $date1->getValue()->willReturn('20180520');
        $date2->getValue()->willReturn('20180520');
        $this->beConstructedWith('', $date1, $date2);
        $this->shouldThrow(\RuntimeException::CLASS)->during('getDate');
    }

    function it_can_parse_dates_that_are_not_firs_in_line(TagNode $node, DateNode $date)
    {
        $date->getValue()->willReturn('20170426');
        $this->beConstructedWith('', $node, $date);
        $this->getDate()->shouldReturnDate('20170426');
    }

    public function getMatchers(): array
    {
        return [
            'returnDate' => function (\DateTimeImmutable $date, string $expected) {
                if ($date->format('Ymd') != $expected) {
                    throw new FailureException("Found date {$date->format('Ymd')}, expected $expected");
                }
                return true;
            }
        ];
    }
}
