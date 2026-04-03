<?php

declare(strict_types=1);

namespace TomasChochola\Connection\Oci;

use RuntimeException;
use UnexpectedValueException;

use function is_array;
use function is_int;
use function is_string;
use function oci_error;

final class OciException extends RuntimeException
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

    /**
     * @param resource|null $handle
     */
    public static function error(mixed $handle = null): self
    {
        $error = $handle === null ? oci_error() : oci_error($handle);

        if (!is_array($error)) {
            throw new UnexpectedValueException('oci_error');
        }

        return new self($error);
    }
}
