<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Passwordless, phone-first account (CLAUDE.md rule 7). One account works as buyer, seller and neighbor.
 * SMS-code login arrives in Milestone 3; this slice only needs the model for sellers.user_id.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'phone', 'email', 'home_zip'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
        ];
    }

    public function sellers(): HasMany
    {
        return $this->hasMany(Seller::class);
    }
}
