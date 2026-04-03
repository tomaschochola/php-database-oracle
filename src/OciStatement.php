<?php

declare(strict_types=1);

namespace TomasChochola\Connection\Oci;

use InvalidArgumentException;
use NoDiscard;
use UnexpectedValueException;

use function is_array;
use function is_resource;
use function is_string;
use function oci_bind_array_by_name;
use function oci_bind_by_name;
use function oci_error;
use function oci_execute;
use function oci_fetch_assoc;
use function oci_free_statement;
use function str_starts_with;

final class OciStatement
{
    /**
     * @var resource
     */
    public readonly mixed $statement;

    /**
     * @param resource $statement
     */
    public function __construct(mixed $statement)
    {
        if (!is_resource($statement)) {
            throw new UnexpectedValueException('$statement');
        }

        $this->statement = $statement;
    }

    #[NoDiscard]
    public function bindParam(string $name, mixed &$value): static
    {
        $ok = oci_bind_by_name($this->statement, str_starts_with($name, ':') ? $name : ':' . $name, $value);

        if ($ok !== true) {
            throw OciException::error($this->statement);
        }

        return $this;
    }

    /**
     * @param iterable<string, mixed> $params
     */
    #[NoDiscard]
    public function bindParams(iterable $params): static
    {
        foreach ($params as $name => &$value) {
            if (!is_string($name)) {
                throw new InvalidArgumentException('$params');
            }

            $this->bindParam($name, $value);
        }

        unset($value);

        return $this;
    }

    /**
     * @param array<mixed, mixed> $values
     */
    #[NoDiscard]
    public function bindArray(string $name, array &$values): static
    {
        $ok = oci_bind_array_by_name($this->statement, str_starts_with($name, ':') ? $name : ':' . $name, $values);

        if ($ok !== true) {
            throw OciException::error($this->statement);
        }

        return $this;
    }

    #[NoDiscard]
    public function execute(): static
    {
        $ok = oci_execute($this->statement);

        if ($ok !== true) {
            throw OciException::error($this->statement);
        }

        return $this;
    }

    /**
     * @return array<mixed, mixed>|null
     */
    #[NoDiscard]
    public function fetchAssoc(): array|null
    {
        $row = oci_fetch_assoc($this->statement);

        if (is_array($row)) {
            return $row;
        }

        $error = oci_error($this->statement);

        if (is_array($error)) {
            throw new OciException($error);
        }

        return null;
    }

    public function __destruct()
    {
        if (is_resource($this->statement)) {
            oci_free_statement($this->statement);
        }
    }
}
