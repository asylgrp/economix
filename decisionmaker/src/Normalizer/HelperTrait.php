<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Normalizer;

use Money\Money;

trait HelperTrait
{
    protected function normalizeAmount(Money $amount): string
    {
        return $amount->getAmount();
    }

    protected function denormalizeAmount(string $normalizedAmount): Money
    {
        return Money::SEK($normalizedAmount);
    }

    protected function normalizeDate(\DateTimeImmutable $date): string
    {
        return $date->format(DATE_W3C);
    }

    protected function denormalizeDate(string $normalizedDate): \DateTimeImmutable
    {
        $date = \DateTimeImmutable::createFromFormat(DATE_W3C, $normalizedDate);

        if (!$date) {
            throw new \LogicException("Unable to denormalize $normalizedDate");
        }

        return $date;
    }
}
