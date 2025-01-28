<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jump_proxy', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_company')->unsigned();
            $table->foreign('id_company')->references('id')->on('companys')->onDelete('cascade');
            $table->string('host')->nullable(false);
            $table->string('ssh')->nullable(false);
            $table->string('user');
            $table->string('ip_netbox')->nullable(false);
            $table->string('token_netbox')->nullable(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jump_proxy');
    }
};
