<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_CATALOG = 'catalog';

    public const ROLE_CONTENT = 'content';

    public const ROLE_SALES = 'sales';

    public const ROLE_SALES_FULL = 'sales_full';

    public const ROLE_SALES_LEADS_ONLY = 'sales_leads_only';

    public const ROLE_SALES_PRODUCTS_LEADS = 'sales_products_leads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'role',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin || $this->role === self::ROLE_ADMIN;
    }

    public function hasRole(string $role): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->role === $role;
    }

    /**
     * @param  array<int, string>  $roles
     */
    public function hasAnyRole(array $roles): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return in_array($this->role, $roles, true);
    }
}
