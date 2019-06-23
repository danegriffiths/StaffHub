<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('staff_number');
            $table->string('forename');
            $table->string('surname');
            $table->string('department');
            $table->time('daily_hours_permitted')->nullable();
            $table->time('weekly_hours_permitted')->nullable();
            $table->time('flexi_balance')->nullable();
            $table->time('latest_flexi_balance')->nullable();
            $table->boolean('clocking_status')->nullable();
            $table->boolean('manager');
            $table->boolean('administrator');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->unsignedInteger('manager_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
