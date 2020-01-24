<?php

declare(strict_types = 1);

namespace receiptanalyzer\spec\asylgrp\receiptanalyzer;

use asylgrp\receiptanalyzer\CsvFactory;
use asylgrp\receiptanalyzer\ReceiptCollection;
use asylgrp\receiptanalyzer\Receipt;
use asylgrp\receiptanalyzer\Cell;
use Money\Money;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CsvFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CsvFactory::CLASS);
    }

    function it_can_create_receipt_collections()
    {
        $source ='
"contact", "periodA", "periodB"
"receiverA", "1;100;a", "2;100;b"
"receiverB", "", "3;100;a,b"
';

        $expected = new ReceiptCollection([
            new Receipt('contact', 'receiverA', 'periodA', new Cell('1', Money::SEK('100'), ['a'])),
            new Receipt('contact', 'receiverA', 'periodB', new Cell('2', Money::SEK('100'), ['b'])),
            new Receipt('contact', 'receiverB', 'periodB', new Cell('3', Money::SEK('100'), ['a', 'b']))
        ]);

        $this->fromString($source)->shouldBeLike($expected);
    }

    function it_handles_missing_tags()
    {
        $source ='
"contact", "periodA"
"receiverA", "1;100"
';

        $expected = new ReceiptCollection([
            new Receipt('contact', 'receiverA', 'periodA', new Cell('1', Money::SEK('100'), []))
        ]);

        $this->fromString($source)->shouldBeLike($expected);
    }

    function it_handles_missing_period_header()
    {
        $source ='
"contact"
"receiverA", "1;100;a"
';

        $expected = new ReceiptCollection([
            new Receipt('contact', 'receiverA', '', new Cell('1', Money::SEK('100'), ['a']))
        ]);

        $this->fromString($source)->shouldBeLike($expected);
    }

    function it_handles_missing_contact_header()
    {
        $source ='
"", "periodA"
"receiverA", "1;100;a"
';

        $expected = new ReceiptCollection([
            new Receipt('', 'receiverA', 'periodA', new Cell('1', Money::SEK('100'), ['a']))
        ]);

        $this->fromString($source)->shouldBeLike($expected);
    }
}
