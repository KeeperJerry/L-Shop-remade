<?php
declare(strict_types = 1);

namespace app\Http\Requests\Frontend\Shop;

use app\Services\Settings\DataType;
use app\Services\Settings\Settings;
use app\Services\Validation\Rule;
use app\Services\Validation\RulesBuilder;
use Illuminate\Foundation\Http\FormRequest;

class BalanceReplenishmentRequest extends FormRequest
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
     * @param Settings $settings
     *
     * @return array
     */
    public function rules(Settings $settings): array
    {
        return [
            'sum' => (new RulesBuilder())
                ->addRule(new Rule('required'))
                ->addRule(new Rule('numeric'))
                ->addRule(
                    new Rule(
                        'min',
                        $settings
                            ->get('purchasing.min_fill_balance_sum')
                            ->getValue(DataType::FLOAT))
                )
                ->build()
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributes(): array
    {
        return [
            'sum' => __('content.frontend.shop.replenishment.sum')
        ];
    }
}
