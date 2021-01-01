<?php
declare(strict_types = 1);

namespace app\Services\Meta\AdditionalVersion;

interface AdditionalVersion
{
    public function number(): int;

    public function formatted(): string;
}
