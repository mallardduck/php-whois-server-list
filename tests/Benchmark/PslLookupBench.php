<?php

declare(strict_types=1);

namespace MallardDuck\Test\WhoisDomainList\Benchmark;

use MallardDuck\WhoisDomainList\PslServerLocator;

class PslLookupBench
{
    private PslServerLocator $locator;

    public function __construct()
    {
        $this->locator = new PslServerLocator();
    }

    /**
     * @Revs(250)
     * @Iterations(5)
     */
    public function benchListLookup(): void
    {
        $this->locator->getWhoisServer('aarp');
        $this->locator->getWhoisServer('com');
        $this->locator->getWhoisServer('xyz');
    }
}
