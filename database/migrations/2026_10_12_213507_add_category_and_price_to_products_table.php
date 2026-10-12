<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('price_cents')->default(0);
        });

        // Pasar las categorías de texto que ya existían a la tabla nueva
        $names = DB::table('products')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category');

        foreach ($names as $name) {
            $id = DB::table('categories')->insertGetId([
                'name'       => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('products')->where('category', $name)->update(['category_id' => $id]);
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('category')->nullable();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
            $table->dropColumn('price_cents');
        });
    }
};