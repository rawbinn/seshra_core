<?php

namespace Seshra\Core\Http\Controllers;

use Illuminate\Http\Request;
use Seshra\Core\Repositories\AttributeRepository;
use Seshra\Core\Repositories\CategoryRepository;

/**
 * Class AttributeSetController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class AttributeSetController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_routes;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * AttributeSetController constructor.
     * @param CategoryRepository $categoryRepository
     * @param AttributeRepository $attributeRepository
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        AttributeRepository $attributeRepository
    ){
        $this->_routes = request('_routes');
        $this->categoryRepository = $categoryRepository;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function index()
    {
        $categories = $this->categoryRepository->getAllWithChildren();
        $attributes = $this->attributeRepository->all();
        return view($this->_routes['view'], compact('categories', 'attributes'));
    }

    /**
     * Ajax Call
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function attributes_of_set(Request $request)
    {
        $selected_attributes = $this->attributeRepository->getAllAttributesOfSet($request->attribute_set_id);
        return response()->json(view('admin::catalog.attribute_sets.partials.selected_attributes', compact('selected_attributes'))->render());
    }

    /**
     * Ajax Call
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function add_attribute_to_set(Request $request)
    {
        $this->attributeRepository->addAttributeToSet($request->attribute_set_id, $request->input('attributes'));
        $selected_attributes = $this->attributeRepository->getAllAttributesOfSet($request->attribute_set_id);
        return response()->json(view('admin::catalog.attribute_sets.partials.selected_attributes', compact('selected_attributes'))->render());
    }

    /**
     * Ajax Call
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function remove_attribute_from_set(Request $request)
    {
        $this->attributeRepository->removeAttributeFromSet($request->attribute_set_id, $request->input('attributes'));
        $selected_attributes = $this->attributeRepository->getAllAttributesOfSet($request->attribute_set_id);
        return response()->json(view('admin::catalog.attribute_sets.partials.selected_attributes', compact('selected_attributes'))->render());
    }
}