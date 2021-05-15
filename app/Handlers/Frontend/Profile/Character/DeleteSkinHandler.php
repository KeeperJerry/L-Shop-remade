<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Profile\Character;

use app\Services\Auth\Auth;
use app\Services\Media\Character\Skin\Image;
use Illuminate\Filesystem\Filesystem;

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
        $skinHash = $this->auth->getUser()->getSkinHash();
        if (Image::isDefault($skinHash)) {
            return false;
        }

        return $this->filesystem->delete(Image::absolutePath($skinHash));
    }
}
