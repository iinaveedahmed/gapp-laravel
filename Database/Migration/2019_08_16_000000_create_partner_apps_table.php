<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePartnerAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_apps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('provider', '45')->nullable();
            $table->string('api_key', '45')->unique();
            $table->dateTime('expire_date');
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->default(now());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_apps');
    }
}
