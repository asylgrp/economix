<?php

declare(strict_types = 1);

namespace receiptanalyzer\spec\asylgrp\receiptanalyzer;

use asylgrp\receiptanalyzer\Receipt;
use asylgrp\receiptanalyzer\Cell;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ReceiptSpec extends ObjectBehavior
{
    const CONTACT = 'contact';
    const RECEIVER = 'receiver';
    const PERIOD = 'period';

    function let(Cell $cell)
    {
        $this->beConstructedWith(
            self::CONTACT,
            self::RECEIVER,
            self::PERIOD,
            $cell
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Receipt::CLASS);
    }

    function it_contains_a_ver_number($cell)
    {
        $cell->getVerificationNr()->willReturn('ver');
        $this->getVerificationNr()->shouldReturn('ver');
    }

    function it_contains_a_contact_name()
    {
        $this->getContactName()->shouldReturn(self::CONTACT);
    }

    function it_contains_a_receiver_name()
    {
        $this->getReceiverName()->shouldReturn(self::RECEIVER);
    }

    function it_contains_a_period()
    {
        $this->getPeriod()->shouldReturn(self::PERIOD);
    }

    function it_contains_an_amount($cell)
    {
        $amount = Money::SEK('1');
        $cell->getAmount()->willReturn($amount);
        $this->getAmount()->shouldReturn($amount);
    }

    function it_contains_tags($cell)
    {
        $cell->getTags()->willReturn(['A', 'B']);
        $this->getTags()->shouldReturn(['A', 'B']);
    }

    function it_can_be_tagged($cell)
    {
        $cell->isTaggedWith('A', 'B')->willReturn(false);
        $this->isTaggedWith('A', 'B')->shouldReturn(false);
    }
}
