<?php

declare(strict_types = 1);

namespace receiptanalyzer\spec\asylgrp\receiptanalyzer;

use asylgrp\receiptanalyzer\Cell;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CellSpec extends ObjectBehavior
{
    const VERNUM = '99';
    const TAG_A = 'A';
    const TAG_B = 'B';
    const AMOUNT = '666';

    function let()
    {
        $this->beConstructedWith(
            self::VERNUM,
            Money::SEK(self::AMOUNT),
            [self::TAG_A, self::TAG_B]
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Cell::CLASS);
    }

    function it_contains_a_ver_number()
    {
        $this->getVerificationNr()->shouldReturn(self::VERNUM);
    }

    function it_contains_an_amount()
    {
        $this->getAmount()->shouldBeLike(Money::SEK(self::AMOUNT));
    }

    function it_contains_tags()
    {
        $this->getTags()->shouldReturn([self::TAG_A, self::TAG_B]);
    }

    function it_can_be_tagged()
    {
        $this->isTaggedWith('invalid')->shouldReturn(false);
        $this->isTaggedWith(self::TAG_A)->shouldReturn(true);
        $this->isTaggedWith(self::TAG_A, 'invalid')->shouldReturn(false);
        $this->isTaggedWith(self::TAG_A, self::TAG_B)->shouldReturn(true);
    }
}
