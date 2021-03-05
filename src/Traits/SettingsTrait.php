<?php

namespace Seshra\Core\Traits;

use Illuminate\Http\Request;
use Seshra\Core\Helpers\Settings;

trait SettingsTrait
{
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
}