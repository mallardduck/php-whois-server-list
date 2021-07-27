<?php

declare(strict_types=1);

namespace MallardDuck\WhoisDomainList\Generator;

use ArrayAccess;
use JsonSerializable;
use RuntimeException;

use function array_key_exists;
use function strtolower;

/**
 * @template-implements ArrayAccess<string, string>
 */
class TopLevelDomain implements ArrayAccess, JsonSerializable
{
    protected string $name;

    protected string $server;

    public function __construct(string $name, string $server)
    {
        $this->name = strtolower($name);
        $this->server = $server;
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->toArray());
    }

    public function offsetGet($offset): string
    {
        if (!$this->offsetExists($offset)) {
            throw new RuntimeException('Property does not exist.');
        }

        return $this->{$offset};
    }

    public function offsetSet($offset, $value): void
    {
        $this->$offset = $value;
    }

    public function offsetUnset($offset): void
    {
        // Intentionally unimplemented
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getServer(): string
    {
        return $this->server;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            $this->name => $this->server,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
