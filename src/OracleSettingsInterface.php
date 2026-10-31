<?php

declare(strict_types=1);

namespace TomasChochola\Database\Oracle;

use NoDiscard;

/**
 * @no-named-arguments
 */
interface OracleSettingsInterface
{
    public string | null $connectionString { get; }

    public string $encoding { get; }

    public string $password { get; }

    public int $sessionMode { get; }

    public string $username { get; }

    /**
     * @param array<mixed, mixed> $with
     */
    #[NoDiscard()]
    public function clone(array $with): static;
}
