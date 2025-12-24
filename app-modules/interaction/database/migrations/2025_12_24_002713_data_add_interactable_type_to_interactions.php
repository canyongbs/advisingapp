<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            //Drop existing indexes, add interactable_type column
            Schema::table('interaction_statuses', function (Blueprint $table) {
                $table->dropUniqueIfExists(['name']);
                $table->string('interactable_type')->nullable();
            });

            Schema::table('interaction_types', function (Blueprint $table) {
                $table->dropUniqueIfExists(['name']);
                $table->string('interactable_type')->nullable();
            });

            Schema::table('interaction_outcomes', function (Blueprint $table) {
                $table->dropUniqueIfExists(['name']);
                $table->string('interactable_type')->nullable();
            });

            Schema::table('interaction_relations', function (Blueprint $table) {
                $table->dropUniqueIfExists(['name']);
                $table->string('interactable_type')->nullable();
            });

            Schema::table('interaction_drivers', function (Blueprint $table) {
                $table->dropUniqueIfExists(['name']);
                $table->string('interactable_type')->nullable();
            });

            Schema::table('interaction_initiatives', function (Blueprint $table) {
                $table->dropUniqueIfExists(['name']);
                $table->string('interactable_type')->nullable();
            });
            
            //Remove during cleanup
            //Duplicate existing records and set as prospect type, set originals as student type
            DB::table('interaction_statuses')
                ->chunkById(100, function (Collection $interactionStatuses) {
                    foreach ($interactionStatuses as $interactionStatus) {
                        DB::table('interaction_statuses')->insert([
                            'name' => $interactionStatus->name,
                            'color' => $interactionStatus->color,
                            'is_default' => $interactionStatus->is_default,
                            'interactable_type' => 'prospect',
                        ]);

                        DB::table('interaction_statuses')
                            ->where('id', $interactionStatus->id)
                            ->update(['interactable_type' => 'student']);
                    }
                });
            
            DB::table('interaction_types')
                ->chunkById(100, function (Collection $interactionTypes) {
                    foreach ($interactionTypes as $interactionType) {
                        DB::table('interaction_types')->insert([
                            'name' => $interactionType->name,
                            'is_default' => $interactionType->is_default,
                            'interactable_type' => 'prospect',
                        ]);

                        DB::table('interaction_types')
                            ->where('id', $interactionType->id)
                            ->update(['interactable_type' => 'student']);
                    }
                });
            
            DB::table('interaction_outcomes')
                ->chunkById(100, function (Collection $interactionOutcomes) {
                    foreach ($interactionOutcomes as $interactionOutcome) {
                        DB::table('interaction_outcomes')->insert([
                            'name' => $interactionOutcome->name,
                            'is_default' => $interactionOutcome->is_default,
                            'interactable_type' => 'prospect',
                        ]);

                        DB::table('interaction_outcomes')
                            ->where('id', $interactionOutcome->id)
                            ->update(['interactable_type' => 'student']);
                    }
                });
            
            DB::table('interaction_relations')
                ->chunkById(100, function (Collection $interactionRelations) {
                    foreach ($interactionRelations as $interactionRelation) {
                        DB::table('interaction_relations')->insert([
                            'name' => $interactionRelation->name,
                            'is_default' => $interactionRelation->is_default,
                            'interactable_type' => 'prospect',
                        ]);

                        DB::table('interaction_relations')
                            ->where('id', $interactionRelation->id)
                            ->update(['interactable_type' => 'student']);
                    }
                });
            
            DB::table('interaction_drivers')
                ->chunkById(100, function (Collection $interactionDrivers) {
                    foreach ($interactionDrivers as $interactionDriver) {
                        DB::table('interaction_drivers')->insert([
                            'name' => $interactionDriver->name,
                            'is_default' => $interactionDriver->is_default,
                            'interactable_type' => 'prospect',
                        ]);

                        DB::table('interaction_drivers')
                            ->where('id', $interactionDriver->id)
                            ->update(['interactable_type' => 'student']);
                    }
                });
            
            DB::table('interaction_initiatives')
                ->chunkById(100, function (Collection $interactionInitiatives) {
                    foreach ($interactionInitiatives as $interactionInitiative) {
                        DB::table('interaction_initiatives')->insert([
                            'name' => $interactionInitiative->name,
                            'is_default' => $interactionInitiative->is_default,
                            'interactable_type' => 'prospect',
                        ]);

                        DB::table('interaction_initiatives')
                            ->where('id', $interactionInitiative->id)
                            ->update(['interactable_type' => 'student']);
                    }
                });
            
            //Remove during cleanup
            //Ensure interactions now have the correct foriegn key
            DB::table('interactions')
                ->chunkById(100, function (Collection $interactions) {
                    foreach ($interactions as $interaction) {
                        $interactionStatus = DB::table('interaction_statuses')->where('id', $interaction->interaction_status_id)->first();
                        if($interaction->interactable_type !== $interactionStatus->interactable_type) {
                            $newStatusId = DB::table('interaction_statuses')
                                ->where('name', $interactionStatus->name)
                                ->where('interactable_type', $interaction->interactable_type)
                                ->value('id');
                            DB::table('interactions')
                                ->where('id', $interaction->id)
                                ->update(['interaction_status_id' => $newStatusId]);
                        }
                        
                        $interactionType = DB::table('interaction_types')->where('id', $interaction->interaction_type_id)->first();
                        if($interaction->interactable_type !== $interactionType->interactable_type) {
                            $newTypeId = DB::table('interaction_types')
                                ->where('name', $interactionType->name)
                                ->where('interactable_type', $interaction->interactable_type)
                                ->value('id');
                            DB::table('interactions')
                                ->where('id', $interaction->id)
                                ->update(['interaction_type_id' => $newTypeId]);
                        }
                        
                        $interactionOutcome = DB::table('interaction_outcomes')->where('id', $interaction->interaction_outcome_id)->first();
                        if($interaction->interactable_type !== $interactionOutcome->interactable_type) {
                            $newOutcomeId = DB::table('interaction_outcomes')
                                ->where('name', $interactionOutcome->name)
                                ->where('interactable_type', $interaction->interactable_type)
                                ->value('id');
                            DB::table('interactions')
                                ->where('id', $interaction->id)
                                ->update(['interaction_outcome_id' => $newOutcomeId]);
                        }
                        
                        $interactionRelation = DB::table('interaction_relations')->where('id', $interaction->interaction_relation_id)->first();
                        if($interaction->interactable_type !== $interactionRelation->interactable_type) {
                            $newRelationId = DB::table('interaction_relations')
                                ->where('name', $interactionRelation->name)
                                ->where('interactable_type', $interaction->interactable_type)
                                ->value('id');
                            DB::table('interactions')
                                ->where('id', $interaction->id)
                                ->update(['interaction_relation_id' => $newRelationId]);
                        }
                        
                        $interactionDriver = DB::table('interaction_drivers')->where('id', $interaction->interaction_driver_id)->first();
                        if($interaction->interactable_type !== $interactionDriver->interactable_type) {
                            $newDriverId = DB::table('interaction_drivers')
                                ->where('name', $interactionDriver->name)
                                ->where('interactable_type', $interaction->interactable_type)
                                ->value('id');
                            DB::table('interactions')
                                ->where('id', $interaction->id)
                                ->update(['interaction_driver_id' => $newDriverId]);
                        }
                        
                        $interactionInitiative = DB::table('interaction_initiatives')->where('id', $interaction->interaction_initiative_id)->first();
                        if($interaction->interactable_type !== $interactionInitiative->interactable_type) {
                            $newInitiativeId = DB::table('interaction_initiatives')
                                ->where('name', $interactionInitiative->name)
                                ->where('interactable_type', $interaction->interactable_type)
                                ->value('id');
                            DB::table('interactions')
                                ->where('id', $interaction->id)
                                ->update(['interaction_initiative_id' => $newInitiativeId]);
                        }
                    }
                });
            
            //Make interactable_type non nullable, add unique index
            Schema::table('interaction_statuses', function (Blueprint $table) {
                $table->string('interactable_type')->nullable(false)->change();
                $table->unique(['name', 'interactable_type']);
            });

            Schema::table('interaction_types', function (Blueprint $table) {
                $table->string('interactable_type')->nullable(false)->change();
                $table->unique(['name', 'interactable_type']);
            });

            Schema::table('interaction_outcomes', function (Blueprint $table) {
                $table->string('interactable_type')->nullable(false)->change();
                $table->unique(['name', 'interactable_type']);
            });

            Schema::table('interaction_relations', function (Blueprint $table) {
                $table->string('interactable_type')->nullable(false)->change();
                $table->unique(['name', 'interactable_type']);
            });

            Schema::table('interaction_drivers', function (Blueprint $table) {
                $table->string('interactable_type')->nullable(false)->change();
                $table->unique(['name', 'interactable_type']);
            });

            Schema::table('interaction_initiatives', function (Blueprint $table) {
                $table->string('interactable_type')->nullable(false)->change();
                $table->unique(['name', 'interactable_type']);
            });
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            //Drop any existing index
            Schema::table('interaction_statuses', function (Blueprint $table) {
                $table->dropUniqueIfExists(['name', 'interactable_type']);
            });

            Schema::table('interaction_types', function (Blueprint $table) {
                $table->dropUniqueIfExists(['name', 'interactable_type']);
            });

            Schema::table('interaction_outcomes', function (Blueprint $table) {
                $table->dropUniqueIfExists(['name', 'interactable_type']);
            });

            Schema::table('interaction_relations', function (Blueprint $table) {
                $table->dropUniqueIfExists(['name', 'interactable_type']);
            });

            Schema::table('interaction_drivers', function (Blueprint $table) {
                $table->dropUniqueIfExists(['name', 'interactable_type']);
            });

            Schema::table('interaction_initiatives', function (Blueprint $table) {
                $table->dropUniqueIfExists(['name', 'interactable_type']);
            });

            //Ensure that all records in the interaction table reference the student variant
            DB::table('interactions')
                ->chunkById(100, function (Collection $interactions) {
                    foreach ($interactions as $interaction) {
                        $interactionStatus = DB::table('interaction_statuses')->where('id', $interaction->interaction_status_id)->first();
                        if($interactionStatus->interactable_type !== 'student') {
                            $newStatusId = DB::table('interaction_statuses')
                                ->where('name', $interactionStatus->name)
                                ->where('interactable_type', 'student')
                                ->value('id');
                            DB::table('interactions')
                                ->where('id', $interaction->id)
                                ->update(['interaction_status_id' => $newStatusId]);
                        }
                        
                        $interactionType = DB::table('interaction_types')->where('id', $interaction->interaction_type_id)->first();
                        if($interactionType->interactable_type !== 'student') {
                            $newTypeId = DB::table('interaction_types')
                                ->where('name', $interactionType->name)
                                ->where('interactable_type', 'student')
                                ->value('id');
                            DB::table('interactions')
                                ->where('id', $interaction->id)
                                ->update(['interaction_type_id' => $newTypeId]);
                        }
                        
                        $interactionOutcome = DB::table('interaction_outcomes')->where('id', $interaction->interaction_outcome_id)->first();
                        if($interactionOutcome->interactable_type !== 'student') {
                            $newOutcomeId = DB::table('interaction_outcomes')
                                ->where('name', $interactionOutcome->name)
                                ->where('interactable_type', 'student')
                                ->value('id');
                            DB::table('interactions')
                                ->where('id', $interaction->id)
                                ->update(['interaction_outcome_id' => $newOutcomeId]);
                        }
                        
                        $interactionRelation = DB::table('interaction_relations')->where('id', $interaction->interaction_relation_id)->first();
                        if($interactionRelation->interactable_type !== 'student') {
                            $newRelationId = DB::table('interaction_relations')
                                ->where('name', $interactionRelation->name)
                                ->where('interactable_type', 'student')
                                ->value('id');
                            DB::table('interactions')
                                ->where('id', $interaction->id)
                                ->update(['interaction_relation_id' => $newRelationId]);
                        }
                        
                        $interactionDriver = DB::table('interaction_drivers')->where('id', $interaction->interaction_driver_id)->first();
                        if($interactionDriver->interactable_type !== 'student') {
                            $newDriverId = DB::table('interaction_drivers')
                                ->where('name', $interactionDriver->name)
                                ->where('interactable_type', 'student')
                                ->value('id');
                            DB::table('interactions')
                                ->where('id', $interaction->id)
                                ->update(['interaction_driver_id' => $newDriverId]);
                        }
                        
                        $interactionInitiative = DB::table('interaction_initiatives')->where('id', $interaction->interaction_initiative_id)->first();
                        if($interactionInitiative->interactable_type !== 'student') {
                            $newInitiativeId = DB::table('interaction_initiatives')
                                ->where('name', $interactionInitiative->name)
                                ->where('interactable_type', 'student')
                                ->value('id');
                            DB::table('interactions')
                                ->where('id', $interaction->id)
                                ->update(['interaction_initiative_id' => $newInitiativeId]);
                        }
                    }
                });

            //Remove all records from meta tables with prospect types
            DB::table('interaction_statuses')
                ->chunkById(100, function (Collection $interactionStatuses) {
                    foreach ($interactionStatuses as $interactionStatus) {
                        if($interactionStatus->interactable_type == 'prospect') {
                            DB::table('interaction_statuses')->delete($interactionStatus->id);
                        }
                    }
                });
            
            DB::table('interaction_types')
                ->chunkById(100, function (Collection $interactionTypes) {
                    foreach ($interactionTypes as $interactionType) {
                        if($interactionType->interactable_type == 'prospect') {
                            DB::table('interaction_types')->delete($interactionType->id);
                        }
                    }
                });
            
            DB::table('interaction_outcomes')
                ->chunkById(100, function (Collection $interactionOutcomes) {
                    foreach ($interactionOutcomes as $interactionOutcome) {
                        if($interactionOutcome->interactable_type == 'prospect') {
                            DB::table('interaction_outcomes')->delete($interactionOutcome->id);
                        }
                    }
                });
            
            DB::table('interaction_relations')
                ->chunkById(100, function (Collection $interactionRelations) {
                    foreach ($interactionRelations as $interactionRelation) {
                        if($interactionRelation->interactable_type == 'prospect') {
                            DB::table('interaction_relations')->delete($interactionRelation->id);
                        }
                    }
                });
            
            DB::table('interaction_drivers')
                ->chunkById(100, function (Collection $interactionDrivers) {
                    foreach ($interactionDrivers as $interactionDriver) {
                        if($interactionDriver->interactable_type == 'prospect') {
                            DB::table('interaction_drivers')->delete($interactionDriver->id);
                        }
                    }
                });
            
            DB::table('interaction_initiatives')
                ->chunkById(100, function (Collection $interactionInitiatives) {
                    foreach ($interactionInitiatives as $interactionInitiative) {
                        if($interactionInitiative->interactable_type == 'prospect') {
                            DB::table('interaction_initiatives')->delete($interactionInitiative->id);
                        }
                    }
                });

            //Remove interactable_type column, add indexes on name columns
            Schema::table('interaction_statuses', function (Blueprint $table) {
                $table->dropColumn('interactable_type');
                $table->unique('name');
            });

            Schema::table('interaction_types', function (Blueprint $table) {
                $table->dropColumn('interactable_type');
                $table->unique('name');
            });

            Schema::table('interaction_outcomes', function (Blueprint $table) {
                $table->dropColumn('interactable_type');
                $table->unique('name');
            });

            Schema::table('interaction_relations', function (Blueprint $table) {
                $table->dropColumn('interactable_type');
                $table->unique('name');
            });

            Schema::table('interaction_drivers', function (Blueprint $table) {
                $table->dropColumn('interactable_type');
                $table->unique('name');
            });

            Schema::table('interaction_initiatives', function (Blueprint $table) {
                $table->dropColumn('interactable_type');
                $table->unique('name');
            });
        });
    }
};
