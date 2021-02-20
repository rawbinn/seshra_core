<?php

namespace Seshra\Core\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Seshra\Core\Repositories\CurrencyRepository;

/**
 * Class CurrencyController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class CurrencyController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_routes;

    /**
     * @var CurrencyRepository
     */
    protected $currencyRepository;

    /**
     * CurrencyController constructor.
     * @param CurrencyRepository $currencyRepository
     */
    public function __construct(
        CurrencyRepository $currencyRepository
    ){
        $this->_routes = request('_routes');
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * @param Request $request
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function changeCurrency(Request $request)
    {
    	$request->session()->put('currency_code', $request->currency_code);
        $currency = Currency::where('code', $request->currency_code)->first();
    	flash(translate('Currency changed to ').$currency->name)->success();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function index(Request $request)
    {
        $sort_search =null;
        $currencies = $this->currencyRepository->orderBy('created_at', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $currencies = $currencies->where('name', 'like', '%'.$sort_search.'%');
        }
        $currencies = $currencies->paginate(10);

        $active_currencies = $this->currencyRepository->where('status', 1)->get();
        return view($this->_routes['view'], compact('currencies', 'active_currencies','sort_search'));
    }

    /**
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function create()
    {
        return view($this->_routes['view']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:currencies',
            'symbol' => 'required|unique:currencies',
            'code' => 'required|unique:currencies',
            'exchange_rate' => 'required'
        ]);
        $this->currencyRepository->create($request->only('name','symbol','code','exchange_rate'));
        flash(translate('Currency updated successfully'))->success();
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
        $currency = $this->currencyRepository->findOrFail($id);
        return view($this->_routes['view'], compact('currency'));
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
            'name' => 'required|unique:currencies,name,'.$id,
            'symbol' => 'required|unique:currencies,symbol,'.$id,
            'code' => 'required|unique:currencies,code,'.$id,
            'exchange_rate' => 'required'
        ]);
       $this->currencyRepository->update($request->only('name','symbol', 'code', 'exchange_rate'), $id);
        flash(translate('Currency updated successfully'))->success();
        return redirect()->route('currency.index');
        
    }

    /**
     * @param Request $request
     * @return int
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function update_status(Request $request)
    {
        $currency = Currency::findOrFail($request->id);
        $currency->status = $request->status;
        if($currency->save()){
            return 1;
        }
        return 0;
    }
}
