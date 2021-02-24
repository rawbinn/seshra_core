<?php

namespace Seshra\Core\Http\Controllers;

use Illuminate\Http\Request;
use Seshra\Core\Models\AttributeSet;
use Illuminate\Support\Facades\Validator;
use Seshra\Core\Repositories\BrandRepository;
use Seshra\Core\Repositories\ColorRepository;
use Seshra\Core\Repositories\ProductRepository;
use Seshra\Core\Repositories\CategoryRepository;
use Seshra\Core\Repositories\WarrantyRepository;
use Seshra\Core\Repositories\AttributeRepository;
use Seshra\Core\Repositories\WarrantyPeriodRepository;
use Seshra\Core\Repositories\AttributeOptionRepository;
use Seshra\Core\Http\Requests\Product\StoreProductRequest;
use Seshra\Core\Http\Requests\Product\UpdateProductRequest;

/**
 * Class ProductController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class ProductController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_routes;

    /**
     * @var BrandRepository
     */
    protected $brandRepository;

    /**
     * @var ColorRepository
     */
    protected $colorRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * @var AttributeOptionRepository
     */
    protected $attributeOptionRepository;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var WarrantyRepository
     */
    protected $warrantyRepository;

    /**
     * @var WarrantyPeriodRepository
     */
    protected $warrantyPeriodRepository;

    /**
     * ProductController constructor.
     * @param BrandRepository $brandRepository
     * @param ColorRepository $colorRepository
     * @param ProductRepository $productRepository
     * @param CategoryRepository $categoryRepository
     * @param WarrantyRepository $warrantyRepository
     * @param AttributeRepository $attributeRepository
     * @param WarrantyPeriodRepository $warrantyPeriodRepository
     * @param AttributeOptionRepository $attributeOptionRepository
     */
    public function __construct(
        BrandRepository $brandRepository,
        ColorRepository $colorRepository,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        WarrantyRepository $warrantyRepository,
        AttributeRepository $attributeRepository,
        WarrantyPeriodRepository $warrantyPeriodRepository,
        AttributeOptionRepository $attributeOptionRepository
    ){
        $this->middleware('seshra:admin');
        $this->_routes = request('_routes');
        $this->brandRepository = $brandRepository;
        $this->colorRepository = $colorRepository;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->warrantyRepository = $warrantyRepository;
        $this->attributeRepository = $attributeRepository;
        $this->warrantyPeriodRepository = $warrantyPeriodRepository;
        $this->attributeOptionRepository = $attributeOptionRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function index(Request $request)
    {
        if($request->has('name') && $request->has('sku') && $request->has('brand')) {
            $products = $this->productRepository->search($request->only('name','sku','brand'));
        }else {
            $products = $this->productRepository->where('digital', 0)->orderBy('id', 'DESC')->paginate(15);
        }

        return view($this->_routes['view'], compact('products'));
    }

    /**
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function create()
    {
        $categories = $this->categoryRepository->where('parent_id', 0)
            ->where('digital', 0)
            ->get();
        return view($this->_routes['view'], compact('categories'));
    }

    /**
     * @param StoreProductRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function store(StoreProductRequest $request)
    {
        $this->productRepository->create($request->only('name','parent_category','video_url','brand','highlights','tags','highlights','long_description','retail_price','tax','tax_type','discount','discount_type','stock','images', 'thumbnails','custom_inputs', 'custom_input_type', 'attributes','options','variants','warranty_type','warranty_period','package_weight','package_length','package_width','package_height','meta_title','meta_description','meta_image','product_tags'));

        flash(translate('New product has been added successfully.'))->success();
        return redirect()->route($this->_routes['redirect']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function edit(Request $request, $id)
    {
        $lang = lang($request->lang);
        $categories = $this->categoryRepository->where('parent_id', 0)
            ->where('digital', 0)
            ->get();
        $product = $this->productRepository->findOrFail(decrypt($id));
        $parents = $product->categories()->pluck('categories.id')->toArray();
        $brands = $this->brandRepository->all();
        $colors = $this->colorRepository->all();
        $warranties = $this->warrantyRepository->all();
        $warranty_periods = $this->warrantyPeriodRepository->all();
        $attributes = AttributeSet::where('category_id', $product->category->id)->whereHas('attribute', function($query) {
            $query->where('type', 'swatch');
        })->get();
        $attributes = $this->attributeRepository->findWhereIn('id', $attributes->pluck('attribute_id')->toArray());
        $selected_attributes = [];
        $custom_inputs = AttributeSet::where('category_id', $product->category->id)->whereHas('attribute', function($query) {
            $query->where('type', '<>', 'swatch');
        })->get();
        if($product->variations) {
            $selected_attribute_ids = [];
            $selected_attribute_options = [];
            foreach($product->variations as $variation) {
                $option_ids = explode('-', $variation->variant);
                foreach($option_ids as $oid) {
                    $attribute_option = $this->attributeOptionRepository->find($oid);
                    $selected_attribute_ids[] = $attribute_option->attribute_id;
                    $selected_attribute_options[$attribute_option->attribute_id][$attribute_option->id] = $attribute_option->name;
                }
            }
            $selected_attribute_ids = array_unique($selected_attribute_ids);
            $selected_attributes = $this->attributeRepository->findWhereIn('id', $selected_attribute_ids);
        }
        $product_attribute_options = [];
        if($product->attributeOptions) {
            foreach($product->attributeOptions as $attribute_option) {
                $product_attribute_options[$attribute_option->attribute_id] = ($attribute_option->attribute_option_id) ?? $attribute_option->text;
            }
        }
        return view($this->_routes['view'], compact('categories', 'product', 'parents','brands','colors','warranties','warranty_periods','attributes','custom_inputs', 'selected_attribute_ids', 'selected_attributes', 'selected_attribute_options', 'product_attribute_options', 'lang'));
    }

    /**
     * @param UpdateProductRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $locale = $request->lang ?? app()->getLocale();
        $this->productRepository->modify($request->only($locale,'parent_category','video_url','brand','highlights','tags','highlights','long_description','retail_price','tax','tax_type','discount','discount_type','stock','images', 'thumbnails','custom_inputs','custom_input_type', 'attributes','options','variants','warranty_type','warranty_period','package_weight','package_length','package_width','package_height','meta_title','meta_description','meta_image','product_tags'), $id);

        flash(translate('Product has been updtaed successfully.'))->success();
        return redirect()->route($this->_routes['redirect']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function destroy($id)
    {
        $this->productRepository->delete(decrypt($id));
        flash(translate('Product has been deleted successfully'))->success();
        return redirect()->route($this->_routes['redirect']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function duplicate(Request $request, $id)
    {
        if($request->token == csrf_token()){
            $this->productRepository->duplicate(decrypt($id));
            flash(translate('Product has been duplicated successfully'))->success();
        }
        else{
            flash(translate('Token mismatch. Refresh page and try again.'))->error();
        }
        
        return redirect()->route($this->_routes['redirect']);
    }

    /**
     * Ajax Call
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function status(Request $request)
    {
        $product = $this->productRepository->update($request->only('status'), decrypt($request->id));
        return response()->json(true);
    }

    /**
     * Ajax Call
     * 
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function variations(Request $request)
    {
        $options = array();
        if($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0){
            $colors_active = 1;
            array_push($options, $request->colors);
        }
        else {
            $colors_active = 0;
        }

        $retail_price = $request->retail_price;
        $product_name = $request->name;
        if($request->has('attributes')) {
            foreach ($request->input('attributes') as $attribute_id) {
                $data = [];
                if(isset($request->options[$attribute_id]) && json_decode($request->options[$attribute_id])) {
                    foreach (json_decode($request->options[$attribute_id]) as $option) {
                        $att_option = $this->optionRepository->whereTranslation('name', $option->value)->first();
                        array_push($data, $att_option->id);
                    }
                    array_push($options, $data);
                }
            }
        }
        if($colors_active == 1){
            $selected_attributes = count($options) - 1;
        }
        else{
            $selected_attributes = count($options);
        }
        $attributes_count = $request->input('attributes') ? count($request->input('attributes')) : 0;
        $has_filled_attribtues = ($selected_attributes == $attributes_count);
        
        if($request->input('attributes') == null || count($options) == 0 || !$has_filled_attribtues){
            return '';
        }
        $combinations = combinations($options);
        return view('admin::catalog.products.sku_combinations', compact('combinations', 'retail_price', 'colors_active', 'product_name'));
    }

    /**
     * Ajax Call
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function variations_edit(Request $request)
    {
        $product = $this->productRepository->findOrFail($request->id);
        $options = array();
        if($request->has('colors_active') && $request->has('colors') && count($request->colors) > 0){
            $colors_active = 1;
            array_push($options, $request->colors);
        }
        else {
            $colors_active = 0;
        }

        $retail_price = $request->retail_price;
        $product_name = $request->name;
        if($request->has('options')) {
            foreach ($request->input('options') as $attribute_id => $value) {
                $data = [];
                if(isset($request->options[$attribute_id]) && json_decode($request->options[$attribute_id])) {
                    foreach (json_decode($request->options[$attribute_id]) as $option) {
                        $att_option = $this->optionRepository->whereTranslation('name', $option->value)->first();
                        array_push($data, $att_option->id);
                    }
                    array_push($options, $data);
                }
            }
        }
        if($colors_active == 1){
            $selected_attributes = count($options) - 1;
        }
        else{
            $selected_attributes = count($options);
        }
        $attributes_count = $request->input('choice') ? count($request->input('choice')) : 0;
        $has_filled_attribtues = ($selected_attributes == $attributes_count);
        
        if($request->input('choice') == null || count($options) == 0 || !$has_filled_attribtues){
            return '';
        }
        $combinations = combinations($options);
        
        return view('admin::catalog.products.sku_combinations_edit', compact('combinations', 'retail_price', 'colors_active', 'product_name', 'product'));
    }

    /**
     * Ajax Call
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function product_attributes(Request $request)
    {
        $category = Category::find($request->category_id);
        if($category) {
            if($category->attributes){
                $attributes = $category->attributes()->select('attributes.id','attributes.name')->where('type', 'swatch')->orderBy('sort','ASC')->get()->toArray();
                return response()->json($attributes);
            }
        }
        return response()->json([]);
    }

    /**
     * Ajax Call
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function loadForm(Request $request)
    {
        $categories = $this->categoryRepository->getAllWithChildren();
        $brands = $this->brandRepository->all();
        $colors = $this->colorRepository->all();
        $inputs = [
            'name' => $request->name,
            'category' => $request->category
        ];
        $custom_inputs = AttributeSet::where('category_id', $request->category)->whereHas('attribute', function($query) {
            $query->where('type', '<>', 'swatch');
        })->get();
        $attributes = AttributeSet::where('category_id', $request->category)->whereHas('attribute', function($query) {
            $query->where('type', 'swatch');
        })->get();
        $warranties = $this->warrantyRepository->all();
        $warranty_periods = $this->warrantyPeriodRepository->all();
        return response()->json(view('admin::catalog.products.partials.form', compact('categories','brands','colors', 'inputs', 'custom_inputs','attributes','warranties', 'warranty_periods'))->render());
    }

    /**
     * Ajax Call
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function getTags(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['status' => false, 'message' => $errors[0]]);
        }
        return response()->json(['status' => true, 'data' => $this->productRepository->searchTag($request->tag)]);
    }

}
