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

namespace TomasChochola\Oracle\Database;

use RuntimeException;

use function is_int;
use function is_string;

/**
 * @no-named-arguments
 */
class OracleException extends RuntimeException
{
    /**
     * @var array<mixed, mixed>
     */
    public readonly array $error;

    /**
     * @param array<mixed, mixed> $error
     */
    public function __construct(array $error)
    {
        $message = $error['message'] ?? null;
        $code = $error['code'] ?? null;

        parent::__construct(is_string($message) ? $message : '', is_int($code) ? $code : 0);

        $this->error = $error;
    }
}
