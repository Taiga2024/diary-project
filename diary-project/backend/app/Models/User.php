<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use APP\Models\{Diary,Comment,Favorite};
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'profile_image',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function diaries() : \Illuminate\Database\Eloquent\Relations\HasMany 
    {
        return $this -> hasmany(Diary::class);
    }

    public function comments() : \Illuminate\Database\Eloquent\Relations\HasMany 
    {
        return $this -> hasmany(Comment::class);
    }

    public function favorites() : \Illuminate\Database\Eloquent\Relations\HasMany 
    {
        return $this -> hasmany(Favorite::class);
    }

    // フォローされているユーザー
    public function followers() : \Illuminate\Database\Eloquent\Relations\BelongsToMany 
    {
        return $this -> belongsToMany(User::class, 'followers', 'followed_id', 'following_id');
    }

    // フォローしているユーザー
    public function follows() : \Illuminate\Database\Eloquent\Relations\BelongsToMany 
    {
        return $this -> belongsToMany(User::class, 'followers', 'following_id', 'followed_id');
    }
}
