<?php

declare(strict_types = 1);

namespace matchmaker\spec\asylgrp\matchmaker\Matcher;

use asylgrp\matchmaker\Matcher\AmountComparator;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AmountComparatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AmountComparator::CLASS);
    }

    function it_interprets_inversed_amount_as_equal()
    {
        $this->equals(Money::SEK('100'), Money::SEK('-100'))->shouldReturn(true);
    }

    function it_evaluates_same_as_max_deviation_as_equal()
    {
        $this->beConstructedWith(0.05);
        $this->equals(Money::SEK('100'), Money::SEK('-105'))->shouldReturn(true);
    }

    function it_evaluates_same_as_max_deviation_as_equal_when_first_value_is_negative()
    {
        $this->beConstructedWith(0.05);
        $this->equals(Money::SEK('-100'), Money::SEK('105'))->shouldReturn(true);
    }

    function it_evaluates_non_inversed_as_not_equal()
    {
        $this->beConstructedWith(0.05);
        $this->equals(Money::SEK('100'), Money::SEK('105'))->shouldReturn(false);
    }

    function it_evaluates_double_inversed_as_not_equal()
    {
        $this->beConstructedWith(0.05);
        $this->equals(Money::SEK('-100'), Money::SEK('-105'))->shouldReturn(false);
    }

    function it_evaluates_more_than_max_deviation_as_not_equal()
    {
        $this->beConstructedWith(0.05);
        $this->equals(Money::SEK('100'), Money::SEK('-106'))->shouldReturn(false);
    }

    function it_evaluates_negative_same_as_max_deviation_as_equal()
    {
        $this->beConstructedWith(0.05);
        $this->equals(Money::SEK('105'), Money::SEK('-100'))->shouldReturn(true);
    }

    function it_evaluates_negative_more_than_max_deviation_as_not_equal()
    {
        $this->beConstructedWith(0.05);
        $this->equals(Money::SEK('106'), Money::SEK('-100'))->shouldReturn(false);
    }

    function it_can_handle_division_by_zero()
    {
        $this->equals(Money::SEK('0'), Money::SEK('0'))->shouldReturn(false);
    }
}
