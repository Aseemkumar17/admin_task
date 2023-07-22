<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;
    protected $visible = ['id','images'];
    public function products()
    {
        return $this->belongsTo(Product::class);
    }
}
