<?php

namespace Seshra\Core;

use Illuminate\Support\Facades\Event;

class EventManager
{
    /**
     * Contains all themes
     *
     * @var array
     */
    protected $templates = [];

    /**
     * Paramters passed with event
     *
     * @var array
     */
    protected $params;

    protected $query;

    /**
     * Fires event for rendering template
     *
     * @param  string  $eventName
     * @param  array|null  $params
     * @return string
     */
    public function handleRenderEvent($eventName, $params = null)
    {
        if($params instanceof \Illuminate\Database\Eloquent\Builder) {
            $this->query = $params;
        }else{
            $this->params = $params ?? [];
            $this->query = null;
        }

        Event::dispatch($eventName, $this);

        return $this->templates;
    }

    /**
     *  get params
     *
     * @return array
     */
    public function query()
    {
        return $this->query;
    }

    /**
     *  get params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     *  get param
     *
     * @param  $name
     * @return mixed
     */
    public function getParam($name)
    {
        return optional($this->params)[$name];
    }

    /**
     * Add templates for render
     *
     * @param  string  $template
     * @return void
     */
    public function addTemplate($template)
    {
        array_push($this->templates, $template);
    }

    /**
     * Renders templates
     *
     * @return string
     */
    public function render()
    {
        if($this->query){
            return $this->query;
        }

        $string = "";
        foreach ($this->templates as $template) {
            if (view()->exists($template)) {
                $string .= view($template , $this->params)->render();
            } elseif (is_string($template)) {
                $string .= $template;
            }
        }

        return $string;
    }
}
