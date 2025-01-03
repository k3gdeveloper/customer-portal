<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageBase64ToLinksTable extends Migration
{
    public function up()
    {
        Schema::table('links', function (Blueprint $table) {
            $table->text('image_base64')->nullable(); // Adicione a coluna image_base64
        });
    }

    public function down()
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn('image_base64');
        });
    }
}
