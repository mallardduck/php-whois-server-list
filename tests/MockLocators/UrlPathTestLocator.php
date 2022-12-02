<?php

declare(strict_types=1);

namespace MallardDuck\Test\WhoisDomainList\MockLocators;

use MallardDuck\WhoisDomainList\ServerLocator;

class UrlPathTestLocator extends ServerLocator
{
    public function getServerListPath(): string
    {
        return 'https://api.plos.org/search?q=title:DNA';
    }
}
