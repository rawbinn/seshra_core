<?php

namespace Seshra\Core\Http\Controllers;

use Illuminate\Http\Request;
use Seshra\Core\Repositories\ColorRepository;

/**
 * Class ColorController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class ColorController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_routes;

    /**
     * @var ColorRepository
     */
    protected $colorRepository;

    /**
     * ColorController constructor.
     * @param ColorRepository $colorRepository
     */
    public function __construct(
        ColorRepository $colorRepository
    ){
        $this->_routes = request('_routes');
        $this->colorRepository = $colorRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function index(Request $request)
    {
        $colors = $this->colorRepository->orderBy('id', 'DESC');
        if($request->has('search')) {
            $colors = $colors->whereTranslationLike('name', $request->search.'%');
        }
        $colors = $colors->paginate(15);
        return view($this->_routes['view'], compact('colors'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:color_translations,name',
            'code' => 'required'
        ]);

        $this->colorRepository->create($request->only('name','code'));

        flash(translate('Color has been addded successfully'))->success();
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
        $color = $this->colorRepository->find($id);

        return view($this->_routes['view'], compact('color','lang'));
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
            'code' => 'required'
        ]);
        $this->colorRepository->update($request->only($locale,'code'), $id);
        
        flash(translate('Color has been updated successfully'))->success();
        return redirect()->route($this->_routes['redirect']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function destroy($id)
    {
        $this->colorRepository->delete($id);

        flash(translate('Color has been deleted successfully'))->success();
        return redirect()->route('admin.colors.index');

    }

}
