<?php

declare(strict_types=1);

namespace TomasChochola\Database\Oracle;

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
