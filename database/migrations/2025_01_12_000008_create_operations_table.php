<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('operations')) {
            Schema::create('operations', function (Blueprint $table) {
                $table->id();
                $table->string('type');
                $table->string('description');
                $table->decimal('montant', 10, 2);
                $table->date('date_operation');
                $table->foreignId('user_id')->nullable();
                $table->timestamps();

                // Index
                $table->index('date_operation');
                $table->index('user_id');
                $table->index('type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('operations');
    }
};
