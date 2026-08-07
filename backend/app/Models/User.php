<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'name',
    'last_name',
    'dni',
    'username',
    'password'
])]
#[Hidden(['password', 'remember_token',])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
<<<<<<< Updated upstream
    use HasFactory, Notifiable, HasApiTokens;
=======
    use HasFactory, Notifiable,HasApiTokens;
>>>>>>> Stashed changes

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
