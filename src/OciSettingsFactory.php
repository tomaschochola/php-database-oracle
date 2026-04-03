<?php

declare(strict_types=1);

namespace TomasChochola\Connection\Oci;

use InvalidArgumentException;
use NoDiscard;

use function array_key_exists;
use function is_int;
use function is_string;

readonly class OciSettingsFactory
{
    #[NoDiscard]
    public function create(string $username, string $password, string|null $connectionString, string $encoding, int $sessionMode): OciSettings
    {
        return new OciSettings($username, $password, $connectionString, $encoding, $sessionMode);
    }

    /**
     * @param array<mixed, mixed> $settings
     */
    #[NoDiscard]
    public function createFrom(array $settings): OciSettings
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
