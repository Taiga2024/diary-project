<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use Illuminate\Support\Str;
use App\Models\{Comment,Favorite,User};

class Diary extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "user_id",
        "thumbnail",
        "title",
        "text",
    ];

    public function user() : \Illuminate\Database\Eloquent\Relations\BelongsTo 
    {
        return $this->belongsTo(User::class);   
    }

    public function comments() : \Illuminate\Database\Eloquent\Relations\HasMany 
    {
        return $this->hasMany(Comment::class);   
    }

    public function favorites() : \Illuminate\Database\Eloquent\Relations\HasMany 
    {
        return $this->hasMany(Favorite::class);   
    }
}
