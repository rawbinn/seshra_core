<?php

namespace Seshra\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Seshra\Core\Repositories\AttributeRepository;
use Seshra\Core\Repositories\AttributeOptionRepository;

/**
 * Class AttributeOptionController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class AttributeOptionController extends Controller
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
     * @var AttributeOptionRepository
     */
    protected $optionRepository;

    /**
     * AttributeOptionController constructor.
     * @param AttributeRepository $attributeRepository
     * @param AttributeOptionRepository $optionRepository
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        AttributeOptionRepository $optionRepository
    ){
        $this->_routes = request('_routes');
        $this->attributeRepository = $attributeRepository;
        $this->optionRepository = $optionRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function index(Request $request)
    {
        $attributes = $this->attributeRepository->findWhereIn('type', ['swatch','select']);
        $attribute_options = $this->optionRepository->orderBy('attribute_id','ASC');
        if($request->has('search')) {
            $attribute_options = $attribute_options->whereTranslationLike('name', $request->search.'%');
        }
        $attribute_options = $attribute_options->paginate(15);
        
        return view($this->_routes['view'], compact('attributes','attribute_options'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function store(Request $request)
    {
        $request->validate([
            'attribute_id' => 'required|integer|exists:attributes,id',
            'name' => [
                'required', 
                function ($attribute, $value, $fail) use ($request) {
                    if ($this->optionRepository->isNameExist($value, $request->attribute_id)) {
                        $fail('The name has been already taken.');
                    }
                }
            ],
            'sort' => 'required|integer|min:0'
        ]);
        $this->optionRepository->create($request->only('attribute_id','name','sort'));

        flash(translate('Attribute option has been added successfully.'))->success();
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
        $option = $this->optionRepository->find($id);
        $attributes = $this->attributeRepository->findWhereIn('type', ['swatch','select']);
        $attribute_options = $this->optionRepository->with('attribute')->orderBy('attribute_id','ASC');
        if($request->has('search')) {
            $attribute_options = $attribute_options->where('name','like', '%'.$request->search.'%');
        }
        $attribute_options = $attribute_options->paginate(15);
        
        return view($this->_routes['view'], compact('option','attributes','attribute_options', 'lang'));
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
            'attribute_id' => 'required|integer|exists:attributes,id',
            $locale.'.name' => [
                'required', 
                function ($attribute, $value, $fail) use ($request, $id) {
                    if ($this->optionRepository->isNameExist($id, $request->attribute_id, $value)) {
                        $fail('The name has been already taken.');
                    }
                }
            ],
            'sort' => 'required|integer|min:0'
        ]);

        $this->optionRepository->update($request->only('attribute_id', $locale,'sort'), $id);

        flash(translate('Attribute option has been updated successfully.'))->success();
        return redirect()->route($this->_routes['redirect']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function destroy($id)
    {
        $this->optionRepository->delete($id);

        flash(translate('Attribute option has been deleted successfully'))->success();
        return redirect()->route($this->_routes['redirect']);
    }

    /**
     * Ajax Call
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function option_sort(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attribute_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            $errors = $validator->messages()->all();
            return response()->json(['status' => false, 'message' => $errors[0], 'data' => []]);
        }
        $option = $this->optionRepository->where('attribute_id', $request->attribute_id)->orderBy('sort','DESC')->first();
        $options = $this->optionRepository->where('attribute_id', $request->attribute_id)->orderBy('attribute_id','ASC')->paginate(15);
        if($option) {
            return response()->json(['sort' => $option->sort + 1, 'view' => view('admin::catalog.attribute_options.partials.options', compact('options'))->render()]);
        }
            
        return response()->json(['sort' => 1, 'view' => false]);
    }

    /**
     * Ajax Call
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function attribute_options(Request $request)
    {
        $options = $this->optionRepository->where('attribute_id', $request->attribute_id)->orderBy('sort','ASC')->get();
        return response()->json($options);
    }
}
