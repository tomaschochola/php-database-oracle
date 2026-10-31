<?php

declare(strict_types=1);

namespace TomasChochola\Database\Oracle;

use NoDiscard;
use Override;

/**
 * @no-named-arguments
 */
readonly class OracleSettings implements OracleSettingsInterface
{
    #[Override()]
    public string | null $connectionString;

    #[Override()]
    public string $encoding;

    #[Override()]
    public string $password;

    #[Override()]
    public int $sessionMode;

    #[Override()]
    public string $username;

    public function __construct(string $username, string $password, string | null $connectionString, string $encoding, int $sessionMode)
    {
        $this->username = $username;
        $this->password = $password;
        $this->connectionString = $connectionString;
        $this->encoding = $encoding;
        $this->sessionMode = $sessionMode;
    }

    #[NoDiscard()]
    #[Override()]
    public function clone(array $with): static
    {
        return clone ($this, $with);
    }
}
