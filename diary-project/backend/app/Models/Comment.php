<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model,SoftDeletes};
use App\Models\{Diary,User};

class Comment extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        "user_id",
        "diary_id",
        "text",
    ];

    public function user() : \Illuminate\Database\Eloquent\Relations\BelongsTo 
    {
        return $this->belongsTo(User::class);   
    }

    public function diary() : \Illuminate\Database\Eloquent\Relations\BelongsTo 
    {
        return $this->belongsTo(Diary::class);
    }
}
