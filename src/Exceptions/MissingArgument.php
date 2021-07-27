<?php

declare(strict_types=1);

namespace MallardDuck\WhoisDomainList\Exceptions;

use RuntimeException;

class MissingArgument extends RuntimeException
{
    public static function create(): self
    {
        return new self('The input value cannot be an empty string.');
    }
}
