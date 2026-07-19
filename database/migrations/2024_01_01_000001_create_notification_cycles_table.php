<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationCyclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_cycles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('cycle_day')->comment('Day in cycle: 2, 4, 7, 10, or 14');
            $table->integer('message_index_day2')->default(0)->comment('Current message index for Day 2');
            $table->integer('message_index_day4')->default(0)->comment('Current message index for Day 4');
            $table->integer('message_index_day7')->default(0)->comment('Current message index for Day 7');
            $table->integer('message_index_day10')->default(0)->comment('Current message index for Day 10');
            $table->integer('message_index_day14')->default(0)->comment('Current message index for Day 14');
            $table->date('cycle_start_date');
            $table->date('last_notification_date')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id');
            $table->index('cycle_start_date');
            $table->index('last_notification_date');
            $table->unique(['user_id', 'last_notification_date']); // Prevent duplicate notifications on same day
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_cycles');
    }
}
