<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'merchant_id',
        'api_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'merchant_id');
    }

    public function merchants()
    {
        return $this->belongsToMany(Merchant::class, 'user_merchant', 'user_id', 'merchant_id', 'id', 'merchant_id');
    }

    /**
     * Get merchant IDs this user can see (for scoping data).
     */
    public function getMerchantIds(): array
    {
        if ($this->role === 'merchant' && $this->merchant_id) {
            return [$this->merchant_id];
        }
        if ($this->role === 'agent') {
            return $this->merchants()->pluck('user_merchant.merchant_id')->toArray();
        }
        return [];
    }
}
