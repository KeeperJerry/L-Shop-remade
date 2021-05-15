<?php
declare(strict_types = 1);

namespace app\Handlers\Frontend\Profile\Character;

use app\DataTransferObjects\Frontend\Profile\Character\VisitResult;
use app\Services\Auth\Auth;
use app\Services\Media\Character\Cloak\Accessor as CloakAccessor;
use app\Services\Media\Character\Cloak\Image as CloakImage;
use app\Services\Media\Character\Skin\Accessor as SkinAccessor;
use app\Services\Media\Character\Skin\Image as SkinImage;
use app\Services\Settings\DataType;
use app\Services\Settings\Settings;

class VisitHandler
{
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var SkinAccessor
     */
    private $skinAccessor;

    /**
     * @var CloakAccessor
     */
    private $cloakAccessor;

    /**
     * @var Settings
     */
    private $settings;

    public function __construct(Auth $auth, SkinAccessor $skinAccessor, CloakAccessor $cloakAccessor, Settings $settings)
    {
        $this->auth = $auth;
        $this->skinAccessor = $skinAccessor;
        $this->cloakAccessor = $cloakAccessor;
        $this->settings = $settings;
    }

    public function handle(): VisitResult
    {
        if ($this->skinAccessor->allowSetHD($this->auth->getUser())) {
            $availableSkinImageSizes = $this->settings->get('system.profile.character.skin.hd.list')->getValue(DataType::JSON);
        } elseif ($this->skinAccessor->allowSet($this->auth->getUser())) {
            $availableSkinImageSizes = $this->settings->get('system.profile.character.skin.list')->getValue(DataType::JSON);
        } else {
            $availableSkinImageSizes = [];
        }

        if ($this->cloakAccessor->allowSetHD($this->auth->getUser())) {
            $availableCloakImageSizes = $this->settings->get('system.profile.character.cloak.hd.list')->getValue(DataType::JSON);
        } elseif ($this->cloakAccessor->allowSet($this->auth->getUser())) {
            $availableCloakImageSizes = $this->settings->get('system.profile.character.cloak.list')->getValue(DataType::JSON);
        } else {
            $availableCloakImageSizes = [];
        }

        return (new VisitResult())
            ->setAllowSetSkin($this->skinAccessor->allowSet($this->auth->getUser()))
            ->setAllowSetCloak($this->cloakAccessor->allowSet($this->auth->getUser()))
            ->setAvailableSkinImageSizes($availableSkinImageSizes)
            ->setAvailableCloakImageSizes($availableCloakImageSizes)
            ->setSkinDefault(SkinImage::isDefault($this->auth->getUser()->getSkinHash() ?? ''))
            ->setCloakExists(CloakImage::exists($this->auth->getUser()->getCloakHash() ?? ''));
    }
}
