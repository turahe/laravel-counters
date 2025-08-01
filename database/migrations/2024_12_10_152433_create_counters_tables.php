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
        $countersTable = config('counters.tables.table_name', 'counters');
        $counterablesTable = config('counters.tables.table_pivot_name', 'counterables');

        Schema::create($countersTable, function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->double('initial_value')->default(0);
            $table->double('value')->default(0);
            $table->double('step')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create($counterablesTable, function (Blueprint $table) {
            $table->id();
            $table->morphs('counterable');
            $table->unsignedBigInteger('counter_id');
            $table->double('value')->default(0);
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
        $countersTable = config('counters.tables.table_name', 'counters');
        $counterablesTable = config('counters.tables.table_pivot_name', 'counterables');
        Schema::dropIfExists($counterablesTable);
        Schema::dropIfExists($countersTable);
    }
};
