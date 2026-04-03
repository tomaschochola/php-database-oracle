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

namespace TomasChochola\Connection\Oci;

use NoDiscard;

/**
 * @no-named-arguments
 */
interface OciSettingsInterface
{
    public string|null $connectionString { get; }

    public string $encoding { get; }

    public string $password { get; }

    public int $sessionMode { get; }

    public string $username { get; }

    /**
     * @param array<mixed, mixed> $with
     */
    #[NoDiscard]
    public function clone(array $with): static;
}
