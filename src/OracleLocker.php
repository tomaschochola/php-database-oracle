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
use Override;
use PDO;
use PDOStatement;
use TomasChochola\Pdo\LockInterface;
use TomasChochola\Pdo\LockerInterface;
use UnexpectedValueException;

use function is_string;
use function strtoupper;

/**
 * @no-named-arguments
 */
readonly class OracleLocker implements LockerInterface
{
    private readonly PDO $oracle;

    public function __construct(PDO $oracle)
    {
        $this->oracle = $oracle;
    }

    #[NoDiscard]
    #[Override]
    public function lock(string $name, int $wait = 3_600): LockInterface
    {
        $handle = $this->handle($name);
        $status = $this->request($handle, $wait);

        if ($status !== '0') {
            throw new UnexpectedValueException('DBMS_LOCK.REQUEST');
        }

        return new OracleLock($this->oracle, $handle);
    }

    #[NoDiscard]
    #[Override]
    public function try(string $name, int $wait = 0): LockInterface|null
    {
        $handle = $this->handle($name);
        $status = $this->request($handle, $wait);

        if ($status === '0') {
            return new OracleLock($this->oracle, $handle);
        }

        if ($status === '1') {
            return null;
        }

        throw new UnexpectedValueException('DBMS_LOCK.REQUEST');
    }

    #[NoDiscard]
    private function handle(string $name): string
    {
        $stm = $this->oracle->prepare(<<<'SQL'
            BEGIN
                DBMS_LOCK.ALLOCATE_UNIQUE(:name, :handle);
            END;
            SQL);

        if (!$stm instanceof PDOStatement) {
            throw new UnexpectedValueException('prepare');
        }

        $lock = 'APP.' . strtoupper($name);
        $handle = '';

        $ok = $stm->bindParam(':name', $lock, PDO::PARAM_STR);

        if ($ok !== true) {
            throw new UnexpectedValueException('bindParam');
        }

        $ok = $stm->bindParam(':handle', $handle, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 128);

        if ($ok !== true) {
            throw new UnexpectedValueException('bindParam');
        }

        $ok = $stm->execute();

        if ($ok !== true) {
            throw new UnexpectedValueException('execute');
        }

        if (!is_string($handle)) {
            throw new UnexpectedValueException('$handle');
        }

        if ($handle === '') {
            throw new UnexpectedValueException('$handle');
        }

        return $handle;
    }

    #[NoDiscard]
    private function request(string $handle, int $wait): string
    {
        $stm = $this->oracle->prepare(<<<'SQL'
            BEGIN
                :status := DBMS_LOCK.REQUEST(:handle, DBMS_LOCK.X_MODE, :wait, FALSE);
            END;
            SQL);

        if (!$stm instanceof PDOStatement) {
            throw new UnexpectedValueException('prepare');
        }

        $status = '';

        $ok = $stm->bindParam(':handle', $handle, PDO::PARAM_STR);

        if ($ok !== true) {
            throw new UnexpectedValueException('bindParam');
        }

        $ok = $stm->bindParam(':wait', $wait, PDO::PARAM_INT);

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

        if ($status === '') {
            throw new UnexpectedValueException('$status');
        }

        return $status;
    }
}
