<?php
declare(strict_types=1);

use app\Entity\User;
use app\Repository\Activation\ActivationRepository;
use app\Repository\News\NewsRepository;
use app\Repository\Persistence\PersistenceRepository;
use app\Repository\Reminder\ReminderRepository;
use app\Repository\Role\RoleRepository;
use app\Repository\User\UserRepository;
use app\Services\Auth\Activator;
use app\Services\Auth\Auth;
use app\Services\Auth\Roles;
use Illuminate\Database\Seeder;

class TestsSeeder extends Seeder
{
    public function run(
        Auth $auth,
        Activator $activator,
        RoleRepository $roleRepository,
        UserRepository $userRepository,
        ActivationRepository $activationRepository,
        ReminderRepository $reminderRepository,
        PersistenceRepository $persistenceRepository,
        NewsRepository $newsRepository): void
    {
        $this->call(SettingsSeeder::class);
        $this->call(RolesSeeder::class);

        $activationRepository->deleteAll();
        $reminderRepository->deleteAll();
        $persistenceRepository->deleteAll();
        $newsRepository->deleteAll();
        $userRepository->deleteAll();

        $user = $auth->register(new User('admin', 'admin@example.com', 'admin'));
        $activator->activate($user);

        $adminRole = $roleRepository->findByName(Roles::ADMIN);
        $user->getRoles()->add($adminRole);
        $adminRole->addUser($user);
        $userRepository->update($user);
        $roleRepository->update($adminRole);
    }
}
