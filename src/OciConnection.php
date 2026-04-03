<?php

declare(strict_types=1);

namespace TomasChochola\Connection\Oci;

use NoDiscard;

use function is_resource;
use function oci_close;
use function oci_parse;

readonly class OciConnection
{
    /**
     * @var resource
     */
    public readonly mixed $connection;

    public readonly bool $free;

    /**
     * @param resource $connection
     */
    public function __construct(mixed $connection, bool $free = false)
    {
        if (!is_resource($connection)) {
            throw new \UnexpectedValueException('$connection');
        }

        $this->connection = $connection;
        $this->free = $free;
    }

    #[NoDiscard]
    public function statement(string $sql): OciStatement
    {
        $statement = oci_parse($this->connection, $sql);

        if (!is_resource($statement)) {
            throw OciException::error($this->connection);
        }

        return new OciStatement($statement);
    }

    public function __destruct()
    {
        if ($this->free && is_resource($this->connection)) {
            oci_close($this->connection);
        }
    }
}
