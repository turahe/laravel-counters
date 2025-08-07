<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $countersTable = config('counter.tables.table_name', 'counters');
        $counterablesTable = config('counter.tables.table_pivot_name', 'counterables');

        Schema::create($countersTable, function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->bigInteger('initial_value')->default(0);
            $table->bigInteger('value')->default(0);
            $table->bigInteger('step')->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['key']);
            $table->index(['name']);
            $table->index(['value']);
            $table->index(['created_at']);
            $table->index(['updated_at']);
        });

        Schema::create($counterablesTable, function (Blueprint $table) use ($countersTable) {
            $table->id();
            $table->morphs('counterable');
            $table->unsignedBigInteger('counter_id');
            $table->bigInteger('value')->default(0);
            $table->timestamps();

            // Add additional indexes for better performance (avoiding duplicate morphs indexes)
            $table->index(['counter_id']);
            $table->index(['value']);
            $table->index(['created_at']);
            $table->index(['updated_at']);

            // Add foreign key constraint
            $table->foreign('counter_id')
                  ->references('id')
                  ->on($countersTable)
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $countersTable = config('counter.tables.table_name', 'counters');
        $counterablesTable = config('counter.tables.table_pivot_name', 'counterables');
        
        Schema::dropIfExists($counterablesTable);
        Schema::dropIfExists($countersTable);
    }
};
