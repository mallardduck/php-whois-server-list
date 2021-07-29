<?php

declare(strict_types=1);

define('PROJ_PARENT_TMP', dirname(__DIR__, 2) . '/php-whois-domain-tmp');

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/


/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

// phpstan:disable
function getProperty(object $object, string $property)
{
    $reflection = new ReflectionObject($object);
    $reflectionProperty = $reflection->getProperty($property);
    if (!$reflectionProperty->isPublic()) {
        $reflectionProperty->setAccessible(true);
    }

    if (!$reflectionProperty->isInitialized($object)) {
        return null;
    }

    return $reflectionProperty->getValue($object);
}

afterAll(static function () {
    if (is_file(__DIR__ . '/../../tmp/empty.json')) {
        unlink(__DIR__ . '/../../tmp/empty.json');
    }
    if (is_dir(PROJ_PARENT_TMP)) {
        rmdir(PROJ_PARENT_TMP);
    }
});
