<?php

namespace Seshra\Core\Traits;


trait SlugTrait
{
    protected function slug($slug)
    {
        $slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $slug));
        if($this->model->where('slug', $slug)->count() > 0) {
            $update_slug = $slug.'-'.mt_rand(1,9);
            return $this->slug($update_slug);
        }
        return strtolower($slug);
    }
}