<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use APP\Models\{Diary,User};

class Favorite extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['user_id', 'diary_id'];

    public function user() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function diary() : \Illuminate\Database\Eloquent\Relations\BelongsTo 
    {
        return $this->belongsTo(Diary::class);
    }
}
