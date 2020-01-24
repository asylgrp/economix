<?php

declare(strict_types = 1);

namespace asylgrp\receiptanalyzer;

use Money\Money;

/**
 * @implements \IteratorAggregate<Receipt>
 */
class ReceiptCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var array<iterable<Receipt>> Iterables containing receipts
     */
    private array $receiptblocks;

    /**
     * @var array<iterable<Receipt>> $receiptblocks
     */
    public function __construct(iterable ...$receiptblocks)
    {
        $this->receiptblocks = $receiptblocks;
    }

    /**
     * Count receipts, implements Countable
     */
    public function count(): int
    {
        return iterator_count($this->getIterator());
    }

    /**
     * @return \Generator<Receipt>
     */
    public function getIterator(): \Generator
    {
        foreach ($this->receiptblocks as $block) {
            foreach ($block as $receipt) {
                yield $receipt;
            }
        }
    }

    /**
     * @return array<Receipt>
     */
    public function getReceipts(): array
    {
        return iterator_to_array($this->getIterator());
    }

    /**
     * Get receipt duplicates (eg. receipts that are stringified in the same way)
     *
     * @param  callable $stringifier Cast Receipt to string for comparison
     * @return array<array<Receipt>> Inner array contains two receipts that are considered duplicates
     */
    public function getDuplicateReceipts(callable $stringifier): array
    {
        $duplicates = [];
        $map = [];

        foreach ($this->getIterator() as $receipt) {
            $key = $stringifier($receipt);

            if (isset($map[$key])) {
                $duplicates[] = [$map[$key], $receipt];
            }

            $map[$key] = $receipt;
        }

        return $duplicates;
    }

    /**
     * Get unique contacts used in receipts
     *
     * @return array<string>
     */
    public function getContacts(): array
    {
        return array_values(
            array_unique(
                array_map(
                    function ($receipt) {
                        return $receipt->getContactName();
                    },
                    $this->getReceipts()
                )
            )
        );
    }

    /**
     * Get unique receivers used in receipts
     *
     * @return array<string>
     */
    public function getReceivers(): array
    {
        return array_values(
            array_unique(
                array_map(
                    function ($receipt) {
                        return $receipt->getReceiverName();
                    },
                    $this->getReceipts()
                )
            )
        );
    }

    /**
     * Get receiver duplicates (eg. receivers whose levenshtein distance is less than or equal to threshold)
     *
     * @param  integer $distanceThreshold Llevenshtein distance threshold
     * @return array<array<string>> Inner array contains two receivers that are considered duplicates
     */
    public function getDuplicateReceivers(int $distanceThreshold = 2): array
    {
        $duplicates = [];
        $receivers = $this->getReceivers();

        while ($left = array_shift($receivers)) {
            foreach ($receivers as $right) {
                $distance = levenshtein($left, $right);
                if ($distance > 0 && $distance <= $distanceThreshold) {
                    $duplicates[] = [$left, $right];
                }
            }
        }

        return $duplicates;
    }

    /**
     * Get unique periods used in receipts
     *
     * @return array<string>
     */
    public function getPeriods(): array
    {
        return array_values(
            array_unique(
                array_map(
                    function ($receipt) {
                        return $receipt->getPeriod();
                    },
                    $this->getReceipts()
                )
            )
        );
    }

    /**
     * Get periods that are definied in both $this and $receipts
     *
     * @return array<string>
     */
    public function getIntersectingPeriods(ReceiptCollection $receipts): array
    {
        return array_intersect($this->getPeriods(), $receipts->getPeriods());
    }

    /**
     * Get unique tags used in receipts
     *
     * @return array<string>
     */
    public function getTags(): array
    {
        return array_values(
            array_unique(
                array_reduce(
                    $this->getReceipts(),
                    function ($tags, $receipt) {
                        return array_merge($tags, $receipt->getTags());
                    },
                    []
                )
            )
        );
    }

    /**
     * Get total amount of all receipts in collection
     */
    public function getTotalAmount(): Money
    {
        $amount = null;

        foreach ($this->getIterator() as $receipt) {
            $amount =  $amount ? $amount->add($receipt->getAmount()) : $receipt->getAmount();
        }

        if (!$amount) {
            throw new \LogicException('Unable to get total amount from empty receipt collection');
        }

        return $amount;
    }

    /**
     * Create a new ReceiptCollection with receipts matching filter
     */
    public function where(callable $filter): ReceiptCollection
    {
        return new ReceiptCollection(array_values(array_filter($this->getReceipts(), $filter)));
    }

    /**
     * Get collection of receipts where contact is ANY one of listed values
     */
    public function whereContact(string ...$contacts): ReceiptCollection
    {
        return $this->where(function ($receipt) use ($contacts) {
            return in_array($receipt->getContactName(), $contacts);
        });
    }

    /**
     * Get collection of receipts where contact is NOT in ANY one of the listed values
     */
    public function whereNotContact(string ...$contacts): ReceiptCollection
    {
        return $this->where(function ($receipt) use ($contacts) {
            return !in_array($receipt->getContactName(), $contacts);
        });
    }

    /**
     * Get collection of receipts where receiver is ANY one of listed values
     */
    public function whereReceiver(string ...$receivers): ReceiptCollection
    {
        return $this->where(function ($receipt) use ($receivers) {
            return in_array($receipt->getReceiverName(), $receivers);
        });
    }

    /**
     * Get collection of receipts where receiver is NOT in ANY one of the listed values
     */
    public function whereNotReceiver(string ...$receivers): ReceiptCollection
    {
        return $this->where(function ($receipt) use ($receivers) {
            return !in_array($receipt->getReceiverName(), $receivers);
        });
    }

    /**
     * Get collection of receipts where period is ANY one of listed values
     */
    public function wherePeriod(string ...$periods): ReceiptCollection
    {
        return $this->where(function ($receipt) use ($periods) {
            return in_array($receipt->getPeriod(), $periods);
        });
    }

    /**
     * Get collection of receipts NOT in ANY period
     */
    public function whereNotPeriod(string ...$periods): ReceiptCollection
    {
        return $this->where(function ($receipt) use ($periods) {
            return !in_array($receipt->getPeriod(), $periods);
        });
    }

    /**
     * Get collection of receipts tagged with ANY one of listed values
     */
    public function whereTag(string ...$tags): ReceiptCollection
    {
        return $this->where(function ($receipt) use ($tags) {
            foreach ($tags as $tag) {
                if ($receipt->isTaggedWith($tag)) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Get collection of receipts NOT tagged with ALL of the listed values
     */
    public function whereNotTag(string ...$tags): ReceiptCollection
    {
        return $this->where(function ($receipt) use ($tags) {
            return !$receipt->isTaggedWith(...$tags);
        });
    }

    /**
     * Validate that collection does not contain verification number duplicates
     *
     * @throws \RuntimeException If there is a duplicate
     */
    public function assertUniqueVerificationNumbers(): void
    {
        $vernums = [];

        foreach ($this->getIterator() as $receipt) {
            if (isset($vernums[$receipt->getVerificationNr()])) {
                throw new \RuntimeException("Verification number duplicate {$receipt->getVerificationNr()}");
            }
            $vernums[$receipt->getVerificationNr()] = true;
        }
    }
}
