<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The `merchandise` and `merchandise_images` tables already exist on the live
 * database, but no migration file ever created them (see stagePicker/CLAUDE.md
 * for background). This migration is written to be safe either way:
 *
 *  - On the live DB: the tables already exist, so we only ADD the columns
 *    that are missing (Schema::hasColumn checks) and never drop anything.
 *  - On a fresh local DB (new developer running `php artisan migrate` from
 *    scratch): the tables don't exist yet, so we create them here with the
 *    full column list.
 *
 * `merchandise_dates` is intentionally left untouched — nothing in the
 * codebase currently reads or writes any column on it, so its purpose isn't
 * clear yet and it's out of scope for this change.
 */
class AddColumnsToMerchandiseTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('merchandise')) {
            Schema::create('merchandise', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->unsignedInteger('price');
                $table->unsignedInteger('sale_price')->nullable();
                $table->string('size')->nullable();
                $table->string('image')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        } else {
            Schema::table('merchandise', function (Blueprint $table) {
                if (!Schema::hasColumn('merchandise', 'title')) {
                    $table->string('title')->nullable();
                }
                if (!Schema::hasColumn('merchandise', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('merchandise', 'price')) {
                    $table->unsignedInteger('price')->default(0);
                }
                if (!Schema::hasColumn('merchandise', 'sale_price')) {
                    $table->unsignedInteger('sale_price')->nullable();
                }
                if (!Schema::hasColumn('merchandise', 'size')) {
                    $table->string('size')->nullable();
                }
                if (!Schema::hasColumn('merchandise', 'image')) {
                    $table->string('image')->nullable();
                }
                if (!Schema::hasColumn('merchandise', 'status')) {
                    $table->tinyInteger('status')->default(1);
                }
            });
        }

        if (!Schema::hasTable('merchandise_images')) {
            Schema::create('merchandise_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('merchandise_id');
                $table->string('image');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('merchandise')) {
            Schema::table('merchandise', function (Blueprint $table) {
                if (Schema::hasColumn('merchandise', 'sale_price')) {
                    $table->dropColumn('sale_price');
                }
            });
        }
    }
}
