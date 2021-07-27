<?php

declare(strict_types=1);

namespace MallardDuck\WhoisDomainList\Exceptions;

use Exception;

class UnknownTopLevelDomain extends Exception
{
    public static function create(string $topLevelDomain): self
    {
        return new self("TLD `{$topLevelDomain}` does not exist");
    }
}
