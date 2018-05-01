<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Matcher\DateComparator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DateComparatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(DateComparator::CLASS);
    }

    function it_interprets_same_day_as_equal()
    {
        $this->equals(new \DateTime('20180430'), new \DateTime('20180430'))->shouldReturn(true);
    }

    function it_evaluates_same_as_max_day_as_equal()
    {
        $this->beConstructedWith(1);
        $this->equals(new \DateTime('20180430'), new \DateTime('20180501'))->shouldReturn(true);
    }

    function it_evaluates_more_than_max_day_as_not_equal()
    {
        $this->beConstructedWith(1);
        $this->equals(new \DateTime('20180430'), new \DateTime('20180502'))->shouldReturn(false);
    }

    function it_evaluates_negative_same_as_max_day_as_equal()
    {
        $this->beConstructedWith(1);
        $this->equals(new \DateTime('20180430'), new \DateTime('20180429'))->shouldReturn(true);
    }

    function it_evaluates_negative_more_than_max_day_as_not_equal()
    {
        $this->beConstructedWith(1);
        $this->equals(new \DateTime('20180430'), new \DateTime('20180428'))->shouldReturn(false);
    }

    function it_can_evaluate_immutable_dates()
    {
        $this->beConstructedWith(6);
        $this->equals(new \DateTimeImmutable('20180629'), new \DateTimeImmutable('20180729'))->shouldReturn(false);
    }
}
