<?php

/**
 * @author Tomáš Chochola <tomaschochola@tomaschochola.cz>
 * @copyright © 2026 Tomáš Chochola <tomaschochola@tomaschochola.cz>
 *
 * @license CC-BY-ND-4.0
 *
 * @see {@link https://creativecommons.org/licenses/by-nd/4.0/} License
 * @see {@link https://github.com/tomaschochola} GitHub Profile
 * @see {@link https://github.com/sponsors/tomaschochola} GitHub Sponsors
 */

declare(strict_types=1);

namespace Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use TomasChochola\Oracle\Database\OracleException;
use TomasChochola\Oracle\Database\OracleSettings;
use TomasChochola\Oracle\Database\OracleSettingsFactory;

use function array_diff_key;
use function array_replace;

/**
 * @internal
 *
 * @no-named-arguments
 */
#[CoversClass(OracleException::class)]
#[CoversClass(OracleSettings::class)]
#[CoversClass(OracleSettingsFactory::class)]
#[Small()]
final class CompatibilityTest extends TestCase
{
    /**
     * @param array<mixed, mixed> $settings
     */
    #[DataProvider('provideInvalidSettingsAreRejectedCases')]
    #[Test()]
    public function invalidSettingsAreRejected(array $settings): void
    {
        $factory = new OracleSettingsFactory();

        $this->expectException(InvalidArgumentException::class);

        self::fail('Invalid settings unexpectedly accepted: ' . $factory->createFrom($settings)::class);
    }

    /**
     * @return iterable<string, array{array<mixed, mixed>}>
     */
    public static function provideInvalidSettingsAreRejectedCases(): iterable
    {
        $valid = [
            'connectionString' => null,
            'encoding' => 'AL32UTF8',
            'password' => 'secret',
            'sessionMode' => 0,
            'username' => 'user',
        ];

        yield 'missing username' => [array_diff_key($valid, ['username' => true])];
        yield 'invalid username' => [array_replace($valid, ['username' => null])];
        yield 'missing password' => [array_diff_key($valid, ['password' => true])];
        yield 'missing connection string' => [array_diff_key($valid, ['connectionString' => true])];
        yield 'invalid connection string' => [array_replace($valid, ['connectionString' => false])];
        yield 'missing encoding' => [array_diff_key($valid, ['encoding' => true])];
        yield 'invalid encoding' => [array_replace($valid, ['encoding' => null])];
        yield 'missing session mode' => [array_diff_key($valid, ['sessionMode' => true])];
        yield 'invalid session mode' => [array_replace($valid, ['sessionMode' => '0'])];
    }

    #[Test()]
    public function malformedOracleErrorUsesSafeDefaults(): void
    {
        $error = ['code' => '942', 'message' => 942];
        $exception = new OracleException($error);

        self::assertSame($error, $exception->error);
        self::assertSame(0, $exception->getCode());
        self::assertSame('', $exception->getMessage());
    }

    #[Test()]
    public function oracleErrorIsPreserved(): void
    {
        $error = [
            'code' => 942,
            'message' => 'table or view does not exist',
            'sqltext' => 'SELECT * FROM missing',
        ];

        $exception = new OracleException($error);

        self::assertSame($error, $exception->error);
        self::assertSame(942, $exception->getCode());
        self::assertSame('table or view does not exist', $exception->getMessage());
    }

    #[Test()]
    public function settingsAreCreatedAndCloned(): void
    {
        $factory = new OracleSettingsFactory();

        $settings = $factory->createFrom([
            'connectionString' => null,
            'encoding' => 'AL32UTF8',
            'password' => 'secret',
            'sessionMode' => 0,
            'username' => 'user',
        ]);

        self::assertNull($settings->connectionString);
        self::assertSame('AL32UTF8', $settings->encoding);
        self::assertSame('secret', $settings->password);
        self::assertSame(0, $settings->sessionMode);
        self::assertSame('user', $settings->username);

        $changed = $settings->clone(['connectionString' => 'database.example.test/service']);

        self::assertNotSame($settings, $changed);
        self::assertNull($settings->connectionString);
        self::assertSame('database.example.test/service', $changed->connectionString);
    }
}
