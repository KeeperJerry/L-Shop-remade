<?php
declare(strict_types = 1);

namespace app\Http\Requests\Frontend\Auth;

use app\Services\Validation\Rule;
use app\Services\Validation\RulesBuilder;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @param Repository $config
     *
     * @return array
     */
    public function rules(Repository $config): array
    {
        $usernameRules = (new RulesBuilder())
            ->addRule(new Rule('required'))
            ->addRule(new Rule('string'))
            ->addRule(new Rule('min', $config->get('auth.validation.username.min')))
            ->addRule(new Rule('max', $config->get('auth.validation.username.max')))
            ->build();
        $passwordRules = (new RulesBuilder())
            ->addRule(new Rule('required'))
            ->addRule(new Rule('string'))
            ->addRule(new Rule('min', $config->get('auth.validation.password.min')))
            ->addRule(new Rule('max', $config->get('auth.validation.password.max')))
            ->build();

        return [
            'username' => $usernameRules,
            'password' => $passwordRules,
        ];
    }
}
