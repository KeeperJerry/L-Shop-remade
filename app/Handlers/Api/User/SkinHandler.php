<?php
declare(strict_types = 1);

namespace app\Handlers\Api\User;

use app\Repository\User\UserRepository;
use app\Services\Auth\Exceptions\UserDoesNotExistException;
use app\Services\Media\Character\Skin\Applicators\Applicator;
use app\Services\Media\Character\Skin\Builder;
use app\Services\Media\Character\Skin\Image as SkinImage;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

// Знакомая библиотека
use Illuminate\Support\Facades\DB;

class SkinHandler
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

    public function front(string $username): Image
    {
        $this->checkUser($username);
        $skinHash = DB::table('users')->where('username', $username)->value('skin_hash') ?? '';
        $canvas = $this->imageManager->make(SkinImage::absolutePath($skinHash));

        return $this->builder($canvas)->front(256);
    }

    public function back(string $username): Image
    {
        $this->checkUser($username);
        $skinHash = DB::table('users')->where('username', $username)->value('skin_hash') ?? '';
        $canvas = $this->imageManager->make(SkinImage::absolutePath($skinHash));

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
