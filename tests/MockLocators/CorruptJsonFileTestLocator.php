<?php

declare(strict_types=1);

namespace MallardDuck\Test\WhoisDomainList\MockLocators;

use MallardDuck\WhoisDomainList\ServerLocator;

class CorruptJsonFileTestLocator extends ServerLocator
{
    public function getServerListPath(): string
    {
        return __DIR__ . '/../stubs/corrupt.json';
    }
}
