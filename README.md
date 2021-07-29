<h1 align="center">mallardduck/whois-server-list</h1>

<p align="center">
    <strong>A php library to look up the whois servers of a Top Level Domain.</strong>
</p>

<p align="center">
    <a href="https://github.com/mallardduck/php-whois-server-list"><img src="http://img.shields.io/badge/source-mallardduck/php--whois--server--list-blue.svg?style=for-the-badge" alt="Source Code"></a>
    <a href="https://github.com/mallardduck/php-whois-server-list/blob/master/LICENSE"><img src="https://img.shields.io/github/license/mallardduck/php-whois-server-list?style=for-the-badge&colorB=darkcyan" alt="Read License"></a>
    <a href="https://php.net"><img src="https://img.shields.io/packagist/php-v/mallardduck/whois-server-list.svg?style=for-the-badge&colorB=%238892BF" alt="PHP Programming Language"></a>
    <a href="https://packagist.org/packages/mallardduck/whois-server-list"><img src="https://img.shields.io/packagist/v/mallardduck/whois-server-list.svg?style=for-the-badge&label=release" alt="Download Package"></a>
    <a href="https://packagist.org/packages/mallardduck/whois-server-list"><img src="https://img.shields.io/packagist/dt/mallardduck/whois-server-list?style=for-the-badge" alt="Package Download Count"></a>
    <a href="https://github.com/mallardduck/php-whois-server-list/actions/workflows/continuous-integration.yml"><img src="https://img.shields.io/github/workflow/status/mallardduck/php-whois-server-list/build/main?style=for-the-badge&logo=github" alt="Build Status"></a>
    <a href="https://codecov.io/gh/mallardduck/php-whois-server-list"><img src="https://img.shields.io/codecov/c/gh/mallardduck/php-whois-server-list?label=codecov&logo=codecov&style=for-the-badge" alt="Codecov Code Coverage"></a>
    <a href="https://shepherd.dev/github/mallardduck/php-whois-server-list"><img src="https://img.shields.io/endpoint?style=for-the-badge&url=https%3A%2F%2Fshepherd.dev%2Fgithub%2Fmallardduck%2Fphp-whois-server-list%2Fcoverage" alt="Psalm Type Coverage"></a>
    <a href="https://shepherd.dev/github/mallardduck/php-whois-server-list"><img src="https://img.shields.io/badge/Psalm%20Level-1-green?style=for-the-badge" alt="Psalm Level"></a>
</p>


## About

This package facilitates the discovery of the authoritative WHOIS server for top level domains.
There are two lists to source the WHOIS server info from; the IANA TLD list and the Public Suffix List.

This project adheres to a [code of conduct](CODE_OF_CONDUCT.md).
By participating in this project and its community, you are expected to
uphold this code.


## Installation

Install this package as a dependency using [Composer](https://getcomposer.org).

``` bash
composer require mallardduck/whois-server-list
```


## Usage

Simply initialize a locator for the list you'd like to use.

``` php
use MallardDuck\WhoisDomainList\IanaServerLocator;

$ianaLocator = new IanaServerLocator();
echo $ianaLocator->getWhoisServer('aarp'); // whois.nic.aarp
```

## Updating

The lists used by this package generate using the script in the `./generator` directory.
This script will download a fresh copy of the list, then look up every TLDs whois server.

To update the list one would simply: clone this repo, run the generator, commit the changes and send a Pull Request.

## Contributing

Contributions are welcome! To contribute, please familiarize yourself with
[CONTRIBUTING.md](CONTRIBUTING.md).


## Copyright and License

The mallardduck/whois-server-list library is copyright © [Dan Pock](mailto:self@danpock.me)
and licensed for use under the terms of the
MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
