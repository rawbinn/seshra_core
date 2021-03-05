<?php

namespace Seshra\Core\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Seshra\Core\Repositories\CountryRepository;
use Seshra\Core\Repositories\CityRepository;
use Seshra\Core\Repositories\DistrictRepository;
use Seshra\Core\Repositories\StateRepository;

/**
 * Class CityController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class CityController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_routes;

    /**
     * @var CityRepository
     */
    protected $cityRepository;

    /**
     * @var CountryRepository
     */
    protected $countryRepository;
    
    /**
     * @var StateRepository
     */
    protected $stateRepository;
   
    /**
     * @var DistrictRepository
     */
    protected $districtRepository;

    /**
     * CityController constructor.
     * @param CityRepository $cityRepository
     */
    public function __construct(
        CityRepository $cityRepository,
        CountryRepository $countryRepository,
        StateRepository $stateRepository,
        DistrictRepository $districtRepository
    ){
        $this->_routes = request('_routes');
        $this->cityRepository = $cityRepository;
        $this->countryRepository = $countryRepository;
        $this->stateRepository = $stateRepository;
        $this->districtRepository = $districtRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function index(Request $request)
    {
        $cities = $this->cityRepository->orderBy('name', 'asc');
        if ($request->has('search')){
            $cities = $cities->where('name', 'like', $request->search.'%');
        }
        $cities = $cities->paginate(15);
        $countries = $this->countryRepository->active()->get();
        return view($this->_routes['view'], compact('cities', 'countries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'district_id' => 'required|integer',
            'name' => 'required'
        ]);

        $this->cityRepository->create($request->only('country_id','state_id','district_id','name'));
        flash(translate('City has been added successfully'))->success();
        return redirect()->route($this->_routes['redirect']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $cities = $this->cityRepository->orderBy('name', 'asc');
        if ($request->has('search')){
            $cities = $cities->where('name', 'like', $request->search.'%');
        }
        $cities = $cities->paginate(15);
        $city  = $this->cityRepository->findOrFail($id);
        $countries = $this->countryRepository->active()->get();
        $states = $this->stateRepository->where('country_id', $city->country_id)->active()->get();
        $districts = $this->districtRepository->where('state_id', $city->state_id)->active()->get();
        return view($this->_routes['view'], compact('city', 'cities', 'countries', 'states', 'districts'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'district_id' => 'required|integer',
            'name' => 'required'
        ]);
        $city = $this->cityRepository->findOrFail($id);
        
        $this->cityRepository->update($request->only('name', 'country_id', 'state_id', 'district_id'), $city->id);

        flash(translate('City has been updated successfully'))->success();
        return redirect()->route($this->_routes['redirect']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $this->cityRepository->delete($id);
        flash(translate('City has been deleted successfully'))->success();
        return redirect()->route($this->_routes['redirect']);
    }

    /**
     * Ajax Call
     * @param Request $request
     * @return int
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'status' => 'required|in:1,0'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['status' => false, 'message' => $errors[0], 'data' => []]);
        }
        $this->cityRepository->update(['status' => $request->status], $request->id);
        return response()->json([
            'status' => true,
            'message' => 'City status updated successfully'
        ]);
    }

    public function getCities(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'district_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['status' => false, 'message' => $errors[0], 'data' => []]);
        }

        $cities = $this->cityRepository->where('district_id', $request->district_id)->active()->get();
        if(!$cities->isEmpty()) {
            return response()->json(['status' => true, 'data' => $cities]);
        }
        return response()->json(['status' => false, 'message' => 'Cities not found!!!']);
    }
}
