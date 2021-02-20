<?php

namespace Seshra\Core\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Seshra\Core\Repositories\BrandRepository;

/**
 * Class BrandController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class BrandController extends Controller
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
     * BrandController constructor.
     * @param BrandRepository $brandRepository
     */
    public function __construct(
        BrandRepository $brandRepository
    ){
        $this->_routes = request('_routes');
        $this->brandRepository = $brandRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function index(Request $request)
    {
        $brands = $this->brandRepository->orderByTranslation('name', 'asc');
        if ($request->has('search')){
            $brands = $brands->whereTranslationLike('name', $request->search.'%');
        }
        $brands = $brands->paginate(15);
        return view($this->_routes['view'], compact('brands'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'unique:brand_translations,name'],
        ]);

        $this->brandRepository->create($request->only('name','logo','meta_title','meta_description'));
        
        flash(translate('Brand has been added successfully'))->success();
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
        $brand  = $this->brandRepository->findOrFail($id);
        return view($this->_routes['view'], compact('brand','lang'));
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

        $brand = $this->brandRepository->findOrFail($id);
        
        $this->brandRepository->update($request->only($locale,'slug','logo','meta_title','meta_description'), $id);

        flash(translate('Brand has been updated successfully'))->success();
        return redirect()->route($this->_routes['redirect']);

    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function destroy($id)
    {
        $this->brandRepository->delete($id);
        flash(translate('Brand has been deleted successfully'))->success();
        return redirect()->route($this->_routes['redirect']);

    }
}
