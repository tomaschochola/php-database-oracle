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

use LogicException;
use NoDiscard;

use function is_array;
use function is_resource;
use function oci_bind_by_name;
use function oci_error;
use function oci_execute;
use function oci_fetch_assoc;
use function oci_free_statement;
use function str_starts_with;

use const OCI_COMMIT_ON_SUCCESS;

/**
 * @no-named-arguments
 */
readonly class OracleStatement
{
    /**
     * @var object{current: bool}
     */
    private readonly object $free;

    /**
     * @var resource
     */
    private readonly mixed $statement;

    /**
     * @param resource $statement
     */
    public function __construct(mixed $statement, bool $free = false)
    {
        $this->statement = $statement;
        $this->free = (object) ['current' => $free];
    }

    public function __destruct()
    {
        if ($this->free->current && is_resource($this->statement)) {
            oci_free_statement($this->statement);
        }
    }

    /**
     * @param resource $statement
     */
    public static function oci_bind_by_name(mixed $statement, string $param, mixed &$var, int $max_length = -1, int $type = 0): void
    {
        $ok = oci_bind_by_name($statement, str_starts_with($param, ':') ? $param : ':' . $param, $var, $max_length, $type);

        if ($ok !== true) {
            $error = oci_error($statement);

            if (is_array($error)) {
                throw new OracleException($error);
            }

            throw new LogicException('fatal');
        }
    }

    /**
     * @param resource $statement
     */
    public static function oci_execute(mixed $statement, int $mode = OCI_COMMIT_ON_SUCCESS): void
    {
        $ok = oci_execute($statement, $mode);

        if ($ok !== true) {
            $error = oci_error($statement);

            if (is_array($error)) {
                throw new OracleException($error);
            }

            throw new LogicException('fatal');
        }
    }

    /**
     * @param resource $statement
     *
     * @return array<mixed, mixed>|null
     */
    #[NoDiscard]
    public static function oci_fetch_assoc(mixed $statement): array|null
    {
        $row = oci_fetch_assoc($statement);

        if (is_array($row)) {
            return $row;
        }

        $error = oci_error($statement);

        if (is_array($error)) {
            throw new OracleException($error);
        }

        return null;
    }

    public function bindByName(string $param, mixed &$var, int $max_length = -1, int $type = 0): void
    {
        self::oci_bind_by_name($this->statement, $param, $var, $max_length, $type);
    }

    public function execute(int $mode = OCI_COMMIT_ON_SUCCESS): void
    {
        self::oci_execute($this->statement, $mode);
    }

    /**
     * @return array<mixed, mixed>|null
     */
    #[NoDiscard]
    public function fetchAssoc(): array|null
    {
        return self::oci_fetch_assoc($this->statement);
    }

    public function free(bool $flag = true): void
    {
        $this->free->current = $flag;
    }
}
