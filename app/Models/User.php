<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Filterable, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'reset_password_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'reset_password_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function scopeSearch($query, $attributes, string $keyword): void
    {
        foreach(Arr::wrap($attributes) as $attribute) {
            $query->orWhere($attribute, 'like', "%{$keyword}%");
        }
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function comments(): HasManyThrough
    {
        return $this->hasManyThrough(Comment::class, Post::class);
    }

    public function remove(): bool
    {
        if ($this->posts->isNotEmpty() || $this === auth()->user()) {
            return false;
        }

        $this->tokens
            ->each(function ($token, $key) {
                $this->revokeAccessAndRefreshTokens($token->id);
            });

        $this->delete();

        return true;
    }

    protected function revokeAccessAndRefreshTokens($tokenId): void
    {
        $tokenRepository = app('Laravel\Passport\TokenRepository');
        $refreshTokenRepository = app('Laravel\Passport\RefreshTokenRepository');

        $tokenRepository->revokeAccessToken($tokenId);
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);
    }
}
