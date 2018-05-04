<?php

declare(strict_types = 1);

namespace asylgrp\descparser;

class DescParser
{
    /**
     * @var string
     */
    private $currentYear;

    /**
     * @var Grammar
     */
    private $grammar;

    public function __construct(string $currentYear = '', Grammar $grammar = null)
    {
        $this->currentYear = $currentYear;
        $this->grammar = $grammar ?: new Grammar;
    }

    public function parse(string $raw): Result
    {
        return new Result($this->currentYear, ...$this->grammar->parse($raw));
    }
}
