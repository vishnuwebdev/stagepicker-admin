<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marketplace order domain — backs the Flutter "Marketplace 2.0" checkout,
 * order history, order detail, invoice, and return/cancel-request screens
 * (see platform/lib/marketplace/), which previously only ever wrote to
 * in-memory GetX controllers. `orders` holds one row per placed order
 * (mirrors `MarketplaceOrder` in marketplace_models.dart); `order_items`
 * holds the line items (mirrors `MarketplaceCartItem`).
 *
 * Follows the same "safe against an untracked live schema" pattern as
 * 2026_07_19_000002_add_columns_to_merchandise_tables.php: both tables are
 * net new here, so this is a straightforward create, but Schema::hasTable
 * guards are kept for consistency in case a same-named table already exists
 * on the live (unmigrated) database.
 */
class CreateOrdersTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('order_code')->unique();

                // placed | processing | shipped | delivered | cancelled |
                // return_requested | returned — mirrors MarketplaceOrderStatus.
                $table->string('status')->default('placed');

                $table->decimal('subtotal', 10, 2)->default(0);
                $table->decimal('shipping_amount', 10, 2)->default(0);
                $table->decimal('total', 10, 2)->default(0);

                // credit_card | apple_pay | google_pay
                $table->string('payment_method')->nullable();
                // Display string shown to the buyer, e.g. "Card •••• 4242".
                $table->string('payment_summary')->nullable();
                // approved | declined — every persisted order is 'approved';
                // declined attempts never reach the DB (see OrderController).
                $table->string('payment_status')->default('approved');

                $table->string('tracking_number')->nullable();

                // Shipping address snapshot at time of order (merchandise is
                // physically shipped; classes/webinars/seminars don't use this).
                $table->string('shipping_full_name')->nullable();
                $table->string('shipping_phone')->nullable();
                $table->string('shipping_address_line1')->nullable();
                $table->string('shipping_address_line2')->nullable();
                $table->string('shipping_city')->nullable();
                $table->string('shipping_state')->nullable();
                $table->string('shipping_postal_code')->nullable();

                // Return/cancel request — resolution stays a manual, human
                // admin-to-customer follow-up for now (see CLAUDE.md ask);
                // this just gives admin a place to see the request landed.
                $table->string('return_reason')->nullable();
                $table->text('return_description')->nullable();
                $table->timestamp('return_requested_at')->nullable();

                $table->timestamps();

                $table->index('user_id');
            });
        }

        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                // Nullable: keeps this table usable even if a line item was
                // built from a since-deleted merchandise row, or (future)
                // from a class/webinar/seminar catalog item instead.
                $table->unsignedBigInteger('merchandise_id')->nullable();

                // Snapshot fields — deliberately duplicated off `merchandise`
                // rather than joined live, so an order's line items keep
                // showing what the buyer actually saw/paid even if the
                // product is later renamed, repriced, or deleted.
                $table->string('title');
                $table->string('variant_label')->nullable();
                $table->decimal('unit_price', 10, 2);
                $table->unsignedInteger('quantity')->default(1);
                $table->string('image')->nullable();

                $table->timestamps();

                $table->index('order_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
}
