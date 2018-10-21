<?php

declare(strict_types = 1);

namespace asylgrp\matchmaker\Filter;

final class Failure implements ResultInterface
{
    /**
     * @var string
     */
    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function isSuccess(): bool
    {
        return false;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
