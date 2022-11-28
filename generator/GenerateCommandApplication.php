<?php

declare(strict_types=1);

namespace MallardDuck\WhoisDomainList\Generator;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;
use Symfony\Component\Console\SingleCommandApplication;

use function array_combine;
use function array_keys;
use function array_map;
use function array_merge;
use function array_values;
use function date;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function json_decode;
use function json_encode;
use function sprintf;
use function strtolower;
use function time;

class GenerateCommandApplication extends SingleCommandApplication
{
    private const TIMESTAMP_DOWN_TO_HOURS = 'Y_m_d-H';

    public int $now;

    public string $ianaTldDomainsUrl = 'https://data.iana.org/TLD/tlds-alpha-by-domain.txt';

    public string $publicSuffixListUrl = 'https://publicsuffix.org/list/public_suffix_list.dat';

    /**
     * @var array<array-key, TopLevelDomain>
     */
    public array $parsedDomainList = [];

    public function __construct(string $name = 'Whois TLD Server List Generator')
    {
        $this->now = time();
        parent::__construct($name);
    }

    public function getListUrlByType(string $type): string
    {
        return match ($type) {
            BuildMode::IANA => $this->ianaTldDomainsUrl,
            BuildMode::PSL => $this->publicSuffixListUrl,
            default => throw new RuntimeException('Invalid type provided...')
        };
    }

    /**
     * @throws GuzzleException
     */
    public function retrieveRemoteFile(string $url): string
    {
        $client = new Client();
        $response = $client->get($url);

        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('unable to load ' . $url);
        }

        return $response->getBody()->getContents();
    }

    public function getListBody(string $buildType): string
    {
        $responsePath = $this->getTempDirPath() . $buildType . '-'
            . date(self::TIMESTAMP_DOWN_TO_HOURS, $this->now) . '_response.txt';
        if (file_exists($responsePath)) {
            if (($fileContents = file_get_contents($responsePath)) === false) {
                throw new RuntimeException('cannot find valid cache...');
            }

            return $fileContents;
        }

        $responseBody = $this->retrieveRemoteFile($this->getListUrlByType($buildType));
        file_put_contents($responsePath, $responseBody);

        return $responseBody;
    }

    private function getTempDirPath(): string
    {
        return __DIR__ . '/../build/tmp/';
    }

    public function parseResponse(string $type, string $body): void
    {
        $parsedPath = $this->getTempDirPath() . $type . '-'
            . date(self::TIMESTAMP_DOWN_TO_HOURS, $this->now) . '_domains.json';
        if (file_exists($parsedPath)) {
            if (($fileContents = file_get_contents($parsedPath)) === false) {
                throw new RuntimeException('cannot find valid cache...');
            }
            /**
             * @var array<string, TopLevelDomain> $results
             */
            $results = [];
            /**
             * @var array<string, array<string, string>> $domains
             */
            $domains = json_decode($fileContents, true);
            foreach ($domains as $tld => $data) {
                unset($domains[$tld]);
                $results[$tld] = new TopLevelDomain(array_keys($data)[0], array_values($data)[0]);
            }
            $this->parsedDomainList = $results;

            return;
        }

        $parser = new Parser($body);
        $parser->parse();
        $this->parsedDomainList = $parser->getTopLevelDomains();
        file_put_contents(
            $parsedPath,
            json_encode($this->parsedDomainList),
        );
    }

    /**
     * @param array<string, string>|null $meta
     */
    private function parsedListToJson(?array $meta = null): string
    {
        // Apply an array map to render values as arrays...
        $keys = array_keys($this->parsedDomainList);
        $items = array_map(fn ($value, $key) => $value->toArray(), $this->parsedDomainList, $keys);
        $items = array_combine($keys, $items);

        // Then collapse the multidimensional array...
        $flatMap = array_merge([], ...array_values($items));
        if ($meta !== null) {
            $flatMap['_meta'] = $meta;
        }

        if (($results = json_encode($flatMap)) === false) {
            throw new RuntimeException("Couldn't encode data to json...");
        }

        return $results;
    }

    public function writeList(string $type): void
    {
        $finalPath = sprintf('%s/../resources/%s-servers.json', __DIR__, strtolower($type));
        file_put_contents($finalPath, $this->parsedListToJson([
            'listType' => $type,
            'source' => $this->getListUrlByType($type),
        ]));
    }
}
