<?php

namespace Seshra\Core\Models\Traits;

trait ActiveScope
{
    public function scopeActive($query, $status = true) {
		return $query->where('status', $status);
	}
}