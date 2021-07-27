<?php

declare(strict_types=1);

namespace MallardDuck\WhoisDomainList;

class PslServerLocator extends ServerLocator
{
    public function getServerListPath(): string
    {
        return __DIR__ . '/../resources/psl-servers.json';
    }
}
