<?php

namespace App\Models;

use App\Enums\Plan;
use App\Enums\SellerStatus;
use App\Enums\SellerType as SellerTypeEnum;
use App\Observers\SellerObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property float|null $miles Selected by App\Queries\SellerSearch.
 */
#[ObservedBy(SellerObserver::class)]
class Seller extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Not fillable on purpose: user_id, status, verification_level, verified_at, plan, partner_rate_bps,
     * lead_fee_cents, claimed_at (set by verification, billing and claim flows, never by a form),
     * and location (a generated column computed by MySQL from lat/lng).
     */
    protected $fillable = [
        'slug', 'name', 'description', 'phone', 'email', 'website', 'instagram', 'facebook', 'social', 'order_url',
        'address_line', 'city', 'state', 'zip', 'county', 'lat', 'lng', 'show_exact_address',
        'coupon_code', 'source', 'founded_year', 'owners', 'processor', 'practices', 'awards',
        'permit_status', 'permit_number', 'permit_type',
        'delivers', 'delivery_radius_mi', 'delivery_fee_cents', 'delivery_min_cents', 'delivery_days',
        'ships', 'ships_scope', 'ships_states', 'ship_fee_cents', 'ship_days',
        'csa', 'csa_notes', 'csa_url', 'wholesale', 'wholesale_notes',
        'service_radius_mi', 'service_days', 'booking_window', 'booking_notes', 'promo_text', 'promo_url',
    ];

    /** Binary geometry; never serialise it. */
    protected $hidden = ['location'];

    protected function casts(): array
    {
        return [
            'status' => SellerStatus::class,
            'plan' => Plan::class,
            'lat' => 'decimal:6',
            'lng' => 'decimal:6',
            'show_exact_address' => 'boolean',
            'practices' => 'array',
            'awards' => 'array',
            'social' => 'array',
            'ships_states' => 'array',
            'delivers' => 'boolean',
            'ships' => 'boolean',
            'csa' => 'boolean',
            'wholesale' => 'boolean',
            'verified_at' => 'datetime',
            'claimed_at' => 'datetime',
            'products_updated_at' => 'datetime',
            'miles' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stateModel(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state', 'code');
    }

    public function types(): HasMany
    {
        return $this->hasMany(SellerType::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /** First-checked type is the main badge (docs/01). Uses the loaded relation when present. */
    public function primaryType(): ?SellerTypeEnum
    {
        $types = $this->types;

        return ($types->firstWhere('is_primary', true) ?? $types->first())?->type;
    }

    public function isVerified(): bool
    {
        return $this->status === SellerStatus::Verified;
    }

    /** Pro and Partner sellers carry the ★ Featured badge (docs/01, docs/06). */
    public function isFeatured(): bool
    {
        return $this->plan?->isPaid() ?? false;
    }

    public function isService(): bool
    {
        return $this->primaryType()?->isService() ?? false;
    }

    /**
     * Permit line beside the badges. Prepared food always discloses permit status (CLAUDE.md rule 4);
     * other sellers show their declared permit type, e.g. "TX cottage food".
     */
    public function permitLabel(): ?string
    {
        if ($this->primaryType() === SellerTypeEnum::HomeCook) {
            return $this->permit_status === 'permitted' ? 'Permitted' : 'Home kitchen · no permit on file';
        }

        return $this->permit_type;
    }

    /**
     * The "how they sell" chips on a result row. Service providers list their trades (approved tags in
     * the services group); everyone else lists buy methods held on the seller row. Farm stand and market
     * chips need seller_hours / market_attendances, which arrive with Milestone 2.
     *
     * @return list<string>
     */
    public function buyChips(): array
    {
        if ($this->isService()) {
            return $this->tags->where('group', 'services')->pluck('name')->take(3)->values()->all();
        }

        return array_values(array_filter([
            $this->csa ? 'CSA' : null,
            $this->delivers ? 'Delivers'.($this->delivery_radius_mi ? " {$this->delivery_radius_mi} mi" : '') : null,
            $this->ships ? ($this->ships_scope === 'nationwide' ? 'Ships nationwide' : 'Ships') : null,
            $this->wholesale ? 'Wholesale' : null,
        ]));
    }

    /** "22 mi", "1.1 mi" — one decimal under 2 miles, as in the design. */
    public function distanceLabel(): ?string
    {
        if ($this->miles === null) {
            return null;
        }

        return ($this->miles < 2 ? number_format($this->miles, 1) : (string) round($this->miles)).' mi';
    }
}
