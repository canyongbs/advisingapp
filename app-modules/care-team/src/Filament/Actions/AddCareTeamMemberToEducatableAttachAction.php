<?php

namespace AdvisingApp\CareTeam\Filament\Actions;

use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Enums\CareTeamRoleType;
use App\Models\Scopes\HasLicense;
use App\Models\Scopes\WithoutAnyAdmin;
use App\Models\User;
use Filament\Actions\AttachAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class AddCareTeamMemberToEducatableAttachAction extends AttachAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('New')
                    ->modalHeading(fn () => "Add Users to {$this->getLivewire()->getOwnerRecord()->display_name}'s Care Team")
                    ->modalSubmitActionLabel('Add')
                    ->modalWidth(Width::FourExtraLarge)
                    ->attachAnother(false)
                    ->color('primary')
                    ->steps(fn() => [
                        Step::make('Add Care Team Members')
                                ->schema([
                                    Repeater::make('careTeams')
                                        ->label(fn () => "Please identify which team members you would like to add to the care team for {$this->getLivewire()->getOwnerRecord()->getMorphClass()} {$this->getLivewire()->getOwnerRecord()->display_name}")
                                        ->schema(fn () => [
                                            Select::make('user_id')
                                                ->label('User')
                                                ->searchable()
                                                ->required()
                                                ->options(match (true) {
                                                        $this->getLivewire()->getOwnerRecord() instanceof Student => User::query()->tap(new HasLicense(Student::getLicenseType()))
                                                            ->whereDoesntHave(
                                                                'studentCareTeams',
                                                                fn ($query) => $query
                                                                    ->where('educatable_type', $this->getLivewire()->getOwnerRecord()->getMorphClass())
                                                                    ->where('educatable_id', $this->getLivewire()->getOwnerRecord()->getKey())
                                                            )
                                                            ->tap(new WithoutAnyAdmin())
                                                            ->pluck('name', 'id'),
                                                        $this->getLivewire()->getOwnerRecord() instanceof Prospect => User::query()->tap(new HasLicense(Prospect::getLicenseType()))
                                                            ->whereDoesntHave(
                                                                'prospectCareTeams',
                                                                fn ($query) => $query
                                                                    ->where('educatable_type', $this->getLivewire()->getOwnerRecord()->getMorphClass())
                                                                    ->where('educatable_id', $this->getLivewire()->getOwnerRecord()->getKey())
                                                            )
                                                            ->tap(new WithoutAnyAdmin())
                                                            ->pluck('name', 'id'),
                                                        default => null,
                                                    }),
                                            Select::make('care_team_role_id')
                                                ->label('Role')
                                                ->relationship('careTeamRole', 'name', fn (Builder $query) =>
                                                    $query
                                                        ->where(
                                                            'type',
                                                            match (true) {
                                                                $this->getLivewire()->getOwnerRecord() instanceof Student => CareTeamRoleType::Student,
                                                                $this->getLivewire()->getOwnerRecord() instanceof Prospect => CareTeamRoleType::Prospect,
                                                                default => null,
                                                            }
                                                        )
                                                        ->orderByDesc('created_at')
                                                )
                                                ->default(fn () => match (true) {
                                                                $this->getLivewire()->getOwnerRecord() instanceof Student => CareTeamRoleType::studentDefault()?->id,
                                                                $this->getLivewire()->getOwnerRecord() instanceof Prospect => CareTeamRoleType::prospectDefault()?->id,
                                                                default => null,
                                                            })
                                                ->preload()
                                                ->optionsLimit(20)
                                                ->searchable()
                                                ->model(CareTeam::class)
                                                ->visible(CareTeamRole::where(
                                                        'type',
                                                        match (true) {
                                                            $this->getLivewire()->getOwnerRecord() instanceof Student => CareTeamRoleType::Student,
                                                            $this->getLivewire()->getOwnerRecord() instanceof Prospect => CareTeamRoleType::Prospect,
                                                            default => null,
                                                        }
                                                    )
                                                    ->count() > 0),
                                        ])
                                        ->addActionLabel('Add User')
                                        ->reorderable(false),
                                ]),
                            Step::make('Confirm New Care Team Members')
                                ->schema([
                                    ViewField::make('summary')
                                        ->view(
                                            'filament.forms.components.care-teams.summary',
                                            fn(Get $get) => ['careTeams' => $get('careTeams'), 'educatable' => $this->getLivewire()->getOwnerRecord()]
                                            ),
                                ])
                        ])
                    ->action(function (array $data) {
                        foreach ($data['careTeams'] as $careTeam) {
                            $user = User::find($careTeam['user_id']);

                            $educatable = $this->getLivewire()->getOwnerRecord();

                            assert($educatable instanceof Student || $educatable instanceof Prospect);

                            if ($educatable->careTeam()->where('user_id', $user->getKey())->doesntExist()) {
                                $educatable->careTeam()->attach($user, ['care_team_role_id' => $careTeam['care_team_role_id']]);
                            }
                        }
                    })
                    ->modalSubmitActionLabel('Confirm')
                    ->successNotificationTitle(fn (array $data) =>
                         count($data['careTeams']) . ' ' . Str::plural('User', count($data['careTeams'])) . " added to {$this->getLivewire()->getOwnerRecord()->display_name}'s Care Team"
                    );
    }

    public static function getDefaultName(): ?string
    {
        return 'attach';
    }

}