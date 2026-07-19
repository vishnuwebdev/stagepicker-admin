<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Mock merchandise products + gallery images, so there's real data to look
 * at through /get-data and /get-merchandise-detail once the Flutter side is
 * wired up (today lib/marketplace/ only renders static/mock product lists —
 * see platform/CLAUDE.md).
 *
 * Re-runnable: clears out any previously-seeded row with a matching title
 * before inserting, so running this twice doesn't create duplicates.
 *
 * Run with: php artisan db:seed --class=MerchandiseSeeder
 */
class MerchandiseSeeder extends Seeder
{
    // All mock rows point at this placeholder image, which already exists
    // in public/admin/uploads/merchandise/. Swap in real product photos
    // later through the admin panel (admin/edit-merchandise/{id}) — this
    // seeder only exists to unblock FE integration testing, not to be the
    // real catalog.
    private const PLACEHOLDER_IMAGE = '1662185677-a2techno.png';

    public function run()
    {
        $products = [
            [
                'title' => 'Acthound Hoodie',
                'description' => 'Premium fleece hoodie featuring the Acthound logo.',
                'price' => 45,
                'sale_price' => 35,
                'size' => 'S,M,L,XL',
            ],
            [
                'title' => 'Acthound T-Shirt',
                'description' => 'Soft cotton tee with the Acthound Casting print.',
                'price' => 25,
                'sale_price' => null,
                'size' => 'S,M,L,XL',
            ],
            [
                'title' => 'Acthound Cap',
                'description' => 'Adjustable cap embroidered with the Acthound logo.',
                'price' => 20,
                'sale_price' => 15,
                'size' => 'One Size',
            ],
            [
                'title' => 'Acthound Mug',
                'description' => 'Ceramic mug with the Acthound logo, dishwasher safe.',
                'price' => 12,
                'sale_price' => null,
                'size' => null,
            ],
            [
                'title' => 'Acthound Tote Bag',
                'description' => 'Canvas tote bag for scripts, headshots, and everything else.',
                'price' => 18,
                'sale_price' => null,
                'size' => 'One Size',
            ],
            [
                'title' => 'Acthound Water Bottle',
                'description' => 'Insulated steel water bottle with the Acthound logo.',
                'price' => 22,
                'sale_price' => 18,
                'size' => null,
            ],
        ];

        foreach ($products as $product) {
            // Remove any earlier seeded row with the same title first, so
            // this seeder can be re-run safely without piling up duplicates.
            $existing = DB::table('merchandise')->where('title', $product['title'])->first();
            if ($existing) {
                DB::table('merchandise_images')->where('merchandise_id', $existing->id)->delete();
                DB::table('merchandise')->where('id', $existing->id)->delete();
            }

            $merchandiseId = DB::table('merchandise')->insertGetId([
                'title' => $product['title'],
                'description' => $product['description'],
                'price' => $product['price'],
                'sale_price' => $product['sale_price'],
                'size' => $product['size'],
                'image' => self::PLACEHOLDER_IMAGE,
                'status' => 1,
            ]);

            // One extra gallery row so the FE's image carousel has more
            // than a single photo to page through.
            DB::table('merchandise_images')->insert([
                'merchandise_id' => $merchandiseId,
                'image' => self::PLACEHOLDER_IMAGE,
            ]);
        }
    }
}
