<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // First, clean up any non-numeric values
        DB::statement("UPDATE users SET employee_id = NULL WHERE employee_id !~ '^[0-9]+$'");

        // Convert the column to integer
        DB::statement('ALTER TABLE users ALTER COLUMN employee_id TYPE INTEGER USING employee_id::integer');
    }

    public function down()
    {
        DB::statement('ALTER TABLE users ALTER COLUMN employee_id TYPE VARCHAR USING employee_id::varchar');
    }
};
