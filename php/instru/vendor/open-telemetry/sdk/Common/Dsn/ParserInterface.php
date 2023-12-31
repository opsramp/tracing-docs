<?php

declare(strict_types=1);

namespace OpenTelemetry\SDK\Common\Dsn;

/**
 * @deprecated
 */
interface ParserInterface
{
    public function parse(string $dsn): DsnInterface;

    public function parseToArray(string $dsn): array;
}
