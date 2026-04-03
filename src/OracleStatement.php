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
        if (!is_resource($statement)) {
            throw new UnexpectedValueException('$statement');
        }

        $this->statement = $statement;
        $this->free = (object) ['current' => $free];
    }

    public function __destruct()
    {
        if ($this->free->current && is_resource($this->statement)) {
            oci_free_statement($this->statement);
        }
    }

    public function bindByName(string $param, mixed &$var, int $max_length = -1, int $type = 0): void
    {
        $ok = oci_bind_by_name($this->statement, str_starts_with($param, ':') ? $param : ':' . $param, $var, $max_length, $type);

        if ($ok !== true) {
            $error = oci_error($this->statement);

            if (is_array($error)) {
                throw new OracleException($error);
            }

            throw new LogicException('fatal');
        }
    }

    public function execute(int $mode = OCI_COMMIT_ON_SUCCESS): void
    {
        $ok = oci_execute($this->statement, $mode);

        if ($ok !== true) {
            $error = oci_error($this->statement);

            if (is_array($error)) {
                throw new OracleException($error);
            }

            throw new LogicException('fatal');
        }
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
            throw new OracleException($error);
        }

        return null;
    }

    public function free(bool $flag = true): void
    {
        $this->free->current = $flag;
    }
}
