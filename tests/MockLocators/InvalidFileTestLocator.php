<?php

declare(strict_types=1);

namespace MallardDuck\Test\WhoisDomainList\MockLocators;

use MallardDuck\WhoisDomainList\ServerLocator;

use function dirname;

use const DIRECTORY_SEPARATOR;

class InvalidFileTestLocator extends ServerLocator
{
    public function getServerListPath(): string
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'empty.json';
    }
}
