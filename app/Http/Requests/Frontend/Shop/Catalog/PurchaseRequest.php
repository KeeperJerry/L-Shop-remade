<?php
declare(strict_types = 1);

namespace app\Http\Requests\Frontend\Shop\Catalog;

use app\Services\Auth\Auth;
use app\Services\Validation\Rule;
use app\Services\Validation\RulesBuilder;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
     * @param Auth       $auth
     * @param Repository $config
     *
     * @return array
     */
    public function rules(Auth $auth, Repository $config): array
    {
        $username = (new RulesBuilder())
            ->addRule(new Rule('min', $config->get('auth.validation.username.min')))
            ->addRule(new Rule('max', $config->get('auth.validation.username.max')));
        if ($auth->check()) {
            $username->addRule(new Rule('nullable'));
        } else {
            $username->addRule(new Rule('required'));
        }

        return [
            'username' => $username->build(),
            'product' => 'required|integer|min:1',
            'amount' => 'required|integer|min:0'
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributes(): array
    {
        return [
            'amount' => __('content.frontend.shop.catalog.purchase.amount')
        ];
    }
}
