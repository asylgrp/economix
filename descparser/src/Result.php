<?php

declare(strict_types = 1);

namespace asylgrp\descparser;

use asylgrp\descparser\Tree\Node;
use asylgrp\descparser\Tree\DateNode;
use asylgrp\descparser\Tree\NameNode;
use asylgrp\descparser\Tree\RelationNode;
use asylgrp\descparser\Tree\TagNode;

class Result
{
    /**
     * List of understood date formats
     */
    private const DATE_FORMATS = ['j/n-y', 'j/n-Y', 'Ymd', 'Y-m-d'];

    /**
     * @var string
     */
    private $currentYear;

    /**
     * @var Node[]
     */
    private $nodes;

    public function __construct(string $currentYear, Node ...$nodes)
    {
        $this->currentYear = $currentYear;
        $this->nodes = $nodes;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        $dates = array_filter(
            array_map(
                function (Node $node): ?\DateTimeImmutable {
                    if (!$node instanceof DateNode) {
                        return null;
                    }

                    $currentYearDate = $node->getValue() . '-' . $this->currentYear;

                    if ($date = \DateTimeImmutable::createFromFormat('j/n-Y', $currentYearDate)) {
                        return $date;
                    }

                    foreach (self::DATE_FORMATS as $format) {
                        if ($date = \DateTimeImmutable::createFromFormat($format, $node->getValue())) {
                            return $date;
                        }
                    }

                    return null;
                },
                $this->getNodes()
            )
        );

        if (empty($dates)) {
            return null;
        }

        if (count($dates) > 1) {
            throw new \RuntimeException('Multiple valid dates found');
        }

        return array_values($dates)[0];
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->filterStrings(function (Node $node) {
            return $node instanceof TagNode;
        });
    }

    /**
     * @return string[]
     */
    public function getNames(): array
    {
        return $this->filterStrings(function (Node $node) {
            return $node instanceof NameNode;
        });
    }

    /**
     * @return string[]
     */
    public function getRelations(): array
    {
        return $this->filterStrings(function (Node $node) {
            return $node instanceof RelationNode;
        });
    }

    /**
     * @return Node[]
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * @return string[]
     */
    private function filterStrings(callable $filter): array
    {
        return array_values(
            array_map(
                function (Node $node) {
                    return $node->getValue();
                },
                array_filter(
                    $this->getNodes(),
                    $filter
                )
            )
        );
    }
}
