<?php

namespace Seshra\Core\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Seshra\Core\Repositories\CountryRepository;
use Seshra\Core\Repositories\DistrictRepository;
use Seshra\Core\Repositories\StateRepository;

/**
 * Class DistrictController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class DistrictController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_routes;

    /**
     * @var DistrictRepository
     */
    protected $districtRepository;

    /**
     * @var CountryRepository
     */
    protected $countryRepository;
    
    /**
     * @var StateRepository
     */
    protected $stateRepository;

    /**
     * DistrictController constructor.
     * @param DistrictRepository $districtRepository
     */
    public function __construct(
        DistrictRepository $districtRepository,
        CountryRepository $countryRepository,
        StateRepository $stateRepository
    ){
        $this->_routes = request('_routes');
        $this->districtRepository = $districtRepository;
        $this->countryRepository = $countryRepository;
        $this->stateRepository = $stateRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function index(Request $request)
    {
        $districts = $this->districtRepository->orderBy('name', 'asc');
        if ($request->has('search')){
            $districts = $districts->where('name', 'like', $request->search.'%');
        }
        $districts = $districts->paginate(15);
        $countries = $this->countryRepository->active()->get();
        return view($this->_routes['view'], compact('districts', 'countries'));
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
            'name' => 'required|unique:districts'
        ]);

        $this->districtRepository->create($request->only('country_id','state_id','name'));
        flash(translate('District has been added successfully'))->success();
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
        $districts = $this->districtRepository->orderBy('name', 'asc');
        if ($request->has('search')){
            $districts = $districts->where('name', 'like', $request->search.'%');
        }
        $districts = $districts->paginate(15);
        $countries = $this->countryRepository->active()->get();
        $states = $this->stateRepository->active()->get();
        $district  = $this->districtRepository->findOrFail($id);
        return view($this->_routes['view'], compact('district', 'districts', 'countries', 'states'));
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
            'name' => 'required|unique:districts,name,'.$id
        ]);
        $district = $this->districtRepository->findOrFail($id);
        
        $this->districtRepository->update($request->only('name', 'country_id', 'state_id'), $district->id);

        flash(translate('District has been updated successfully'))->success();
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
        $this->districtRepository->delete($id);
        flash(translate('District has been deleted successfully'))->success();
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
        $this->districtRepository->update(['status' => $request->status], $request->id);
        return response()->json([
            'status' => true,
            'message' => 'District status updated successfully'
        ]);
    }

    public function getDistricts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'state_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['status' => false, 'message' => $errors[0], 'data' => []]);
        }

        $districts = $this->districtRepository->where('state_id', $request->state_id)->active()->get();
        if(!$districts->isEmpty()) {
            return response()->json(['status' => true, 'data' => $districts]);
        }
        return response()->json(['status' => false, 'message' => 'Districts not found!!!']);
    }
}
