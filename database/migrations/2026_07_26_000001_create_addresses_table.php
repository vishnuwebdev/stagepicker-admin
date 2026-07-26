<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Saved shipping addresses, reusable across checkouts — lets the Flutter
 * checkout screen (platform/lib/marketplace/screens/mp_checkout.dart) offer
 * "pick a saved address or add a new one" instead of always starting from a
 * blank form. An order's own shipping_* columns (see
 * 2026_07_25_000001_create_orders_tables.php) remain an immutable snapshot
 * taken at checkout time — editing a saved address later, or editing the
 * form after selecting one, never rewrites a past order's delivery address.
 */
class CreateAddressesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('addresses')) {
            Schema::create('addresses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('full_name');
                $table->string('phone');
                $table->string('address_line1');
                $table->string('address_line2')->nullable();
                $table->string('city');
                $table->string('state');
                $table->string('postal_code');
                $table->boolean('is_default')->default(false);
                $table->timestamps();

                $table->index('user_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
