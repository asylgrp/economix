<?php

declare(strict_types = 1);

namespace asylgrp\decisionmaker\Normalizer;

use byrokrat\amount\Amount;
use byrokrat\amount\Currency\SEK;

trait HelperTrait
{
    protected function normalizeAmount(Amount $amount): string
    {
        return $amount->getAmount();
    }

    protected function denormalizeAmount(string $normalizedAmount): Amount
    {
        return new SEK($normalizedAmount);
    }

    protected function normalizeDate(\DateTimeImmutable $date): string
    {
        return $date->format(DATE_W3C);
    }

    protected function denormalizeDate(string $normalizedDate): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(DATE_W3C, $normalizedDate);
    }
}
