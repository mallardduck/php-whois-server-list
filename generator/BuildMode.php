<?php

declare(strict_types=1);

namespace MallardDuck\WhoisDomainList\Generator;

abstract class BuildMode
{
    public const IANA = 'iana';
    public const PSL = 'psl';
    public const BOTH = 'both';
}
