<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use AdvisingApp\DataMigration\OneTimeOperationManager;

class CreateOneTimeOperationsTable extends Migration
{
    protected string $name;

    public function __construct()
    {
        $this->name = OneTimeOperationManager::getTableName();
    }

    public function up(): void
    {
        Schema::create($this->name, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('dispatched');
            $table->timestamp('processed_at')->nullable();
        });
    }
}
