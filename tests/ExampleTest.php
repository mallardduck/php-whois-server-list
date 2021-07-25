<?php

declare(strict_types=1);

namespace MallardDuck\Test\Whois\DomainList;

use Mockery\MockInterface;
use MallardDuck\Whois\DomainList\Example;

class ExampleTest extends TestCase
{
    public function testGreet(): void
    {
        /** @var Example & MockInterface $example */
        $example = $this->mockery(Example::class);
        $example->shouldReceive('greet')->passthru();

        $this->assertSame('Hello, Friends!', $example->greet('Friends'));
    }
}
