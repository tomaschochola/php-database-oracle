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

namespace TomasChochola\Database\Oracle;

use LogicException;
use NoDiscard;

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
    private mixed $connection;

    /**
     * @var object{current: bool}
     */
    private object $free;

    /**
     * @param resource $connection
     */
    public function __construct(mixed $connection, bool $free = false)
    {
        $this->connection = $connection;
        $this->free = (object) ['current' => $free];
    }

    public function __destruct()
    {
        if ($this->free->current && is_resource($this->connection)) {
            oci_close($this->connection);
        }
    }

    /**
     * @param resource $connection
     *
     * @return resource
     */
    #[NoDiscard()]
    public static function oci_parse(mixed $connection, string $sql): mixed
    {
        $parsed = oci_parse($connection, $sql);

        if (!is_resource($parsed)) {
            $error = oci_error($connection);

            if (is_array($error)) {
                throw new OracleException($error);
            }

            throw new LogicException('fatal');
        }

        return $parsed;
    }

    public function free(bool $flag = true): void
    {
        $this->free->current = $flag;
    }

    #[NoDiscard()]
    public function parse(string $sql): OracleStatement
    {
        return new OracleStatement(self::oci_parse($this->connection, $sql));
    }
}
