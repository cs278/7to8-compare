<?php

namespace Cs278\Comparator;

final class ExperimentalComparator implements ComparatorInterface
{
    private Comparator $authoritative;
    private Comparator $underTest;
    private \Closure $onDifferenceCallback;
    private \Closure $onThrowableCallback;

    public function __construct(
        Comparator $authoritative,
        Comparator $underTest,
        \Closure $onDifferenceCallback,
        \Closure $onThrowableCallback,
    ) {
        $this->authoritative = $authoritative;
        $this->underTest = $underTest;
        $this->onDifferenceCallback = $onDifferenceCallback;
        $this->onThrowableCallback = $onThrowableCallback;
    }

    public function compare($a, $b): int
    {
        $result = $this->authoritative->compare($a, $b);

        try {
            $testResult = $this->underTest->compare($a, $b);

            if ($testResult !== $result) {
                ($this->onDifferenceCallback)($result, $testResult, $a, $b);
            }
        } catch (\Throwable $e) {
            ($this->onThrowableCallback)($e, $a, $b);
        }

        return $result;

    }
}