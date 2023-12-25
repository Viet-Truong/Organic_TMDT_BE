<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    const BUYER = 'Người mua hàng';
    const SELLER = 'Người bán hàng';

    const STATUS_ACTIVE = 'Hoạt động';
    const STATUS_LOCKED = 'Khoá';
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'role',
        'status',
        'dob',
        'card_id',
        'phone_number',
        'address',
        'gender',
        'avatar',
        'verification_token',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'shop_id', 'id');
    }

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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
