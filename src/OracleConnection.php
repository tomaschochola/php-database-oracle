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

namespace TomasChochola\Connection\Oracle;

use LogicException;
use NoDiscard;
use UnexpectedValueException;

use function is_array;
use function is_resource;
use function oci_close;
use function oci_error;
use function oci_parse;

/**
 * @no-named-arguments
 */
readonly class OracleConnection
{
    /**
     * @var resource
     */
    private readonly mixed $connection;

    /**
     * @var object{current: bool}
     */
    private readonly object $free;

    /**
     * @param resource $connection
     */
    public function __construct(mixed $connection, bool $free = false)
    {
        if (!is_resource($connection)) {
            throw new UnexpectedValueException('$connection');
        }

        $this->connection = $connection;
        $this->free = (object) ['current' => $free];
    }

    public function __destruct()
    {
        if ($this->free->current && is_resource($this->connection)) {
            oci_close($this->connection);
        }
    }

    public function free(bool $flag = true): void
    {
        $this->free->current = $flag;
    }

    #[NoDiscard]
    public function parse(string $sql): OracleStatement
    {
        $statement = oci_parse($this->connection, $sql);

        if (!is_resource($statement)) {
            $error = oci_error($this->connection);

            if (is_array($error)) {
                throw new OracleException($error);
            }

            throw new LogicException('fatal');
        }

        return new OracleStatement($statement);
    }
}
