<?php
namespace Seshra\Core\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreProductRequest
 * @package Seshra\Core\Http\Requests\Product
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class StoreProductRequest extends FormRequest
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
     * @return array
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'parent_category' => 'required',
            'highlights' => 'required',
            'retail_price' => 'required',
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
