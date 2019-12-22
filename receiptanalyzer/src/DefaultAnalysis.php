<?php

declare(strict_types = 1);

namespace asylgrp\receiptanalyzer;

use byrokrat\amount\Amount;

class DefaultAnalysis
{
    public static function analyze(ReceiptCollection $receipts, bool $recurse = true): void
    {
        $receipts->assertUniqueVerificationNumbers();

        printf("Kontakptersoner: %s\n", implode(', ', $receipts->getContacts()));
        printf("Antal mottagare: %s\n", count($receipts->getReceivers()));
        printf("Antal kvitton: %s\n", count($receipts));
        printf("Totalsumma: %s\n", self::money($receipts->getTotalAmount()));
        printf("Perioder: %s\n", implode(', ', $receipts->getPeriods()));

        $tags = $receipts->getTags();
        sort($tags);

        printf("Taggar: %s\n", implode(', ', $tags));

        $duplicateReceipts = $receipts->getDuplicateReceipts(function ($receipt) {
            return "{$receipt->getReceiverName()}:{$receipt->getPeriod()}";
        });
        printf("Misstänkta kvittodubletter: %s\n", count($duplicateReceipts));
        foreach ($duplicateReceipts as list($left, $right)) {
            printf(
                " - %s och %s (%s i %s)\n",
                $left->getVerificationNr(),
                $right->getVerificationNr(),
                $left->getReceiverName(),
                $left->getPeriod()
            );
        }

        $duplicateReceivers = $receipts->getDuplicateReceivers(2);
        printf("Misstänkta mottagardubletter: %s\n", count($duplicateReceivers));
        foreach ($duplicateReceivers as list($left, $right)) {
            printf(" - %s => %s\n", $left, $right);
        }

        echo "Fördelning över antal kvitton:\n";
        foreach (range(1, 10) as $n) {
            if ($count = count(self::getReceiversWithNReceipts($n, $receipts))) {
                echo " - $n st: $count mottagare\n";
            }
        }

        echo "\n";

        if ($recurse) {
            foreach ($tags as $tag) {
                echo "Tagg: $tag\n";
                self::analyze($receipts->whereTag($tag), false);
            }
        }
    }

    /**
     * @return array<string>
     */
    private static function getReceiversWithNReceipts(int $n, ReceiptCollection $receipts): array
    {
        $found = [];

        foreach ($receipts->getReceivers() as $receiver) {
            if (count($receipts->whereReceiver($receiver)) == $n) {
                $found[] = $receiver;
            }
        }

        return $found;
    }

    private static function money(Amount $amount): string
    {
        return (new \NumberFormatter('sv_SE', \NumberFormatter::CURRENCY))->formatCurrency($amount->getFloat(), 'sek');
    }
}
