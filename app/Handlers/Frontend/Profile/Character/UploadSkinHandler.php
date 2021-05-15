<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Profile\Character;

use app\Exceptions\ForbiddenException;
use app\Exceptions\Media\Character\InvalidRatioException;
use app\Exceptions\Media\Character\InvalidResolutionException;
use app\Services\Auth\Auth;
use app\Services\Media\Character\Skin\Accessor;
use app\Services\Media\Character\Skin\Image as SkinImage;
use app\Services\Media\Character\Skin\Resolution;
use app\Services\Validation\SkinValidator;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

// Добавляем класс для обращения в БД (UUID нету в функциях)
use Illuminate\Support\Facades\DB;

class UploadSkinHandler
{
    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * @var Accessor
     */
    private $accessor;

    /**
     * @var Resolution
     */
    private $resolution;

    /**
     * @var SkinValidator
     */
    private $validator;

    /**
     * @var Auth
     */
    private $auth;

    public function __construct(
        ImageManager $imageManager,
        Accessor $accessor,
        Resolution $resolution,
        SkinValidator $validator,
        Auth $auth)
    {
        $this->imageManager = $imageManager;
        $this->accessor = $accessor;
        $this->resolution = $resolution;
        $this->validator = $validator;
        $this->auth = $auth;
    }

    /**
     * @param UploadedFile $file
     *
     * @throws InvalidRatioException
     * @throws InvalidResolutionException
     * @throws FileException
     * @throws ForbiddenException
     */
    public function handle(UploadedFile $file): void
    {
        $image = $this->imageManager->make($file);
        $hash = sha1_file($file->getPathname());

        if ($this->accessor->allowSetHD($this->auth->getUser())) {
            if (!$this->validator->validate($image->width(), $image->height())) {
                throw new InvalidRatioException($image->width(), $image->height());
            }

            if (!$this->resolution->isAny($image)) {
                throw new InvalidResolutionException($image->width(), $image->height());
            }

            $this->move($image, $hash);

            return;
        }

        if ($this->accessor->allowSet($this->auth->getUser())) {
            if (!$this->validator->validate($image->width(), $image->height())) {
                throw new InvalidRatioException($image->width(), $image->height());
            }

            if (!$this->resolution->isSD($image)) {
                throw new InvalidResolutionException($image->width(), $image->height());
            }

            $this->move($image, $hash);

            return;
        }

        throw new ForbiddenException();
    }

    /**
     * @param Image $image
     *
     * @throws FileException
     */
    private function move(Image $image, string $hash): void
    {
        DB::table('users')->where('id', $this->auth->getUser()->getId())->update(['skin_hash' => $hash]);
        $image->save(SkinImage::getAbsolutePath($hash));
    }
}
