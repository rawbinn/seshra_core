<?php

namespace Seshra\Core\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Seshra\Core\Repositories\CountryRepository;

/**
 * Class CountryController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class CountryController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_routes;

    /**
     * @var CountryRepository
     */
    protected $countryRepository;

    /**
     * CountryController constructor.
     * @param CountryRepository $countryRepository
     */
    public function __construct(
        CountryRepository $countryRepository
    ){
        $this->_routes = request('_routes');
        $this->countryRepository = $countryRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function index(Request $request)
    {
        $countries = $this->countryRepository->orderBy('name', 'asc');
        if ($request->has('search')){
            $countries = $countries->where('name', 'like', $request->search.'%');
        }
        $countries = $countries->paginate(15);
        return view($this->_routes['view'], compact('countries'));
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
            'code' => 'required|max:2|unique:countries',
            'name' => 'required|unique:countries'
        ]);

        $this->countryRepository->create($request->only('code', 'name'));
        flash(translate('Country has been added successfully'))->success();
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
        $countries = $this->countryRepository->orderBy('name', 'asc');
        if ($request->has('search')){
            $countries = $countries->where('name', 'like', $request->search.'%');
        }
        $countries = $countries->paginate(15);

        $country  = $this->countryRepository->findOrFail($id);
        return view($this->_routes['view'], compact('country', 'countries'));
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
            'code' => 'required|max:2|unique:countries,code,'.$id,
            'name' => 'required|unique:countries,name,'.$id
        ]);
        $country = $this->countryRepository->findOrFail($id);
        
        $this->countryRepository->update($request->only('code', 'name'), $country->id);

        flash(translate('Country has been updated successfully'))->success();
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
        $this->countryRepository->delete($id);
        flash(translate('Country has been deleted successfully'))->success();
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
        $this->countryRepository->update(['status' => $request->status], $request->id);
        return response()->json([
            'status' => true,
            'message' => 'Country status updated successfully'
        ]);
    }
}
