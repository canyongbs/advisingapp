<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use AdvisingApp\DataMigration\OneTimeOperationManager;
use TimoKoerber\LaravelOneTimeOperations\Models\Operation;

class CreateOneTimeOperationsTable extends Migration
{
    protected string $name;

    public function __construct()
    {
        $this->name = OneTimeOperationManager::getTableName();
    }

    public function up()
    {
        Schema::create($this->name, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->enum('dispatched', [Operation::DISPATCHED_SYNC, Operation::DISPATCHED_ASYNC]);
            $table->timestamp('processed_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->name);
    }
}
