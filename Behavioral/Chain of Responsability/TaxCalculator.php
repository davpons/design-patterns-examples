<?php

abstract class TaxCalculator
{
    protected ?TaxCalculator $next = null;

    public function setNext(TaxCalculator $next): void
    {
        $this->next = $next;
    }

    public abstract function getValue(float $total, string $country): float;
}

class GermanyTaxCalculator extends TaxCalculator
{
    public function getValue(float $total, string $country): float
    {
        if (mb_strtolower($country) === 'germany') {
            return 0.1 * $total;
        }

        if ($this->next !== null) {
            return $this->next->getValue($total, $country);
        }

        throw new \Exception('Not implemented exception');
    }
}

class SpainTaxCalculator extends TaxCalculator
{
    public function getValue(float $total, string $country): float
    {
        if (mb_strtolower($country) === 'spain') {
            return 0.21 * $total;
        }

        if ($this->next !== null) {
            return $this->next->getValue($total, $country);
        }

        throw new \Exception('Not implemented exception');
    }
}

$germanyTaxCalculator = new GermanyTaxCalculator();
$spainTaxCalculator = new SpainTaxCalculator();
$germanyTaxCalculator->setNext($spainTaxCalculator);

echo $germanyTaxCalculator->getValue(100, 'Spain');
