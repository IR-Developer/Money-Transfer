<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('src_id')->unsigned();
            $table->foreignId('des_id')->unsigned();
            $table->boolean('status');
            $table->unsignedBigInteger('amount');
            $table->string('bank_ref_code', 12)->unique();
            $table->unsignedBigInteger('our_ref_code')->unique();
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
        Schema::dropIfExists('transfers');
    }
};
