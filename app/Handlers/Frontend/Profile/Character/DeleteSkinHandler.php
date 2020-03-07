<?php
declare(strict_types = 1);

namespace App\Handlers\Frontend\Profile\Character;

use App\Services\Auth\Auth;
use App\Services\Media\Character\Skin\Image;
use Illuminate\Filesystem\Filesystem;

// Знакомая библиотека
use Illuminate\Support\Facades\DB;

class DeleteSkinHandler
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Auth $auth, Filesystem $filesystem)
    {
        $this->auth = $auth;
        $this->filesystem = $filesystem;
    }

    public function handle(): bool
    {
		// Почемы бы и нет?
		$usersUUID = DB::table('users')->where('username', $this->auth->getUser()->getUsername())->value('uuid');
        // $username = $this->auth->getUser()->getUsername();
        if (Image::isDefault($usersUUID)) {
            return false;
        }

        return $this->filesystem->delete(Image::absolutePath($usersUUID));
    }
}
