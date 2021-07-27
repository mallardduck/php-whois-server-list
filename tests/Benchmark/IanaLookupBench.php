<?php

declare(strict_types=1);

namespace MallardDuck\Test\WhoisDomainList\Benchmark;

use MallardDuck\WhoisDomainList\IanaServerLocator;

class IanaLookupBench
{
    private IanaServerLocator $locator;

    public function __construct()
    {
        $this->locator = new IanaServerLocator();
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
