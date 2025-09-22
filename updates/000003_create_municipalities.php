<?php
// create_municipalities_table.php
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rainlab_location_municipalities', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');  // Fixed missing semicolon
            $table->string('code', 4);
            $table->timestamps();    // Add timestamps for created_at/updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('rainlab_location_municipalities');
    }
};
