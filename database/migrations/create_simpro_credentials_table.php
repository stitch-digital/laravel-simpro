<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('simpro_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('base_url');
            $table->string('access_token');
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('simpro_credentials');
    }
};
