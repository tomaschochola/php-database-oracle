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
use UnexpectedValueException;

use function is_resource;
use function oci_new_connect;

/**
 * @no-named-arguments
 */
readonly class OracleNewConnectFactory
{
    /**
     * @return resource
     */
    #[NoDiscard]
    public function create(OracleSettingsInterface $settings): mixed
    {
        $oracle = oci_new_connect(
            $settings->username,
            $settings->password,
            $settings->connectionString,
            $settings->encoding,
            $settings->sessionMode,
        );

        if (!is_resource($oracle)) {
            throw new UnexpectedValueException('oci_new_connect');
        }

        return $oracle;
    }
}
