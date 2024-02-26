<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use AdvisingApp\DataMigration\Models\Operation;
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
            $table->bigIncrements('id');
            $table->string('name');
            $table->enum('dispatched', [Operation::DISPATCHED_SYNC, Operation::DISPATCHED_ASYNC]);
            $table->timestamp('processed_at')->nullable();
        });
    }
}
