<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker;

use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Matchable\Matchable;
use asylgrp\descparser\DescParser;
use byrokrat\accounting\Dimension\AccountInterface;
use byrokrat\accounting\Container;

/**
 * Transform accounting data to matchable items
 */
class AccountingMatchableFactory
{
    /**
     * Id used when creating incoming balance matchable
     */
    const INCOMING_BALANCE_ID = '0';

    /**
     * Description used when creating incoming balance matchable
     */
    const INCOMING_BALANCE_DESC = 'Skuld från föregående år';

    private \DateTimeImmutable $incomingBalanceDate;
    private DescParser $descParser;

    public function __construct(int $currentYear, DescParser $descParser)
    {
        $this->incomingBalanceDate = new \DateTimeImmutable(($currentYear - 1) . '-12-31');
        $this->descParser = $descParser;
    }

    public static function createFactoryForYear(int $currentYear): self
    {
        return new self($currentYear, new DescParser((string)$currentYear));
    }

    /**
     * @param Container<mixed> $bookkeeping
     * @return array<MatchableInterface>
     */
    public function createMatchablesForAccount(AccountInterface $account, Container $bookkeeping): array
    {
        $matchables = [];

        if ($summary = $account->getAttribute('summary')) {
            $incomingBalance = $summary->getIncomingBalance();

            if (!$incomingBalance->isZero()) {
                $matchables[] = new Matchable(
                    self::INCOMING_BALANCE_ID,
                    self::INCOMING_BALANCE_DESC,
                    $this->incomingBalanceDate,
                    $incomingBalance
                );
            }
        }

        foreach ($bookkeeping->select()->verifications()->whereAccount($account->getId()) as $ver) {
            $descData = $this->descParser->parse($ver->getDescription());

            foreach ($ver->select()->transactions()->whereAccount($account->getId()) as $trans) {
                $matchables[] = new Matchable(
                    (string)$ver->getVerificationId(),
                    $trans->getDescription(),
                    $descData->getDate() ?: $trans->getTransactionDate(),
                    $trans->getAmount(),
                    $descData->getRelations()
                );
            }
        }

        return $matchables;
    }
}
