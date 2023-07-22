<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_permission extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'permission_id'];
    public function admin()
    {

        return $this->belongsTo(AdminLogin::class,'user_id');
    }
    public function permission()
    {
        return $this->belongsTo(Access_permission::class,'permission_id');
    }
}
