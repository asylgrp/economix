<?php

declare(strict_types = 1);

namespace asylgrp\receiptanalyzer;

use byrokrat\amount\Amount;

class CsvFactory
{
    /**
     * @var callable
     */
    private $cellParser;

    public function __construct(callable $cellParser = null)
    {
        $this->cellParser = $cellParser ?: function (string $cellData): ?Cell {
            $parts = explode(';', $cellData);

            if (count($parts) < 2) {
                return null;
            }

            return new Cell(
                $parts[0],
                new Amount($parts[1]),
                array_filter(explode(',', $parts[2] ?? ''))
            );
        };
    }

    public function fromString(string $source): ReceiptCollection
    {
        $tmpfile = (string)tempnam(sys_get_temp_dir(), 'receiptanalyzer');
        file_put_contents($tmpfile, trim($source));
        $collection = self::fromFile($tmpfile);
        unlink($tmpfile);
        return $collection;
    }

    public function fromFile(string $filename): ReceiptCollection
    {
        $filehandle = fopen($filename, 'r');

        if (!$filehandle) {
            throw new \RuntimeException("Unable to open $filename");
        }

        $periods = fgetcsv($filehandle);

        if (!$periods) {
            throw new \RuntimeException("Invalid file $filename");
        }

        $contact = array_shift($periods);
        $receipts = [];

        while (($row = fgetcsv($filehandle))) {
            $receiver = array_shift($row);

            foreach ($row as $index => $cellData) {
                if ($cell = ($this->cellParser)($cellData)) {
                    $receipts[] = new Receipt(
                        $contact,
                        $receiver,
                        $periods[$index] ?? '',
                        $cell
                    );
                }
            }
        }

        return new ReceiptCollection($receipts);
    }
}
