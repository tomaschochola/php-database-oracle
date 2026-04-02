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

namespace TomasChochola\Pdo\Oracle;

use NoDiscard;
use PDO;

use function array_replace;
use function implode;

/**
 * @no-named-arguments
 */
readonly class OracleFactory
{
    #[NoDiscard]
    public function create(OracleSettingsInterface $settings): PDO
    {
        $dsn = [];

        if ($settings->host !== '' && $settings->dbname !== '') {
            $dbname = 'dbname=//' . $settings->host;

            if ($settings->port !== '') {
                $dbname .= ':' . $settings->port;
            }

            $dbname .= '/' . $settings->dbname;

            $dsn[] = $dbname;
        }

        if ($settings->host === '' && $settings->dbname !== '') {
            $dsn[] = 'dbname=' . $settings->dbname;
        }

        $dsn[] = 'charset=AL32UTF8';

        return new PDO('oci:' . implode(';', $dsn), $settings->username, $settings->password, array_replace([
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ], $settings->options));
    }
}
