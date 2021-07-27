<?php

declare(strict_types=1);

namespace MallardDuck\WhoisDomainList\Generator;

use MallardDuck\Whois\Client;
use RuntimeException;

use function count;
use function explode;
use function idn_to_ascii;
use function preg_match;
use function strpos;
use function substr;
use function trim;

class Parser
{
    protected string $body;

    /** @var array<TopLevelDomain> */
    protected array $domains = [];

    public function __construct(string $body)
    {
        $this->body = $body;
    }

    public function parse(): void
    {
        $lines = explode("\n", $this->body);

        foreach ($lines as $line) {
            $this->parseLine(trim($line));
        }
    }

    /**
     * @return TopLevelDomain[]
     */
    public function getTopLevelDomains(): array
    {
        return $this->domains;
    }

    protected function parseLine(string $line): void
    {
        if ($line === '') {
            return;
        }

        if (strpos($line, '#') === 0 || strpos($line, '//') === 0) {
            return;
        }

        if (strpos($line, '*.') === 0) {
            $line = substr($line, 2);
        }

        $this->addTopLevelDomain($line);
    }

    protected function addTopLevelDomain(string $name): void
    {
        $asciiDomain = idn_to_ascii($name);
        if ($asciiDomain === false) {
            throw new RuntimeException('Cannot convert to IDN...' . $name);
        }
        $domain = new TopLevelDomain($asciiDomain, $this->findAuthoritativeWhoisServer($asciiDomain));

        $this->domains[$name] = $domain;
    }

    private function findAuthoritativeWhoisServer(string $tld): string
    {
        $client = new Client('whois.iana.org');
        preg_match('/whois:(\s*)(.*)/i', $client->makeRequest($tld), $matches);
        if (count($matches) === 0) {
            return 'whois.iana.org';
        }

        return $matches[2];
    }
}
