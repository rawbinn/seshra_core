<?php

namespace Seshra\Core\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Seshra\Core\Eloquent\Repository;
use Illuminate\Container\Container as App;
use Seshra\Core\Exceptions\GeneralException;
use Seshra\Core\Traits\SlugTrait;

/**
 * Class ProductRepository
 * @package Seshra\Core\Repositories
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class ProductRepository extends Repository
{
    use SlugTrait;
    
    protected $categoryRepository;
    
    protected $brandRepository;

    /**
     * Create a new repository instance.
     *
     * @param \Illuminate\Container\Container $app
     *
     * @return void
     */
    public function __construct(
        App $app,
        CategoryRepository $categoryRepository,
        BrandRepository $brandRepository
    )
    {
        $this->categoryRepository = $categoryRepository;
        $this->brandRepository = $brandRepository;
        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return 'Seshra\Core\Contracts\Product';
    }

    public function getMyProducts($orderBy = 'DESC', $paginate = 20)
    {
        return $this->model->where('user_id', current_user()->id)->where('digital', 0)->orderBy('id', $orderBy)->paginate($paginate);
    }

    public function create(array $attributes)
    {
        DB::beginTransaction();
        try {
            $productData = [
                'user_id' => current_user()->id,
                'sku' => $this->slug(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $attributes['name']))),
                'slug' => $this->slug(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $attributes['name']))),
                'video_url' => $attributes['video_url'],
                'brand_id' => $attributes['brand'],
                'retail_price' => $attributes['retail_price'],
                'discount' => $attributes['discount'],
                'discount_type' => $attributes['discount_type'],
                'stock' => $attributes['stock'],
                'warranty_id' => $attributes['warranty_type'],
                'warranty_period_id' => $attributes['warranty_period'],
                'meta_title' => $attributes['meta_title'],
                'meta_description' => $attributes['meta_description'],
                'meta_image' => $attributes['meta_image']
            ];

            $model = app()->make($this->model());
            foreach (locales() as $locale) {
                foreach ($model->translatedAttributes as $attribute) {
                    if (isset($attributes[$attribute])) {
                        $productData[$locale->code][$attribute] = $attributes[$attribute];
                    }
                }
            }
            $product = parent::create($productData);

            if(isset($attributes['custom_inputs'])) {
                foreach($attributes['custom_inputs'] as $input_key => $custom_input) {
                    if($attributes['custom_input_type'][$input_key] == 'select') {
                        $product->attributeOptions()->create(['attribute_id' => $input_key, 'attribute_option_id' => $custom_input]);
                    }else{
                        $product->attributeOptions()->create(['attribute_id' => $input_key, 'text' => $custom_input]);
                    }
                }
            }

            $category = $this->categoryRepository->findOrFail($attributes['parent_category']);
            $categories = $category->parents()->pluck('id')->toArray();
            array_push($categories, $attributes['parent_category']);
            $product->categories()->sync($categories);

            $product->dimension()->create(['weight' => $attributes['package_weight'], 'length' => $attributes['package_length'], 'width' => $attributes['package_width'], 'height' => $attributes['package_height']]);
            if($attributes['images'] !='') {
                $images_ids = explode(',', $attributes['images']);
                $product->images()->attach(combine_pivot($images_ids, ['type' => 1]));
            }
            if($attributes['thumbnails'] !='') {
                $images_ids = explode(',', $attributes['thumbnails']);
                $product->images()->attach(combine_pivot($images_ids, ['type' => 0]));
            }
            $tags = collect(json_decode($attributes['product_tags'], true));
            if($tags->count() > 0)
                $product->tag()->create(['tags' => $tags->pluck('value')->implode(',')]);
            
            if(isset($attributes['variants'])) {
                foreach($attributes['variants'] as $key => $value) {
                    $name = $product->name. ' '.$value['sku'];
                    $product->variations()->create(['variant' => $key, 'name' => $name, 'sku' => $value['sku'], 'retail_price' => $value['retail_price'], 'discount' => $value['discount'], 'stock' => $value['stock'], 'image' => $value['image']]);
                }
            }
        }catch(\Exception $e) {
            DB::rollBack();
            throw new GeneralException($e->getMessage());
        }
        DB::commit();
    }

    /**
     * @param array $attributes
     * @param mixed $id
     * @return mixed
     * @throws ValidatorException
     */
    public function modify(array $attributes, $id)
    {
        DB::beginTransaction();
        try {
            // 'sku' => $this->slug(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $attributes['name']))),
            // 'slug' => $this->slug(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $attributes['name']))),
       
            $attributes['brand_id'] = $attributes['brand'];
            $attributes['warranty_id'] = $attributes['warranty_type'];
            $attributes['warranty_period_id'] = $attributes['warranty_period'];
            unset($attributes['brand'], $attributes['warranty_type'], $attributes['warranty_period']);
            
            $product = parent::update($attributes, decrypt($id));
            
            // $category = $this->categoryRepository->findOrFail($attributes['parent_category']);
            // $categories = $category->parents()->pluck('id')->toArray();
            // array_push($categories, $attributes['parent_category']);
            // $product->categories()->sync($categories);

            if(isset($attributes['custom_inputs'])) {
                $product->attributeOptions()->delete();
                foreach($attributes['custom_inputs'] as $input_key => $custom_input) {
                    if($attributes['custom_input_type'][$input_key] == 'select') {
                        $product->attributeOptions()->create(['attribute_id' => $input_key, 'attribute_option_id' => $custom_input]);
                    }else{
                        $product->attributeOptions()->create(['attribute_id' => $input_key, 'text' => $custom_input]);
                    }
                }
            }

            $product->dimension()->update(['weight' => $attributes['package_weight'], 'length' => $attributes['package_length'], 'width' => $attributes['package_width'], 'height' => $attributes['package_height']]);

            if($attributes['images'] !='') {
                $images_ids = explode(',', $attributes['images']);
                $product->images()->sync(combine_pivot($images_ids, ['type' => 1]));
            }
            if($attributes['thumbnails'] !='') {
                $images_ids = explode(',', $attributes['thumbnails']);
                $product->thumbnails()->sync(combine_pivot($images_ids, ['type' => 0]));
            }
            $tags = collect(json_decode($attributes['product_tags'], true));
            if($tags->count() > 0)
                $product->tag()->update(['tags' => $tags->pluck('value')->implode(',')]);
            
            $product->variations()->delete();
            if(isset($attributes['variants'])) {
                foreach($attributes['variants'] as $key => $variant) {
                    $name = $product->name. ' '.$variant['sku'];
                    $product->variations()->create(['name' => $name, 'variant' => $key, 'sku' => $variant['sku'], 'retail_price' => $variant['retail_price'], 'stock' => $variant['stock'] ]);
                }
            }
        }catch(\Exception $e) {
            DB::rollBack();
            throw new GeneralException($e->getMessage());
        }
        DB::commit();
    }

    public function duplicate($id)
    {
        $product = $this->model->findOrFail($id);
        $duplicate_product = $product->replicate();
        $duplicate_product->slug = $this->slug($product->slug);
        $duplicate_product->sku = $this->sku($product->sku);
        $duplicate_product->push();
        $product->relations = [];
        $product->load("categories","images","thumbnails","dimension","tag","variations","attributeOptions", "translations");
        $relations = $product->getRelations();
        foreach ($relations as $relation_key => $relation) {
            if($relation instanceof Collection) {
                foreach ($relation as $relationRecord) {
                    if($relation_key == 'variations' || $relation_key == 'attributeOptions' || $relation_key == 'translations') {
                        $newRelationship = $relationRecord->replicate();
                        if($relation_key == 'variations') {
                            $newRelationship->sku = $this->sku($newRelationship->sku, 'variations');
                        }
                        $newRelationship->product_id = $duplicate_product->id;
                        $newRelationship->push();
                    }
                    elseif($relation_key == 'categories') {
                        $duplicate_product->categories()->attach($relationRecord);
                    }
                    elseif($relation_key == 'images') {
                        $duplicate_product->images()->attach($relationRecord, ['type' => 1]);
                    }
                    elseif($relation_key == 'thumbnails') {
                        $duplicate_product->thumbnails()->attach($relationRecord, ['type' => 0]);
                    }
                }
            }
            else {
                $newRelation = $relation->replicate();
                $newRelation->product_id = $duplicate_product->id;
                $newRelation->push();
            }
        }
        try{
            $duplicate_product->push();

        }catch(\Exception $e){

        }
    }

    public function delete($id)
    {
        $product = $this->findOrFail($id);
        try{
            parent::delete($id);
            $product->deleteTranslations();
            $product->categories()->detach();
            $product->images()->detach();
            $product->thumbnails()->detach();
            $product->dimension()->delete();
            $product->tag()->delete();
            $product->variations()->delete();
            $product->attributeOptions()->delete();
            return true;
        }
        catch(\Exception $e) {
            throw new GeneralException($e->getMessage());
        }
    }

    private function sku($sku, $type = null)
    {
        if($type == 'variations') {
            $skuCount = DB::table('product_variations')->where('sku', $sku)->count();
        }
        else {
            $skuCount = $this->model->where('sku', $sku)->count();
        }
        if($skuCount > 0) {
            $update_sku = $sku.'-'.mt_rand(1,9);
            return $this->sku($update_sku, $type);
        }
        return strtolower($sku);
    }

    public function searchTag($tag)
    {
        $tagsArray = [];
        $tags = DB::table('product_tags')->select('tags')->where('tags', 'like', '%'.$tag.'%')->take(10)->pluck('tags')->toArray();
        foreach($tags as $values) {
           $tagsArray = array_merge(explode(',',$values), $tagsArray);
        }
        $tag = preg_quote($tag, '~');
        $tagsArray = preg_grep('~'.$tag.'~', $tagsArray);
        return array_values($tagsArray);
    }

    public function search($inputs, $paginate = 10)
    {
        $products = $this->model;
        if($inputs['brand'] != '') {
            $brand_ids = $this->brandRepository->select('id')->whereTranslationLike('name', $inputs['brand'].'%')->take(5)->pluck('id')->toArray();
            $products = $products->whereIn('brand_id', $brand_ids);
        }
        if($inputs['name'] != '') {
            $products = $products->whereTranslationLike('name', $inputs['name'].'%');
        }
        if($inputs['sku'] != '') {
            $products = $products->where('sku', $inputs['sku']);
        }
        $products = filter('PRODUCT_SEARCH_QUERY', $products);
        return $products->paginate($paginate);
    }

    public function searchMyProducts($inputs, $paginate = 10)
    {
        $products = $this->model->where('user_id', current_user()->id);
        if($inputs['brand'] != '') {
            $brand_ids = $this->brandRepository->select('id')->whereTranslationLike('name', $inputs['brand'].'%')->take(5)->pluck('id')->toArray();
            $products = $products->whereIn('brand_id', $brand_ids);
        }
        if($inputs['name'] != '') {
            $products = $products->whereTranslationLike('name', $inputs['name'].'%');
        }
        if($inputs['sku'] != '') {
            $products = $products->where('sku', $inputs['sku']);
        }
        return $products->paginate($paginate);
    }
}