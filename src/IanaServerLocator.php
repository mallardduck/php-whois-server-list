<?php

declare(strict_types=1);

namespace MallardDuck\WhoisDomainList;

class IanaServerLocator extends ServerLocator
{
    public function getServerListPath(): string
    {
        return __DIR__ . '/../resources/iana-servers.json';
    }
}
