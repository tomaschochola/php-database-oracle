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

use function is_resource;
use function oci_connect;
use function oci_new_connect;
use function oci_pconnect;

/**
 * @no-named-arguments
 */
readonly class OciConnectionFactory
{
    #[NoDiscard]
    public function create(OciSettingsInterface $settings, bool $free = false): OciConnection
    {
        $oracle = oci_connect($settings->username, $settings->password, $settings->connectionString, $settings->encoding, $settings->sessionMode);

        if (!is_resource($oracle)) {
            throw OciException::error();
        }

        return new OciConnection($oracle, $free);
    }

    #[NoDiscard]
    public function createNew(OciSettingsInterface $settings, bool $free = false): OciConnection
    {
        $oracle = oci_new_connect($settings->username, $settings->password, $settings->connectionString, $settings->encoding, $settings->sessionMode);

        if (!is_resource($oracle)) {
            throw OciException::error();
        }

        return new OciConnection($oracle, $free);
    }

    #[NoDiscard]
    public function createPersistent(OciSettingsInterface $settings, bool $free = false): OciConnection
    {
        $oracle = oci_pconnect($settings->username, $settings->password, $settings->connectionString, $settings->encoding, $settings->sessionMode);

        if (!is_resource($oracle)) {
            throw OciException::error();
        }

        return new OciConnection($oracle, $free);
    }
}
