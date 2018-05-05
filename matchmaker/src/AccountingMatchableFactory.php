<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker;

use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Matchable\Matchable;
use asylgrp\descparser\DescParser;
use byrokrat\accounting\Account;
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

    /**
     * @var \DateTimeImmutable
     */
    private $incomingBalanceDate;

    /**
     * @var DescParser
     */
    private $descParser;

    public function __construct(int $currentYear, DescParser $descParser)
    {
        $this->incomingBalanceDate = new \DateTimeImmutable(($currentYear - 1) . '-12-31');
        $this->descParser = $descParser;
    }

    public static function createFactoryForYear(int $currentYear)
    {
        return new self($currentYear, new DescParser((string)$currentYear));
    }

    /**
     * @return MatchableInterface[]
     */
    public function createMatchablesForAccount(Account $account, Container $bookkeeping): array
    {
        $matchables = [];

        $incomingBalance = $account->getAttribute('summary')->getIncomingBalance();

        if (!$incomingBalance->isZero()) {
            $matchables[] = new Matchable(
                self::INCOMING_BALANCE_ID,
                self::INCOMING_BALANCE_DESC,
                $this->incomingBalanceDate,
                $incomingBalance
            );
        }

        foreach ($bookkeeping->select()->verifications()->whereAccount($account->getId()) as $ver) {
            $descData = $this->descParser->parse($ver->getDescription());

            foreach ($ver->select()->transactions()->whereAccount($account->getId()) as $trans) {
                $matchables[] = new Matchable(
                    (string)$ver->getId(),
                    $trans->getDescription(),
                    $descData->getDate() ?: $trans->getDate(),
                    $trans->getAmount(),
                    $descData->getRelations()
                );
            }
        }

        return $matchables;
    }
}
