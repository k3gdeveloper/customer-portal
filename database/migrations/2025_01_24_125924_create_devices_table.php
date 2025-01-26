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
        Schema::create('devices', function (Blueprint $table) {
            $table->id()->nullable(false);
            $table->bigInteger('id_company')->nullable(false);
            $table->integer('id_netbox')->nullable(false);
            $table->string('name')->nullable(false);
            $table->string('so')->nullable(false);
            $table->string('ip')->nullable(false);
            $table->string('ssh')->nullable(false);
            $table->tinyInteger('status_netbox')->default(0);
            $table->tinyInteger('status_zabbix')->default(0);
            $table->tinyInteger('rsakey')->default(0);
            $table->string('user');
            $table->string('password');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
