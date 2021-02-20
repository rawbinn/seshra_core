<?php

namespace Seshra\Core\Http\Controllers;

use Illuminate\Http\Request;
use Seshra\Core\Repositories\WarrantyPeriodRepository;
use Seshra\Core\Repositories\WarrantyRepository;

/**
 * Class WarrantyPeriodController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class WarrantyPeriodController extends Controller
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
     * @var WarrantyPeriodRepository
     */
    protected $warrantyPeriodRepository;

    /**
     * WarrantyPeriodController constructor.
     * @param WarrantyRepository $warrantyRepository
     * @param WarrantyPeriodRepository $warrantyPeriodRepository
     */
    public function __construct(
        WarrantyRepository $warrantyRepository,
        WarrantyPeriodRepository $warrantyPeriodRepository
    ){
        $this->_routes = request('_routes');
        $this->warrantyRepository = $warrantyRepository;
        $this->warrantyPeriodRepository = $warrantyPeriodRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function index(Request $request)
    {
        $periods = $this->warrantyPeriodRepository->orderBy('name', 'asc');
        if ($request->has('search')) {
            $periods = $periods->where('name', 'like', '%'.$request->search.'%');
        }
        $periods = $periods->paginate(15);
        return view($this->_routes['view'], compact('periods'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required',
        ]);
        $this->warrantyPeriodRepository->create($request->only('name'));
        flash(translate('Warranty period has been added successfully'))->success();
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
        $warranty  = $this->warrantyPeriodRepository->findOrFail($id);
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
        $request->validate([
            'name'        => 'required',
        ]);
        $locale = $request->locale ?? app()->getLocale();

        $warranty = $this->warrantyPeriodRepository->findOrFail($id);
        
        $this->warrantyPeriodRepository->update($request->only('name'), $id);

        flash(translate('Warranty period has been updated successfully'))->success();
        return redirect()->route($this->_routes['redirect']);

    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function destroy($id)
    {
        $this->warrantyPeriodRepository->delete($id);
        flash(translate('Warranty period has been deleted successfully'))->success();
        return redirect()->route($this->_routes['redirect']);

    }
}