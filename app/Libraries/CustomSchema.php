<?php

namespace App\Libraries;

use App\Libraries\CustomBlueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\MySqlBuilder;
use Illuminate\Database\Schema\PostgresBuilder;
use Illuminate\Support\Facades\DB;

class CustomSchema
{
    /**
     * Get a schema builder instance for a connection.
     *
     * @param  string|null  $name
     * @return Builder|MySqlBuilder|PostgresBuilder
     */
    public static function connection()
    {
        $schema = DB::connection()->getSchemaBuilder();
        $schema->blueprintResolver(function ($table, $callback) {
            return new CustomBlueprint($table, $callback);
        });

        return $schema;
    }
}
