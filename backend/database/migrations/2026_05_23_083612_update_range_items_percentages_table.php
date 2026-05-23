<?php

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
        Schema::table('range_items', function (Blueprint $table) {

            $table->dropColumn('action');

            $table->integer('raise_percentage')
                ->default(0);

            $table->integer('call_percentage')
                ->default(0);

            $table->integer('fold_percentage')
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('range_items', function (Blueprint $table) {

            $table->dropColumn([
                'raise_percentage',
                'call_percentage',
                'fold_percentage',
            ]);

            $table->string('action')
                ->default('raise');
        });
    }
};