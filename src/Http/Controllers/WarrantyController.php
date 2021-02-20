<?php

namespace Seshra\Core\Http\Controllers;

use Illuminate\Http\Request;
use Seshra\Core\Repositories\WarrantyRepository;

/**
 * Class WarrantyController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class WarrantyController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_routes;

    /**
     * @var WarrantyRepository
     */
    protected $warrantyRepository;

    /**
     * WarrantyController constructor.
     * @param WarrantyRepository $warrantyRepository
     */
    public function __construct(
        WarrantyRepository $warrantyRepository
    ){
        $this->_routes = request('_routes');
        $this->warrantyRepository = $warrantyRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function index(Request $request)
    {
        $warranties = $this->warrantyRepository->orderBy('name', 'asc');
        if ($request->has('search')){
            $warranties = $warranties->where('name', 'like', '%'.$request->search.'%');
        }
        $warranties = $warranties->paginate(15);
        return view($this->_routes['view'], compact('warranties'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required'
        ]);

        $this->warrantyRepository->create($request->only('name'));
        
        flash(translate('Warranty has been added successfully'))->success();
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
        $warranty  = $this->warrantyRepository->findOrFail($id);
        return view($this->_routes['view'], compact('warranty','lang'));
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

        $warranty = $this->warrantyRepository->findOrFail($id);
        
        $this->warrantyRepository->update($request->only('name'), $id);

        flash(translate('Warranty has been updated successfully'))->success();
        return redirect()->route($this->_routes['redirect']);

    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function destroy($id)
    {
        $this->warrantyRepository->delete($id);
        flash(translate('Warranty has been deleted successfully'))->success();
        return redirect()->route($this->_routes['redirect']);

    }
}