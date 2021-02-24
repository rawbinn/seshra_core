<?php

namespace Seshra\Core\Http\Controllers;

use Illuminate\Http\Request;
use Seshra\Core\Helpers\Settings;

/**
 * Class AppearanceController
 * @package Seshra\Core\Http\Controllers
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class AppearanceController extends Controller
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

    public function getAppearanceForm()
    {
        return view($this->_routes['view']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @author Rawbinn Shrestha ( rawbinnn@gmail.com )
     */
    public function updateAppearance(Request $request)
    {
        $request->validate([
            'settings' => 'required'
        ]);
        $settings = $request->settings;
        if(array_key_exists('header_menu_labels', $settings)) {
            $settings['header_menu'] = [];
            foreach($settings['header_menu_labels'] as $key => $label) {
                $settings['header_menu'] = array_merge($settings['header_menu'], [
                    $label => $settings['header_menu_links'][$key]
                ]);
            }
            unset($settings['header_menu_labels'], $settings['header_menu_links']);
        }
        if(array_key_exists('widgets', $settings)) {
            $links = [];
            foreach($settings['widgets'] as $key => $widget) {
                foreach($widget['labels'] as $lkey => $label) {
                    $links = array_merge($links, [
                        $label => $widget['links'][$lkey]
                    ]);
                }
                $settings['widgets'][$key]['links'] = $links;
                unset($settings['widgets'][$key]['labels']);
            }
        }
        if(array_key_exists('sliders', $settings)) {
            $sliders = [];
            foreach($settings['sliders']['labels'] as $key => $value) {
                $sliders[] = [
                    'label' => $value,
                    'link' => $settings['sliders']['links'][$key],
                    'image' => $settings['sliders']['images'][$key]
                ];
            }
            $settings['sliders'] = $sliders;
        }
        Settings::update($settings);
        flash(translate("Settings updated successfully"))->success();
        return redirect()->route($this->_routes['redirect']);
    }

}