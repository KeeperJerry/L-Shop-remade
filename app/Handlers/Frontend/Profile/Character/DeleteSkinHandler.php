<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Profile\Character;

use app\Services\Auth\Auth;
use app\Services\Media\Character\Skin\Image;
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
        $skinHash = DB::table('users')->where('id', $this->auth->getUser()->getId())->value('skin_hash');
        if (Image::isDefault($skinHash)) {
            return false;
        }

        return $this->filesystem->delete(Image::absolutePath($skinHash));
    }
}
