<?php

namespace Seshra\Core\Http\Controllers;

use Illuminate\Http\Request;
use Seshra\Core\Http\Requests\Attribute\StoreAttributeRequest;
use Seshra\Core\Repositories\AttributeRepository;

/**
 * Class AttributeController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class AttributeController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_routes;

    /**
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * AttributeController constructor.
     * @param AttributeRepository $attributeRepository
     */
    public function __construct(
        AttributeRepository $attributeRepository
    ){
        $this->_routes = request('_routes');
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function index(Request $request)
    {
        $attributes = $this->attributeRepository->orderBy('id', 'DESC');
        if($request->has('search')) {
            $attributes = $attributes->whereTranslationLike('name', $request->search.'%');
        }
        $attributes = $attributes->paginate(15);
        return view($this->_routes['view'], compact('attributes'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function store(StoreAttributeRequest $request)
    {
        $this->attributeRepository->create($request->only('name','type','is_filterable'));

        flash(translate('Attribute has been added successfully'))->success();
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
        $attribute = $this->attributeRepository->find($id);

        return view($this->_routes['view'], compact('attribute','lang'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function update(Request $request, $id)
    {
        $locale = $request->locale ?? app()->getLocale();
        $request->validate([
            $locale.'.name' => 'required',
            'type' => 'required|in:text,select,swatch',
            'is_filterable' => 'required|in:0,1'
        ]);
        $this->attributeRepository->update($request->only($locale,'type','is_filterable'), $id);
        
        flash(translate('Attribute has been updated successfully'))->success();
        return redirect()->route($this->_routes['redirect']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function destroy($id)
    {
        $this->attributeRepository->delete($id);

        flash(translate('Attribute has been deleted successfully'))->success();
        return redirect()->route($this->_routes['redirect']);

    }
    
}
