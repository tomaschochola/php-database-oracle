<?php

declare(strict_types=1);

namespace TomasChochola\Database\Oracle;

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
readonly class OracleDatabase
{
    private OracleSettingsInterface $settings;

    public function __construct(OracleSettingsInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return resource
     */
    #[NoDiscard()]
    public static function oci_connect(string $username, string $password, string | null $connection_string = null, string $encoding = '', int $session_mode = OCI_DEFAULT): mixed
    {
        $oracle = oci_connect($username, $password, $connection_string, $encoding, $session_mode);

        if (!is_resource($oracle)) {
            $error = oci_error();

            if (is_array($error)) {
                throw new OracleException($error);
            }

            throw new LogicException('fatal');
        }

        return $oracle;
    }

    /**
     * @return resource
     */
    #[NoDiscard()]
    public static function oci_new_connect(string $username, string $password, string | null $connection_string = null, string $encoding = '', int $session_mode = OCI_DEFAULT): mixed
    {
        $oracle = oci_new_connect($username, $password, $connection_string, $encoding, $session_mode);

        if (!is_resource($oracle)) {
            $error = oci_error();

            if (is_array($error)) {
                throw new OracleException($error);
            }

            throw new LogicException('fatal');
        }

        return $oracle;
    }

    /**
     * @return resource
     */
    #[NoDiscard()]
    public static function oci_pconnect(string $username, string $password, string | null $connection_string = null, string $encoding = '', int $session_mode = OCI_DEFAULT): mixed
    {
        $oracle = oci_pconnect($username, $password, $connection_string, $encoding, $session_mode);

        if (!is_resource($oracle)) {
            $error = oci_error();

            if (is_array($error)) {
                throw new OracleException($error);
            }

            throw new LogicException('fatal');
        }

        return $oracle;
    }

    #[NoDiscard()]
    public function connect(): OracleConnection
    {
        return new OracleConnection(self::oci_connect($this->settings->username, $this->settings->password, $this->settings->connectionString, $this->settings->encoding, $this->settings->sessionMode));
    }

    #[NoDiscard()]
    public function connectNew(): OracleConnection
    {
        return new OracleConnection(self::oci_new_connect($this->settings->username, $this->settings->password, $this->settings->connectionString, $this->settings->encoding, $this->settings->sessionMode));
    }

    #[NoDiscard()]
    public function connectPersistent(): OracleConnection
    {
        return new OracleConnection(self::oci_pconnect($this->settings->username, $this->settings->password, $this->settings->connectionString, $this->settings->encoding, $this->settings->sessionMode));
    }
}
