<?php
namespace Seshra\Core\Http\Requests\Attribute;

use Illuminate\Foundation\Http\FormRequest;
use Seshra\User\Facades\Admin;

/**
 * Class StoreProductRequest
 * @package Seshra\Core\Http\Requests\Product
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class StoreAttributeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Admin::hasPermission('catalog.attributes.create');
    }

    /**
     * @return array
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'type' => 'required|in:text,select,swatch',
            'is_filterable' => 'required|in:0,1'
        ];
    }
}
