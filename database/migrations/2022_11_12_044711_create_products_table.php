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
        CustomSchema::connection()->create('products', function (CustomBlueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('image');
            $table->authorField();
            $table->softDeletes();
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
        CustomSchema::connection()->dropIfExists('products');
    }
};
