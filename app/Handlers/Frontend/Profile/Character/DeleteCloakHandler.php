<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Profile\Character;

use app\Services\Auth\Auth;
use app\Services\Media\Character\Cloak\Image;
use Illuminate\Filesystem\Filesystem;

// Знакомая библиотека
use Illuminate\Support\Facades\DB;

class DeleteCloakHandler
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
        $cloakHash = DB::table('users')->where('id', $this->auth->getUser()->getId())->value('cloak_hash');
        if (!Image::exists($cloakHash)) {
            return false;
        }

        return $this->filesystem->delete(Image::absolutePath($cloakHash));
    }
}
