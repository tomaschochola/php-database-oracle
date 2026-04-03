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
use UnexpectedValueException;

use function is_resource;
use function oci_close;
use function oci_parse;

/**
 * @no-named-arguments
 */
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
            throw new UnexpectedValueException('$connection');
        }

        $this->connection = $connection;
        $this->free = $free;
    }

    public function __destruct()
    {
        if ($this->free && is_resource($this->connection)) {
            oci_close($this->connection);
        }
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
}
