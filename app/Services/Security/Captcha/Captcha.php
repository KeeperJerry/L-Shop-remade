<?php
declare(strict_types = 1);

namespace app\Services\Security\Captcha;

interface Captcha
{
    public function verify(string $code, string $ip): bool;

    public function key(): string;
}
