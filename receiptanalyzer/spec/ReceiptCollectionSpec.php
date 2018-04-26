<?php

declare(strict_types = 1);

namespace receiptanalyzer\spec\asylgrp\receiptanalyzer;

use asylgrp\receiptanalyzer\ReceiptCollection;
use asylgrp\receiptanalyzer\Receipt;
use byrokrat\amount\Amount;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ReceiptCollectionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ReceiptCollection::CLASS);
    }

    function it_can_iterate_over_receipts(Receipt $receiptA, Receipt $receiptB)
    {
        $this->beConstructedWith([$receiptA, $receiptB]);
        $this->getReceipts()->shouldIterateAs([$receiptA, $receiptB]);
    }

    function it_can_contain_multiple_receipt_blocks(Receipt $receiptA, Receipt $receiptB)
    {
        $this->beConstructedWith([$receiptA], [$receiptB]);
        $this->getReceipts()->shouldIterateAs([$receiptA, $receiptB]);
    }

    function it_can_iterate_over_contacts(Receipt $receiptA, Receipt $receiptB, Receipt $receiptC)
    {
        $receiptA->getContactName()->willReturn('A');
        $receiptB->getContactName()->willReturn('B');
        $receiptC->getContactName()->willReturn('A');

        $this->beConstructedWith([$receiptA, $receiptB, $receiptC]);
        $this->getContacts()->shouldIterateAs(['A', 'B']);
    }

    function it_can_iterate_over_receivers(Receipt $receiptA, Receipt $receiptB, Receipt $receiptC)
    {
        $receiptA->getReceiverName()->willReturn('A');
        $receiptB->getReceiverName()->willReturn('B');
        $receiptC->getReceiverName()->willReturn('A');

        $this->beConstructedWith([$receiptA, $receiptB, $receiptC]);
        $this->getReceivers()->shouldIterateAs(['A', 'B']);
    }

    function it_can_find_duplicate_receivers(Receipt $receiptA, Receipt $receiptB, Receipt $receiptC)
    {
        $receiptA->getReceiverName()->willReturn('foobar');
        $receiptB->getReceiverName()->willReturn('baz');
        $receiptC->getReceiverName()->willReturn('fobar');

        $this->beConstructedWith([$receiptA, $receiptB, $receiptC]);
        $this->getDuplicateReceivers()->shouldIterateAs([['foobar', 'fobar']]);
    }

    function it_can_iterate_over_periods(Receipt $receiptA, Receipt $receiptB, Receipt $receiptC)
    {
        $receiptA->getPeriod()->willReturn('A');
        $receiptB->getPeriod()->willReturn('B');
        $receiptC->getPeriod()->willReturn('A');

        $this->beConstructedWith([$receiptA, $receiptB, $receiptC]);
        $this->getPeriods()->shouldIterateAs(['A', 'B']);
    }

    function it_can_find_intersecting_periods(Receipt $receiptA, Receipt $receiptB, Receipt $receiptC)
    {
        $receiptA->getPeriod()->willReturn('A');
        $receiptB->getPeriod()->willReturn('B');
        $receiptC->getPeriod()->willReturn('A');
        $this->beConstructedWith([$receiptA, $receiptB]);

        $this->getIntersectingPeriods(
            new ReceiptCollection([$receiptC->getWrappedObject()])
        )->shouldIterateAs(['A']);
    }

    function it_can_iterate_over_tags(Receipt $receiptA, Receipt $receiptB, Receipt $receiptC)
    {
        $receiptA->getTags()->willReturn(['A', 'B']);
        $receiptB->getTags()->willReturn(['B', 'C']);
        $receiptC->getTags()->willReturn(['A', 'B']);

        $this->beConstructedWith([$receiptA, $receiptB, $receiptC]);
        $this->getTags()->shouldIterateAs(['A', 'B', 'C']);
    }

    function it_can_summarize_amounts(
        Receipt $receiptA,
        Receipt $receiptB,
        Amount $amountA,
        Amount $amountB,
        Amount $amountC
    ) {
        $amountA->add($amountB)->willReturn($amountC);

        $receiptA->getAmount()->willReturn($amountA);
        $receiptB->getAmount()->willReturn($amountB);

        $this->beConstructedWith([$receiptA, $receiptB]);
        $this->getTotalAmount()->shouldReturn($amountC);
    }

    function it_fails_on_summarize_with_no_amounts() {
        $this->shouldThrow(\LogicException::CLASS)->during('getTotalAmount');
    }

    function it_can_filter_contacts(Receipt $receiptA, Receipt $receiptB, Receipt $receiptC)
    {
        $receiptA->getContactName()->willReturn('A');
        $receiptB->getContactName()->willReturn('B');
        $receiptC->getContactName()->willReturn('C');

        $this->beConstructedWith([$receiptA, $receiptB, $receiptC]);
        $this->whereContact('A', 'B')->shouldBeLike(
            new ReceiptCollection([$receiptA->getWrappedObject(), $receiptB->getWrappedObject()])
        );
    }

    function it_can_filter_on_not_contacts(Receipt $receiptA, Receipt $receiptB, Receipt $receiptC)
    {
        $receiptA->getContactName()->willReturn('A');
        $receiptB->getContactName()->willReturn('B');
        $receiptC->getContactName()->willReturn('C');

        $this->beConstructedWith([$receiptA, $receiptB, $receiptC]);
        $this->whereNotContact('C')->shouldBeLike(
            new ReceiptCollection([$receiptA->getWrappedObject(), $receiptB->getWrappedObject()])
        );
    }

    function it_can_filter_receivers(Receipt $receiptA, Receipt $receiptB, Receipt $receiptC)
    {
        $receiptA->getReceiverName()->willReturn('A');
        $receiptB->getReceiverName()->willReturn('B');
        $receiptC->getReceiverName()->willReturn('C');

        $this->beConstructedWith([$receiptA, $receiptB, $receiptC]);
        $this->whereReceiver('A', 'B')->shouldBeLike(
            new ReceiptCollection([$receiptA->getWrappedObject(), $receiptB->getWrappedObject()])
        );
    }

    function it_can_filter_on_not_receivers(Receipt $receiptA, Receipt $receiptB, Receipt $receiptC)
    {
        $receiptA->getReceiverName()->willReturn('A');
        $receiptB->getReceiverName()->willReturn('B');
        $receiptC->getReceiverName()->willReturn('C');

        $this->beConstructedWith([$receiptA, $receiptB, $receiptC]);
        $this->whereNotReceiver('C')->shouldBeLike(
            new ReceiptCollection([$receiptA->getWrappedObject(), $receiptB->getWrappedObject()])
        );
    }

    function it_can_filter_periods(Receipt $receiptA, Receipt $receiptB, Receipt $receiptC)
    {
        $receiptA->getPeriod()->willReturn('A');
        $receiptB->getPeriod()->willReturn('B');
        $receiptC->getPeriod()->willReturn('C');

        $this->beConstructedWith([$receiptA, $receiptB, $receiptC]);
        $this->wherePeriod('A', 'B')->shouldBeLike(
            new ReceiptCollection([$receiptA->getWrappedObject(), $receiptB->getWrappedObject()])
        );
    }

    function it_can_filter_on_not_periods(Receipt $receiptA, Receipt $receiptB, Receipt $receiptC)
    {
        $receiptA->getPeriod()->willReturn('A');
        $receiptB->getPeriod()->willReturn('B');
        $receiptC->getPeriod()->willReturn('C');

        $this->beConstructedWith([$receiptA, $receiptB, $receiptC]);
        $this->whereNotPeriod('C')->shouldBeLike(
            new ReceiptCollection([$receiptA->getWrappedObject(), $receiptB->getWrappedObject()])
        );
    }

    function it_can_filter_tags(Receipt $receiptA, Receipt $receiptB, Receipt $receiptC)
    {
        $receiptA->isTaggedWith('A')->willReturn(true);
        $receiptB->isTaggedWith('A')->willReturn(false);
        $receiptB->isTaggedWith('B')->willReturn(true);
        $receiptC->isTaggedWith('A')->willReturn(false);
        $receiptC->isTaggedWith('B')->willReturn(false);

        $this->beConstructedWith([$receiptA, $receiptB, $receiptC]);
        $this->whereTag('A', 'B')->shouldBeLike(
            new ReceiptCollection([$receiptA->getWrappedObject(), $receiptB->getWrappedObject()])
        );
    }

    function it_can_filter_on_not_tags(Receipt $receiptA, Receipt $receiptB)
    {
        $receiptA->isTaggedWith('A', 'B')->willReturn(true);
        $receiptB->isTaggedWith('A', 'B')->willReturn(false);

        $this->beConstructedWith([$receiptA, $receiptB]);
        $this->whereNotTag('A', 'B')->shouldBeLike(
            new ReceiptCollection([$receiptB->getWrappedObject()])
        );
    }

    function it_can_find_verification_number_dublicates(Receipt $receiptA, Receipt $receiptB)
    {
        $receiptA->getVerificationNr()->willReturn('foo');
        $receiptB->getVerificationNr()->willReturn('foo');
        $this->beConstructedWith([$receiptA, $receiptB]);
        $this->shouldThrow(\RuntimeException::CLASS)->during('assertUniqueVerificationNumbers');
    }
}
