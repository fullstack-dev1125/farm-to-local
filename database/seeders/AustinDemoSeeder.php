<?php

namespace Database\Seeders;

use App\Enums\Plan;
use App\Enums\SellerStatus;
use App\Enums\SellerType as T;
use App\Models\Category;
use App\Models\Seller;
use App\Models\SellerType;
use App\Models\Tag;
use App\Models\Zip;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Demo sellers around Austin, TX (docs/02 "Seeds").
 *
 * The nine named sellers sit at their real towns' coordinates, so distances are true geography rather
 * than numbers arranged to suit a test. Blue Oak Ranch (Dripping Springs, 30.19 / -98.09) is ~21 mi
 * from the 78701 centroid: inside 25 mi, outside 10 mi.
 *
 * The rest is padded to 50 with factory sellers placed on real ZIP centroids in distance bands from
 * 78701, with a deliberate mix of plans and statuses so the ordering rule and status filter are exercised.
 * Faker is seeded, so the demo is identical on every run.
 */
class AustinDemoSeeder extends Seeder
{
    private const CENTRE = [30.2713, -97.7426]; // 78701

    public function run(): void
    {
        fake()->seed(2026);

        foreach ($this->namedSellers() as $row) {
            $this->createSeller($row);
        }

        $this->pad(total: 50);
    }

    /** @return list<array<string, mixed>> */
    private function namedSellers(): array
    {
        return [
            [
                'name' => 'Blue Oak Ranch', 'city' => 'Dripping Springs', 'zip' => '78620', 'lat' => 30.190000, 'lng' => -98.090000,
                'types' => [T::Farm], 'status' => SellerStatus::Verified, 'plan' => Plan::Partner,
                'categories' => ['meat-poultry', 'eggs-dairy'],
                'tags' => [['meat', 'Beef'], ['meat', 'Chicken'], ['meat', 'Bulk · quarter / half / whole'], ['eggs', 'Chicken eggs']],
                'description' => 'Grass-fed and finished beef, pastured chicken, eggs. Quarter and half beef reservations open for November.',
                'promo_text' => 'In season: ground beef, whole chicken, eggs',
                'awards' => ['Producer of the Year 2025 · Texas Beef Council', 'Best pastured chicken · Dripping Springs Fair'],
                'practices' => ['Grass-fed & finished', 'Pasture-raised', 'No antibiotics'],
                'ships' => true, 'ships_scope' => 'states', 'ships_states' => ['TX'],
                'founded_year' => 2011, 'owners' => 'The Ortega family',
            ],
            [
                'name' => 'Hollow Creek Bakehouse', 'city' => 'East Austin', 'zip' => '78702', 'lat' => 30.262600, 'lng' => -97.719600,
                'types' => [T::Bakery], 'status' => SellerStatus::Verified, 'plan' => Plan::Pro,
                'categories' => ['bakery-sweets'],
                'tags' => [['bakery', 'Sourdough'], ['bakery', 'Bagels'], ['bakery', 'Pies & cobblers']],
                'description' => 'Naturally leavened sourdough, bagels on Saturdays, seasonal fruit pies. Pre-order by Thursday for weekend pickup.',
                'promo_text' => 'This week: pear galette, rye',
                'awards' => ['Best Bread 2025 · Austin Chronicle readers'],
            ],
            [
                'name' => 'Gulf Line Seafood', 'city' => 'Port Aransas', 'zip' => '78373', 'lat' => 27.833900, 'lng' => -97.061100,
                'types' => [T::Fisherman], 'status' => SellerStatus::Verified, 'plan' => Plan::Pro,
                'categories' => ['seafood'],
                'tags' => [['seafood', 'Shrimp'], ['seafood', 'Finfish'], ['seafood', 'CSF share']],
                'description' => 'Weekly CSF box — whatever the boat brought in, flash-frozen at the dock. Shrimp, snapper, oysters.',
                'promo_text' => 'Ships TX overnight · $25 flat',
                'practices' => ['Wild-caught'],
                'ships' => true, 'ships_scope' => 'states', 'ships_states' => ['TX'], 'ship_fee_cents' => 2500, 'csa' => true,
            ],
            [
                'name' => 'Cedar Bend Nursery', 'city' => 'Manor', 'zip' => '78653', 'lat' => 30.340800, 'lng' => -97.556900,
                'types' => [T::Nursery], 'status' => SellerStatus::Verified, 'plan' => Plan::Free,
                'categories' => ['plants-starts'],
                'tags' => [['plants', 'Vegetable starts'], ['plants', 'Fruit trees'], ['plants', 'Native plants']],
                'description' => 'Strawberry starts, bare-root fruit trees, Texas natives and vegetable transplants grown on site.',
                'promo_text' => 'Plant now: strawberry starts, garlic, onion sets',
                'ships' => true, 'ships_scope' => 'states', 'ships_states' => ['TX'],
            ],
            [
                'name' => 'Two Sisters Cottage Kitchen', 'city' => 'Round Rock', 'zip' => '78664', 'lat' => 30.508300, 'lng' => -97.678900,
                'types' => [T::HomeMaker], 'status' => SellerStatus::Verified, 'plan' => Plan::Free,
                'categories' => ['pantry-preserves', 'wellness-body'],
                'tags' => [['pantry', 'Jams & jellies'], ['wellness', 'Tallow balm & salves'], ['wellness', 'Tinctures & elderberry']],
                'description' => 'Small-batch jams, elderberry syrup, grass-fed tallow balm and lip salve. Everything made in Round Rock.',
                'promo_text' => 'New: fig preserves',
                'awards' => ['Blue ribbon, preserves · Williamson County Fair 2024'],
                'permit_type' => 'TX cottage food',
                'delivers' => true, 'delivery_radius_mi' => 20, 'delivery_fee_cents' => 500,
            ],
            [
                // Seeded from public records and unclaimed: excluded from search until claimed (status seeded).
                'name' => 'Wimberley Valley Honey', 'city' => 'Wimberley', 'zip' => '78676', 'lat' => 29.997400, 'lng' => -98.098600,
                'types' => [T::Bakery], 'status' => SellerStatus::Seeded, 'plan' => Plan::Free,
                'categories' => ['pantry-preserves'],
                'tags' => [['pantry', 'Honey & bee products']],
                'description' => 'Raw wildflower honey by the jar or the case.',
                'source' => 'Texas Apiary Inspection Service registry',
            ],
            [
                'name' => 'Stonewall Peach Co.', 'city' => 'Stonewall', 'zip' => '78671', 'lat' => 30.231000, 'lng' => -98.659700,
                'types' => [T::Farm], 'status' => SellerStatus::Listed, 'plan' => Plan::Free,
                'categories' => ['produce', 'pantry-preserves'],
                'tags' => [['produce', 'Stone fruit'], ['pantry', 'Jams & jellies']],
                'description' => 'Peaches June–Aug by the half-bushel, jam and cobbler kits year-round.',
                'practices' => ['No spray'],
                'ships' => true, 'ships_scope' => 'nationwide',
            ],
            [
                'name' => 'High Plains Lamb Co.', 'city' => 'Amarillo', 'zip' => '79101', 'lat' => 35.222000, 'lng' => -101.831300,
                'types' => [T::Farm], 'status' => SellerStatus::Verified, 'plan' => Plan::Free,
                'categories' => ['meat-poultry'],
                'tags' => [['meat', 'Lamb & goat']],
                'description' => 'Grass-finished lamb by the cut or the half. Dry-ice packed, ships Mondays.',
                'practices' => ['Grass-fed & finished'],
                'ships' => true, 'ships_scope' => 'nationwide', 'ship_days' => 'Mon',
            ],
            [
                'name' => 'Hill Country Mobile Processing', 'city' => 'Johnson City', 'zip' => '78636', 'lat' => 30.276900, 'lng' => -98.412000,
                'types' => [T::Service], 'status' => SellerStatus::Verified, 'plan' => Plan::Pro,
                'categories' => ['farm-garden-services', 'meat-poultry'],
                'tags' => [['services', 'Mobile butchering / on-farm harvest'], ['services', 'Cut & wrap / processing'], ['services', 'Classes & workshops']],
                'description' => 'On-farm harvest for beef, hogs, lamb and goats with cut-and-wrap. Poultry processing days, 50-bird minimum. Saturday butchering class.',
                'promo_text' => 'Beef from $175 + $1.10/lb hanging · booking 3–5 weeks out',
                'awards' => ['Texas Meat Processors Assn · Member since 2019'],
                'permit_type' => 'Custom-exempt registered',
                'service_radius_mi' => 60, 'service_days' => 'Mon–Sat', 'booking_window' => '3–5 weeks out',
            ],
        ];
    }

    /**
     * Distance bands from 78701 → [count, plan/status pattern]. Factory sellers sit on real ZIP centroids
     * from zips.csv; the two out-of-state sellers use explicit coordinates.
     */
    private function pad(int $total): void
    {
        $bands = [
            [0, 10, 12],
            [10, 25, 11],
            [25, 50, 10],
            [50, 999, 6],
        ];

        $zips = Zip::all()->map(function (Zip $z) {
            $z->setAttribute('miles', $this->miles((float) $z->lat, (float) $z->lng));

            return $z;
        });

        // Deterministic mix: index → [status, plan]. Paid sellers appear in every band so "non-free first"
        // is tested against closer free sellers; seeded/suspended ones must never appear in results.
        $mix = [
            [SellerStatus::Listed, Plan::Free], [SellerStatus::Verified, Plan::Free], [SellerStatus::Listed, Plan::Pro],
            [SellerStatus::Verified, Plan::Free], [SellerStatus::Seeded, Plan::Free], [SellerStatus::Listed, Plan::Free],
            [SellerStatus::Verified, Plan::Partner], [SellerStatus::Listed, Plan::Free], [SellerStatus::Suspended, Plan::Free],
            [SellerStatus::Verified, Plan::Free],
        ];

        $i = 0;
        foreach ($bands as [$min, $max, $count]) {
            // Keep clear of band edges so rounding never moves a seller into the neighbouring band.
            // Evenly spaced picks across the distance-sorted pool: deterministic and spread through the band.
            $pool = $zips->filter(fn ($z) => $z->miles > $min + 0.5 && $z->miles < $max - 0.5)->sortBy('miles')->values();
            $step = max(1, intdiv($pool->count(), $count));
            $candidates = $pool->filter(fn ($z, $k) => $k % $step === 0)->take($count);

            foreach ($candidates as $zip) {
                [$status, $plan] = $mix[$i++ % count($mix)];
                $this->createFactorySeller($zip->lat, $zip->lng, $zip->city, 'TX', $zip->zip, $status, $plan);
            }
        }

        // Two out-of-state sellers; the Pro one proves paid placement never pulls in far-away sellers.
        $this->createFactorySeller(35.686975, -105.937799, 'Santa Fe', 'NM', '87501', SellerStatus::Verified, Plan::Pro);
        $this->createFactorySeller(36.153982, -95.992775, 'Tulsa', 'OK', '74103', SellerStatus::Listed, Plan::Free);

        // Top up if the ZIP sample was too sparse for a band (never happens with the committed CSV).
        while (Seller::count() < $total) {
            $this->createFactorySeller(30.35, -97.70, 'Austin', 'TX', '78701', SellerStatus::Listed, Plan::Free);
        }
    }

    private function createFactorySeller(float $lat, float $lng, ?string $city, string $state, string $zip, SellerStatus $status, Plan $plan): void
    {
        $type = fake()->randomElement([T::Farm, T::Farm, T::Farm, T::Bakery, T::HomeMaker, T::Nursery, T::HomeCook, T::Fisherman]);
        $categories = match ($type) {
            T::Farm => fake()->randomElements(['produce', 'meat-poultry', 'eggs-dairy'], fake()->numberBetween(1, 2)),
            T::Bakery => ['bakery-sweets'],
            T::HomeMaker => ['pantry-preserves'],
            T::Nursery => ['plants-starts'],
            T::HomeCook => ['prepared-food'],
            T::Fisherman => ['seafood'],
        };

        $seller = Seller::factory()
            ->at($lat, $lng, $city, $state)
            ->status($status)
            ->plan($plan)
            ->ofType($type)
            ->create([
                'zip' => $zip,
                'delivers' => fake()->boolean(25),
                'delivery_radius_mi' => 15,
                'ships' => fake()->boolean(20),
                'csa' => $type === T::Farm && fake()->boolean(30),
                'permit_status' => $type === T::HomeCook ? fake()->randomElement(['permitted', 'no_permit']) : 'none',
            ]);

        $seller->categories()->attach(Category::whereIn('slug', $categories)->pluck('id'));
    }

    private function createSeller(array $row): void
    {
        $seller = new Seller(collect($row)->except(['types', 'status', 'plan', 'categories', 'tags'])->all()
            + ['slug' => Str::slug($row['name']), 'state' => 'TX', 'source' => $row['source'] ?? 'demo']);
        $seller->forceFill([
            'status' => $row['status'],
            'plan' => $row['plan'],
            'verification_level' => $row['status'] === SellerStatus::Verified ? 1 : 0,
            'verified_at' => $row['status'] === SellerStatus::Verified ? now()->subMonths(3) : null,
            'partner_rate_bps' => $row['plan'] === Plan::Partner ? 600 : null,
        ])->save();

        foreach ($row['types'] as $i => $type) {
            SellerType::create(['seller_id' => $seller->id, 'type' => $type, 'is_primary' => $i === 0]);
        }

        $seller->categories()->attach(Category::whereIn('slug', $row['categories'])->pluck('id'));

        $tagIds = collect($row['tags'])->map(fn ($t) => Tag::firstOrCreate(
            ['slug' => Str::slug($t[1])],
            ['name' => $t[1], 'group' => $t[0]],
        )->id);
        $seller->tags()->attach($tagIds);
    }

    private function miles(float $lat, float $lng): float
    {
        [$clat, $clng] = self::CENTRE;
        $dLat = deg2rad($lat - $clat);
        $dLng = deg2rad($lng - $clng);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($clat)) * cos(deg2rad($lat)) * sin($dLng / 2) ** 2;

        return 2 * 3958.7613 * asin(sqrt($a));
    }
}
