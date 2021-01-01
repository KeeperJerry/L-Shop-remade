<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Users\Edit;

use app\DataTransferObjects\Admin\Users\Edit\RenderResult;
use app\DataTransferObjects\Admin\Users\Edit\User;
use app\Entity\Permission;
use app\Entity\Role;
use app\Exceptions\User\UserNotFoundException;
use app\Repository\Permission\PermissionRepository;
use app\Repository\Role\RoleRepository;
use app\Repository\User\UserRepository;
use app\Services\Auth\Activator;
use app\Services\Auth\Auth;
use app\Services\Auth\BanManager;
use app\Services\Auth\Permissions;
use app\Services\DateTime\Formatting\JavaScriptFormatter;
use app\Services\Media\Character\Cloak\Image as CloakImage;
use app\Services\Media\Character\Skin\Image as SkinImage;

class RenderHandler
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var Activator
     */
    private $activator;

    /**
     * @var BanManager
     */
    private $banManager;

    /**
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * @var PermissionRepository
     */
    private $permissionRepository;

    public function __construct(
        Auth $auth,
        UserRepository $userRepository,
        Activator $activator,
        BanManager $banManager,
        RoleRepository $roleRepository,
        PermissionRepository $permissionRepository)
    {
        $this->auth = $auth;
        $this->userRepository = $userRepository;
        $this->activator = $activator;
        $this->banManager = $banManager;
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * @param int $userId
     *
     * @return RenderResult
     * @throws UserNotFoundException
     */
    public function handle(int $userId): RenderResult
    {
        $user = $this->userRepository->find($userId);
        if ($user === null) {
            throw UserNotFoundException::byId($userId);
        }

        $activation = $this->activator->activation($user);
        $activatedAt = $activation !== null ? $activation->getCompletedAt() : null;

        $cloakExists = CloakImage::exists($user->getUsername());
        $userDTO = (new User($user, $this->banManager))
            ->setSkinFront(route('api.skin.front', ['username' => $user->getUsername()]))
            ->setSkinBack(route('api.skin.back', ['username' => $user->getUsername()]))
            ->setCloakFront(route('api.cloak.front', ['username' => $user->getUsername()]))
            ->setCloakBack(route('api.cloak.back', ['username' => $user->getUsername()]))
            ->setSkinDefault(SkinImage::isDefault($user->getUsername()))
            ->setCloakExists($cloakExists)
            ->setActivatedAt((new JavaScriptFormatter())->format($activatedAt))
            ->setBanned($this->banManager->isBanned($user));

        return (new RenderResult())
            ->setUser($userDTO)
            ->setRoles($this->roles())
            ->setPermissions($this->permissions())
            ->setPurchasesAccess($this->auth->getUser()->hasPermission(Permissions::ADMIN_PURCHASES_ACCESS))
            ->setCanCompletePurchase($this->auth->getUser()->hasPermission(Permissions::ALLOW_COMPLETE_PURCHASES))
            ->setCartAccess($this->auth->getUser()->hasPermission(Permissions::ADMIN_GAME_CART_ACCESS));
    }

    /**
     * @return Role[]
     */
    private function roles(): array
    {
        $roles = [];
        foreach ($this->roleRepository->findByAll() as $role) {
            $roles[] = $role->getName();
        }

        return $roles;
    }

    /**
     * @return Permission[]
     */
    private function permissions(): array
    {
        $permissions = [];
        foreach ($this->permissionRepository->findAll() as $permission) {
            $permissions[] = $permission->getName();
        }

        return $permissions;
    }
}
