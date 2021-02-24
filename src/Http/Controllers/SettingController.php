<?php

namespace Seshra\Core\Http\Controllers;

use Setting;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Seshra\Core\Helpers\Settings;

/**
 * Class SettingController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class SettingController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_routes;

    /**
     * SettingController constructor.
     */
    public function __construct(
    ){
        $this->_routes = request('_routes');
    }

    public function getSettingsForm()
    {
        return view($this->_routes['view']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'settings' => 'required'
        ]);
        Settings::update($request->settings);
        flash(translate("Settings updated successfully"))->success();
        return redirect()->route($this->_routes['redirect']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function updateDefaultLanguage(Request $request)
    {
        $request->validate([
            'locale' => 'required|exists:languages,code'
        ]);
        update_env_file('DEFAULT_LANGUAGE', $request->locale);
        flash(translate("Settings updated successfully"))->success();
        return redirect()->route($this->_routes['redirect']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function updateDefaultCurrency(Request $request)
    {
        $request->validate([
            'system_default_currency' => 'required|exists:currencies,id'
        ]);
        Setting::set('system_default_currency', $request->system_default_currency);
        flash(translate("Settings updated successfully"))->success();
        return redirect()->route($this->_routes['redirect']);
    }

}