<?php

declare(strict_types = 1);

namespace asylgrp\accounting2matchable;

use asylgrp\descparser\DescParser;
use asylgrp\matchmaker\Matchable\MatchableInterface;
use asylgrp\matchmaker\Matchable\Matchable;
use byrokrat\accounting\Account;
use byrokrat\accounting\Container;

/**
 * Transform accounting data to matchable items
 */
class MatchableFactory
{
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
                '',
                'Skuld från föregående år',
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
