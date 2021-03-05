<?php

namespace Seshra\Core\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Seshra\Core\Repositories\CountryRepository;
use Seshra\Core\Repositories\StateRepository;

/**
 * Class StateController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class StateController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_routes;

    /**
     * @var StateRepository
     */
    protected $stateRepository;

    /**
     * @var CountryRepository
     */
    protected $countryRepository;

    /**
     * StateController constructor.
     * @param StateRepository $stateRepository
     */
    public function __construct(
        StateRepository $stateRepository,
        CountryRepository $countryRepository
    ){
        $this->_routes = request('_routes');
        $this->stateRepository = $stateRepository;
        $this->countryRepository = $countryRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function index(Request $request)
    {
        $states = $this->stateRepository->orderBy('name', 'asc');
        if ($request->has('search')){
            $states = $states->where('name', 'like', $request->search.'%');
        }
        $states = $states->paginate(15);
        $countries = $this->countryRepository->active()->get();
        return view($this->_routes['view'], compact('states', 'countries'));
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
            'name' => 'required|unique:states'
        ]);

        $this->stateRepository->create($request->only('country_id','name'));
        flash(translate('State has been added successfully'))->success();
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
        $states = $this->stateRepository->orderBy('name', 'asc');
        if ($request->has('search')){
            $states = $states->where('name', 'like', $request->search.'%');
        }
        $states = $states->paginate(15);
        $countries = $this->countryRepository->active()->get();
        $state  = $this->stateRepository->findOrFail($id);
        return view($this->_routes['view'], compact('state', 'states', 'countries'));
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
            'name' => 'required|unique:states,name,'.$id
        ]);
        $state = $this->stateRepository->findOrFail($id);
        
        $this->stateRepository->update($request->only('name', 'country_id'), $state->id);

        flash(translate('State has been updated successfully'))->success();
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
        $this->stateRepository->delete($id);
        flash(translate('State has been deleted successfully'))->success();
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
        $this->stateRepository->update(['status' => $request->status], $request->id);
        return response()->json([
            'status' => true,
            'message' => 'State status updated successfully'
        ]);
    }

    public function getStates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['status' => false, 'message' => $errors[0], 'data' => []]);
        }

        $states = $this->stateRepository->where('country_id', $request->country_id)->active()->get();
        if(!$states->isEmpty()) {
            return response()->json(['status' => true, 'data' => $states]);
        }
        return response()->json(['status' => false, 'message' => 'States not found!!!']);
    }
}
