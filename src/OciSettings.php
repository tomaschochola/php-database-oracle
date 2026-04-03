<?php

declare(strict_types=1);

namespace TomasChochola\Connection\Oci;

use NoDiscard;
use Override;

readonly class OciSettings implements OciSettingsInterface
{
    #[Override]
    public readonly string|null $connectionString;

    #[Override]
    public readonly string $encoding;

    #[Override]
    public readonly string $password;

    #[Override]
    public readonly int $sessionMode;

    #[Override]
    public readonly string $username;

    public function __construct(string $username, string $password, string|null $connectionString, string $encoding, int $sessionMode)
    {
        $this->username = $username;
        $this->password = $password;
        $this->connectionString = $connectionString;
        $this->encoding = $encoding;
        $this->sessionMode = $sessionMode;
    }

    #[NoDiscard]
    #[Override]
    public function clone(array $with): static
    {
        return clone ($this, $with);
    }
}
