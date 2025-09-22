<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rainlab_location_postal_codes', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');  // Fixed missing semicolon
            $table->string('code', 4)->unique(); // Using unique constraint
            $table->unsignedInteger('municipality_id');
            $table->timestamps();    // Add timestamps for created_at/updated_at
            
            // Add foreign key constraint
            $table->foreign('municipality_id')
                  ->references('id')
                  ->on('rainlab_location_municipalities')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rainlab_location_postal_codes');
    }
};
