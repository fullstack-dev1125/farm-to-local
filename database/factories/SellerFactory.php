<?php

namespace Database\Factories;

use App\Enums\Plan;
use App\Enums\SellerStatus;
use App\Enums\SellerType as SellerTypeEnum;
use App\Models\Seller;
use App\Models\SellerType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Defaults to a listed, free Texas seller somewhere near Austin. status and plan are not fillable, so the
 * factory sets them with forceFill() in the state helpers below.
 *
 * @extends Factory<Seller>
 */
class SellerFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            fake()->lastName().' Family Farm', fake()->lastName().' Creek Ranch', fake()->firstName()."'s Garden",
            fake()->lastName().' Orchard', fake()->lastName().' Bakery', fake()->lastName().' Pastures',
        ]);

        return [
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'name' => $name,
            'description' => fake()->sentence(14),
            'phone' => '+1512'.fake()->numerify('#######'),
            'city' => fake()->randomElement(['Austin', 'Pflugerville', 'Buda', 'Kyle', 'Leander', 'Bastrop']),
            'state' => 'TX',
            'zip' => '78701',
            'lat' => fake()->latitude(30.10, 30.45),
            'lng' => fake()->longitude(-97.95, -97.55),
            'source' => 'factory',
        ];
    }

    public function at(float $lat, float $lng, ?string $city = null, string $state = 'TX'): static
    {
        return $this->state(fn () => array_filter(
            ['lat' => $lat, 'lng' => $lng, 'city' => $city, 'state' => $state],
            fn ($v) => $v !== null,
        ));
    }

    public function status(SellerStatus $status): static
    {
        return $this->afterMaking(fn (Seller $s) => $s->forceFill([
            'status' => $status,
            'verification_level' => $status === SellerStatus::Verified ? 1 : 0,
            'verified_at' => $status === SellerStatus::Verified ? now() : null,
        ]));
    }

    public function listed(): static
    {
        return $this->status(SellerStatus::Listed);
    }

    public function verified(): static
    {
        return $this->status(SellerStatus::Verified);
    }

    public function seeded(): static
    {
        return $this->status(SellerStatus::Seeded);
    }

    public function suspended(): static
    {
        return $this->status(SellerStatus::Suspended);
    }

    public function plan(Plan $plan): static
    {
        return $this->afterMaking(fn (Seller $s) => $s->forceFill([
            'plan' => $plan,
            'partner_rate_bps' => $plan === Plan::Partner ? 600 : null,
        ]));
    }

    public function pro(): static
    {
        return $this->plan(Plan::Pro);
    }

    public function partner(): static
    {
        return $this->plan(Plan::Partner);
    }

    /** Attach the given types; the first is primary (docs/01). */
    public function ofType(SellerTypeEnum ...$types): static
    {
        return $this->afterCreating(function (Seller $s) use ($types) {
            foreach ($types as $i => $type) {
                SellerType::create(['seller_id' => $s->id, 'type' => $type, 'is_primary' => $i === 0]);
            }
        });
    }
}
