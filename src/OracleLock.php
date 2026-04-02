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

use Override;
use PDO;
use PDOStatement;
use TomasChochola\Pdo\LockInterface;
use UnexpectedValueException;

use function is_string;

/**
 * @no-named-arguments
 */
readonly class OracleLock implements LockInterface
{
    private readonly string $handle;

    private readonly PDO $oracle;

    public function __construct(PDO $oracle, string $handle)
    {
        $this->oracle = $oracle;
        $this->handle = $handle;
    }

    #[Override]
    public function unlock(): void
    {
        $stm = $this->oracle->prepare(<<<'SQL'
            BEGIN
                :status := DBMS_LOCK.RELEASE(:handle);
            END;
            SQL);

        if (!$stm instanceof PDOStatement) {
            throw new UnexpectedValueException('prepare');
        }

        $handle = $this->handle;
        $status = '';

        $ok = $stm->bindParam(':handle', $handle, PDO::PARAM_STR);

        if ($ok !== true) {
            throw new UnexpectedValueException('bindParam');
        }

        $ok = $stm->bindParam(':status', $status, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 12);

        if ($ok !== true) {
            throw new UnexpectedValueException('bindParam');
        }

        $ok = $stm->execute();

        if ($ok !== true) {
            throw new UnexpectedValueException('execute');
        }

        if (!is_string($status)) {
            throw new UnexpectedValueException('$status');
        }

        if ($status !== '0') {
            throw new UnexpectedValueException('DBMS_LOCK.RELEASE');
        }
    }
}
