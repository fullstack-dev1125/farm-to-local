<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Loads database/data/zips.csv (zip,lat,lng,city,state,county). The committed file is a 200-row Texas
 * sample (GeoNames US postal codes, CC BY 4.0); the owner will drop in the full ~33k-row file later.
 * Upserts in chunks, so it is idempotent and fast on the full file.
 */
class ZipSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/zips.csv');
        $fh = fopen($path, 'r') ?: throw new RuntimeException("Cannot open {$path}");

        $header = fgetcsv($fh);
        if ($header !== ['zip', 'lat', 'lng', 'city', 'state', 'county']) {
            throw new RuntimeException('zips.csv must have the header zip,lat,lng,city,state,county');
        }

        $batch = [];
        while (($row = fgetcsv($fh)) !== false) {
            if (count($row) !== 6 || ! preg_match('/^\d{5}$/', $row[0])) {
                continue;
            }
            [$zip, $lat, $lng, $city, $state, $county] = $row;
            $batch[] = [
                'zip' => $zip,
                'lat' => round((float) $lat, 6),
                'lng' => round((float) $lng, 6),
                'city' => $city !== '' ? $city : null,
                'state' => strtoupper($state),
                'county' => $county !== '' ? $county : null,
            ];
            if (count($batch) === 1000) {
                $this->flush($batch);
            }
        }
        $this->flush($batch);
        fclose($fh);
    }

    private function flush(array &$batch): void
    {
        if ($batch) {
            DB::table('zips')->upsert($batch, ['zip'], ['lat', 'lng', 'city', 'state', 'county']);
            $batch = [];
        }
    }
}
