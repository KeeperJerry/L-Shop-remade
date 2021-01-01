<?php
declare(strict_types = 1);

namespace app\Handlers\Admin\Control\Security;

use app\DataTransferObjects\Admin\Control\Security\VisitResult;
use app\Services\Settings\DataType;
use app\Services\Settings\Settings;

class VisitHandler
{
    /**
     * @var Settings
     */
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function handle(): VisitResult
    {
        return (new  VisitResult())
            ->setCaptchaEnabled($this->settings->get('system.security.captcha.enabled')->getValue(DataType::BOOL))
            ->setRecaptchaPublicKey($this->settings->get('system.security.captcha.recaptcha.public_key')->getValue())
            ->setRecaptchaSecretKey($this->settings->get('system.security.captcha.recaptcha.secret_key')->getValue())
            ->setResetPasswordEnabled($this->settings->get('auth.reset_password.enabled')->getValue(DataType::BOOL))
            ->setChangePasswordEnabled($this->settings->get('auth.change_password.enabled')->getValue(DataType::BOOL));
    }
}
