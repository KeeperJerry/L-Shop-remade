<?php
declare(strict_types = 1);

namespace Tests\Integrated\Services\Auth;

use app\Services\Auth\Auth;
use app\Services\Auth\Reminder;
use app\Entity\User;
use Tests\TestCase;

class ReminderTest extends TestCase
{
    use AuthTrait;

    public function testComplete(): void
    {
        $this->transaction();
        $this->registerPool([]);
        $auth = $this->app->make(Auth::class);
        $user = new User('D3lph1', 'd3lph1.contact@gmail.com', '123456');
        $auth->register($user);

        $reminder = $this->app->make(Reminder::class);
        $entity = $reminder->makeReminder($user);
        $code = $entity->getCode();
        self::assertTrue($reminder->complete($code, 'qwerty'));

        self::assertFalse($auth->authenticate('D3lph1', '123456', true));
        self::assertTrue($auth->authenticate('D3lph1', 'qwerty', true));

        $this->rollback();
    }
}
