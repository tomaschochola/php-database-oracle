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

namespace TomasChochola\Connection\Oracle;

use InvalidArgumentException;
use NoDiscard;

use function array_key_exists;
use function is_int;
use function is_string;

/**
 * @no-named-arguments
 */
readonly class OracleSettingsFactory
{
    #[NoDiscard]
    public function create(string $username, string $password, string|null $connectionString, string $encoding, int $sessionMode): OracleSettings
    {
        return new OracleSettings($username, $password, $connectionString, $encoding, $sessionMode);
    }

    /**
     * @param array<mixed, mixed> $settings
     */
    #[NoDiscard]
    public function createFrom(array $settings): OracleSettings
    {
        if (!isset($settings['username']) || !is_string($settings['username'])) {
            throw new InvalidArgumentException('$settings');
        }

        if (!isset($settings['password']) || !is_string($settings['password'])) {
            throw new InvalidArgumentException('$settings');
        }

        if (!array_key_exists('connectionString', $settings)) {
            throw new InvalidArgumentException('$settings');
        }

        if (!is_string($settings['connectionString']) && $settings['connectionString'] !== null) {
            throw new InvalidArgumentException('$settings');
        }

        if (!isset($settings['encoding']) || !is_string($settings['encoding'])) {
            throw new InvalidArgumentException('$settings');
        }

        if (!isset($settings['sessionMode']) || !is_int($settings['sessionMode'])) {
            throw new InvalidArgumentException('$settings');
        }

        return $this->create($settings['username'], $settings['password'], $settings['connectionString'], $settings['encoding'], $settings['sessionMode']);
    }
}
