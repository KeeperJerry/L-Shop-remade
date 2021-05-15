<?php
declare(strict_types = 1);

namespace app\Handlers\Api\User;

use app\Repository\User\UserRepository;
use app\Services\Auth\Exceptions\UserDoesNotExistException;
use app\Services\Media\Character\Cloak\Applicators\Applicator;
use app\Services\Media\Character\Cloak\Builder;
use app\Services\Media\Character\Cloak\Image as CloakImage;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

// Знакомая библиотека
use Illuminate\Support\Facades\DB;

class CloakHandler
{
    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(ImageManager $imageManager, UserRepository $userRepository)
    {
        $this->imageManager = $imageManager;
        $this->userRepository = $userRepository;
    }

    public function front(string $username): ?Image
    {
        $this->checkUser($username);
        $cloakHash = DB::table('users')->where('username', $username)->value('cloak_hash') ?? '';
        if (!CloakImage::exists($username)) {
            return null;
        }
        $canvas = $this->imageManager->make(CloakImage::absolutePath($cloakHash));

        return $this->builder($canvas)->front(256);
    }

    public function back(string $username): ?Image
    {
        $this->checkUser($username);
        $cloakHash = DB::table('users')->where('username', $username)->value('cloak_hash') ?? '';
        if (!CloakImage::exists($username)) {
            return null;
        }
        $canvas = $this->imageManager->make(CloakImage::absolutePath($cloakHash));

        return $this->builder($canvas)->back(256);
    }

    private function checkUser(string $username)
    {
        if ($this->userRepository->findByUsername($username) === null) {
            throw new UserDoesNotExistException($username);
        }
    }

    private function builder(Image $canvas)
    {
        return new Builder($this->imageManager, app(Applicator::class, [
            'canvas' => $canvas
        ]));
    }
}
