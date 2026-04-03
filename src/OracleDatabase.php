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

use function is_array;
use function is_resource;
use function oci_connect;
use function oci_error;
use function oci_new_connect;
use function oci_pconnect;

use const OCI_DEFAULT;

/**
 * @no-named-arguments
 */
class OracleDatabase
{
    #[NoDiscard]
    public function connect(string $username, string $password, string|null $connection_string = null, string $encoding = '', int $session_mode = OCI_DEFAULT): OracleConnection
    {
        $oracle = oci_connect($username, $password, $connection_string, $encoding, $session_mode);

        if (!is_resource($oracle)) {
            $error = oci_error();

            if (is_array($error)) {
                throw new OracleException($error);
            }

            throw new LogicException('fatal');
        }

        return new OracleConnection($oracle);
    }

    #[NoDiscard]
    public function newConnect(string $username, string $password, string|null $connection_string = null, string $encoding = '', int $session_mode = OCI_DEFAULT): OracleConnection
    {
        $oracle = oci_new_connect($username, $password, $connection_string, $encoding, $session_mode);

        if (!is_resource($oracle)) {
            $error = oci_error();

            if (is_array($error)) {
                throw new OracleException($error);
            }

            throw new LogicException('fatal');
        }

        return new OracleConnection($oracle);
    }

    #[NoDiscard]
    public function pconnect(string $username, string $password, string|null $connection_string = null, string $encoding = '', int $session_mode = OCI_DEFAULT): OracleConnection
    {
        $oracle = oci_pconnect($username, $password, $connection_string, $encoding, $session_mode);

        if (!is_resource($oracle)) {
            $error = oci_error();

            if (is_array($error)) {
                throw new OracleException($error);
            }

            throw new LogicException('fatal');
        }

        return new OracleConnection($oracle);
    }
}
