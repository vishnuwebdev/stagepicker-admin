<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

/**
 * The Species/Breed dropdowns on the mobile "Add Animal Profile" and "New
 * Animal Audition" forms are fed live from the admin-managed animal_species
 * / animal_breeds tables (per the confirmed "admin-managed dropdown lists"
 * decision). Those tables start empty — nothing seeds them automatically —
 * so until an admin adds rows via Admin > Animal Audition > Species/Breed
 * Management, both dropdowns legitimately show no options.
 *
 * This is a one-time data migration (not a Seeder, so it runs automatically
 * with the rest of `php artisan migrate` instead of requiring a separate
 * `db:seed` step) that pre-populates the exact species list the producer
 * originally asked for, plus a handful of common breeds per species so the
 * Breed dropdown isn't empty for the most common cases either. It's
 * intentionally idempotent (checked by name) so re-running migrate never
 * duplicates rows, and admins can still add more species/breeds afterward
 * through the admin panel exactly as before.
 */
class SeedInitialAnimalSpeciesAndBreeds extends Migration
{
    public function up()
    {
        $now = Carbon::now();

        $speciesWithBreeds = [
            'Dog' => ['German Shepherd', 'Labrador Retriever', 'Golden Retriever', 'Poodle', 'Bulldog'],
            'Cat' => ['Bengal Cat', 'Persian', 'Siamese', 'Maine Coon'],
            'Horse' => ['Arabian Horse', 'Thoroughbred', 'Quarter Horse'],
            'Bird' => ['Parrot', 'Cockatiel', 'Canary'],
            'Reptile' => ['Iguana', 'Bearded Dragon', 'Corn Snake'],
            'Exotic' => [],
            'Farm Animal' => ['Goat', 'Sheep', 'Pig', 'Cow', 'Chicken'],
        ];

        foreach ($speciesWithBreeds as $speciesName => $breeds) {
            $existing = DB::table('animal_species')->where('name', $speciesName)->first();

            if ($existing) {
                $speciesId = $existing->id;
            } else {
                $speciesId = DB::table('animal_species')->insertGetId([
                    'name' => $speciesName,
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach ($breeds as $breedName) {
                $breedExists = DB::table('animal_breeds')
                    ->where('animal_species_id', $speciesId)
                    ->where('name', $breedName)
                    ->exists();

                if (!$breedExists) {
                    DB::table('animal_breeds')->insert([
                        'animal_species_id' => $speciesId,
                        'name' => $breedName,
                        'status' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    public function down()
    {
        // Intentionally left as a no-op: rolling back would delete
        // admin-editable master data that may since have real
        // species/breeds attached to live animal profiles and posts.
    }
}
