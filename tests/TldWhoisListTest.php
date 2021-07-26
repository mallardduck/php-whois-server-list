<?php

declare(strict_types=1);

namespace MallardDuck\Test\WhoisDomainList;

use MallardDuck\WhoisDomainList\TldWhoisList;
use Mockery\MockInterface;

class TldWhoisListTest extends TestCase
{
    public function testGreet(): void
    {
        /** @var TldWhoisList & MockInterface $example */
        $example = $this->mockery(TldWhoisList::class);
        $example->shouldReceive('greet')->passthru();

        $this->assertSame('Hello, Friends!', $example->greet('Friends'));
    }
}
