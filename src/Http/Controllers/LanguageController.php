<?php

namespace Seshra\Core\Http\Controllers;

use Illuminate\Http\Request;
use Seshra\Core\Repositories\LocaleRepository;
use Seshra\Core\Repositories\TranslationRepository;

/**
 * Class LanguageController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class LanguageController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_routes;

    /**
     * @var LocaleRepository
     */
    protected $localeRepository;

    /**
     * @var TranslationRepository
     */
    protected $translationRepository;

    /**
     * LanguageController constructor.
     * @param LocaleRepository $localeRepository
     * @param TranslationRepository $translationRepository
     */
    public function __construct(
        LocaleRepository $localeRepository,
        TranslationRepository $translationRepository
    ){
        $this->_routes = request('_routes');
        $this->localeRepository = $localeRepository;
        $this->translationRepository = $translationRepository;
    }

    /**
     * @param Request $request
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function changeLanguage(Request $request)
    {
        $request->session()->put('locale', $request->locale);
        $language = $this->localeRepository->findOneByField('code', $request->locale);
    	flash(translate('Language changed to ').$language->name)->success();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function index(Request $request)
    {
        $languages = $this->localeRepository->paginate(5);
        return view($this->_routes['view'], compact('languages'));
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
            'name' => 'required|unique:languages',
            'code' => 'required'
        ]);
        $this->localeRepository->create($request->only('name','code'));
        flash(translate('Language has been added successfully'))->success();
        return redirect()->route($this->_routes['redirect']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function show(Request $request, $id)
    {
        $sort_search = null;
        $language = $this->localeRepository->findOrFail(decrypt($id));
        $lang_keys = $this->translationRepository->where('lang', env('DEFAULT_LANGUAGE', 'en'));
        if ($request->has('search')){
            $sort_search = $request->search;
            $lang_keys = $lang_keys->where('lang_key', 'like', '%'.$sort_search.'%');
        }
        $lang_keys = $lang_keys->paginate(50);
        return view($this->_routes['view'], compact('language','lang_keys','sort_search'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\View
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function edit($id)
    {
        $language =$this->localeRepository->findOrFail(decrypt($id));
        return view($this->_routes['view'], compact('language'));
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
            'name' => 'required|unique:languages,name,'.$id,
            'code' => 'required'
        ]);
        $this->localeRepository->update($request->only('name','code'), $id);
        flash(translate('Language has been updated successfully'))->success();
        return redirect()->route($this->_routes['redirect']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function updateTranslations(Request $request, $id)
    {
        $language = $this->localeRepository->findOrFail($request->id);
        foreach ($request->values as $key => $value) {
            $translation_def = $this->translationRepository->where('lang_key', $key)->where('lang', $language->code)->first();
            if($translation_def == null){
                $this->translationRepository->create([
                    'lang' => $language->code,
                    'lang_key' => $key,
                    'lang_value' => $value
                ]);
            }
            else {
                $translation_def->lang_value = $value;
                $translation_def->save();
            }
        }
        flash(translate('Translations updated for ').$language->name)->success();
        return redirect()->route($this->_routes['redirect']);
    }

    /**
     * @param Request $request
     * @return int
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function updateType(Request $request)
    {
        $language = $this->localeRepository->findOrFail($request->id);
        $language->rtl = $request->status;
        if($language->save()){
            flash(translate('RTL status updated successfully'))->success();
            return 1;
        }
        return 0;
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function destroy($id)
    {
        $this->localeRepository->delete($id);
        flash(translate('Language has been deleted successfully'))->success();
        return redirect()->route($this->_routes['redirect']);
    }
}
