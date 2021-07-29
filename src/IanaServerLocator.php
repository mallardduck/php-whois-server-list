<?php

declare(strict_types=1);

namespace MallardDuck\WhoisDomainList;

use function dirname;

use const DIRECTORY_SEPARATOR;

class IanaServerLocator extends ServerLocator
{
    public function getServerListPath(): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'iana-servers.json';
    }
}
