<?php

namespace App\Models;

use App\Traits\AuditedBySoftDelete;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use AuditedBySoftDelete, HasFactory, Notifiable, SoftDeletes, HasRoles, HasPanelShield;

    protected $table = 'users';

    protected $guarded = ['id'];

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

    public function wallets(): BelongsToMany
    {
        return $this->belongsToMany(Wallet::class, 'users_wallet')
            ->using(UserWallet::class)
            ->withTimestamps();
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasAnyRole(['super admin', 'super-admin']);
    }

    public function isPengguna(): bool
    {
        return $this->hasRole('pengguna');
    }
}
