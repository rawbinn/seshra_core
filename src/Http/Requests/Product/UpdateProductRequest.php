<?php
namespace Seshra\Core\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateProductRequest
 * @package Seshra\Core\Http\Requests\Product
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $locale = $this->lang ?? app()->getLocale();
        return [
            $locale.'.name' => 'required',
            'parent_category' => 'required',
            $locale.'.highlights' => 'required',
            'retail_price' => 'required',
            'tax' => 'required',
            'tax_type' => 'required',
            'discount' => 'required',
            'discount_type' => 'required',
            'stock' => 'required',
            'package_weight' => 'required',
            'package_length' => 'required',
            'package_width' => 'required',
            'package_height' => 'required',
            'warranty_type' => 'required',
            'product_tags' => 'required'
        ];
    }
}
