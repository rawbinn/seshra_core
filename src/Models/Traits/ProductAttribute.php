<?php

namespace Seshra\Core\Models\Traits;

use Illuminate\Support\Facades\Storage;

trait ProductAttribute
{
    public function getFormattedPrice()
    {
        return currency($this->retail_price);
    }

    public function getThumbnail()
    {
        if(!$this->thumbnails) {
            return '';
        }
        $thumbnail_file = $this->thumbnails->first()->file_name;
        return Storage::disk(config('filesystems.default'))->url($thumbnail_file);
    }

    public function scopeActive($query, $status = true) {
		return $query->where('status', $status);
	}

    public function scopeFeatured($query, $status = true) {
		return $query->where('featured', $status);
	}
}