<?php

use Illuminate\Database\Migrations\Migration;
use App\Libraries\CustomBlueprint;
use App\Libraries\CustomSchema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        CustomSchema::connection()->create('category', function (CustomBlueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->authorField();
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
        CustomSchema::connection()->dropIfExists('category');
    }
};
