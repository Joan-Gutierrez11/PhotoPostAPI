<?php

namespace App\Models;

use App\Utils\PathUrlGetter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory, PathUrlGetter;

    protected $fillable = [
        'title',
        'description',
        'image',
        'user_id'
    ];

    protected $hidden = [
        'user_id',
        'user'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getImageAttribute($value){
        return $this->getUrl($value);
    }

}
