<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    protected $table = 'images';

    /**
     * Get the owning imageable model (post or comment).
     */
    public function imageable()
    {
        return $this->morphTo();
    }
}
