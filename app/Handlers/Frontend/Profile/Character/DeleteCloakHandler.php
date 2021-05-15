<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Profile\Character;

use app\Services\Auth\Auth;
use app\Services\Media\Character\Cloak\Image;
use Illuminate\Filesystem\Filesystem;

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
        $cloakHash = $this->auth->getUser()->getCloakHash();
        if (!Image::exists($cloakHash)) {
            return false;
        }

        return $this->filesystem->delete(Image::absolutePath($cloakHash));
    }
}
