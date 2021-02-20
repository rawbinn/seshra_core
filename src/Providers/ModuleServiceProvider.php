<?php

namespace Seshra\Core\Providers;

use Seshra\Core\Models\Brand;
use Seshra\Core\Models\Color;
use Seshra\Core\Models\Locale;
use Seshra\Core\Models\Order;
use Seshra\Core\Models\Product;
use Seshra\Core\Models\Currency;
use Seshra\Core\Models\Category;
use Seshra\Core\Models\Warranty;
use Seshra\Core\Models\Attribute;
use Seshra\Core\Models\ProductTag;
use Seshra\Core\Models\Translation;
use Seshra\Core\Models\AttributeSet;
use Seshra\Core\Models\WarrantyPeriod;
use Seshra\Core\Models\AttributeOption;
use Seshra\Core\Models\ProductDimension;
use Seshra\Core\Models\ProductVariation;
use Seshra\Core\Models\BrandTranslation;
use Seshra\Core\Models\CategoryTranslation;
use Seshra\Core\Models\AttributeTranslation;
use Konekt\Concord\BaseModuleServiceProvider;
use Seshra\Core\Models\OrderDetail;
use Seshra\Core\Models\ProductAttributeOption;
use Seshra\Core\Models\Review;

/**
 * Class ModuleServiceProvider
 * @package Seshra\Core\Providers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        Color::class,
        Brand::class,
        Order::class,
        Locale::class,
        Review::class,
        Product::class,
        Category::class,
        Currency::class,
        Warranty::class,
        Attribute::class,
        ProductTag::class,
        OrderDetail::class,
        Translation::class,
        AttributeSet::class,
        WarrantyPeriod::class,
        AttributeOption::class,
        BrandTranslation::class,
        ProductVariation::class,
        ProductDimension::class,
        CategoryTranslation::class,
        AttributeTranslation::class,
        ProductAttributeOption::class
    ];
}