<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The table name for this migration
     *
     * @var string $schemaTable
     */
    protected string $schemaTable = 'configurations';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create($this->schemaTable, static function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('name')->unique();
            $table->string('value');
            $table->string('default');
            $table->string('value_type');
            $table->json('data')->nullable();
            $table->nullableMorphs('edited_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists($this->schemaTable);
    }
};
