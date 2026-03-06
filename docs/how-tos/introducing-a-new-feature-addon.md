# Introducing a New Feature Addon

This guide covers gating features behind addon toggles tied to the account subscription that can be externally configured.

---

## Creating the New Feature

1. Adding New Case to Enum

    `app/Enums/Feature.php`: A new case will need to be added to the enum:

    ```php
    case ExampleFeature = 'example-feature';
    ```

2. Add New Feature to License Addons Data DTO

    `app/DataTransferObjects/LicenseManagement/LicenseAddonsData.php`: The constructor will need to have the new key added:

    ```php
    public function __construct(
            // ...
            public bool $exampleFeature = false,
        ) {}
    ```

3. Modify Tenant Requests

    Because this feature is toggled externally via API, the rules for requests relating to tenants will also need to be updated to listen for this feature.

    `app/Http/Requests/Tenants/CreateTenantRequest.php` and `app/Http/Requests/Tenants/SyncTenantRequest.php` will both need an additional rule in their `rules()` functions:

    ```php
        public function rules(): array
        {
            return [
                // ...
                'addons.exampleFeature' => ['required', 'boolean'],
            ];
        }
    ```

4. Add Feature Toggle to License Settings

    `app/Filament/Pages/ManageLicenseSettings.php`: A new Toggle will need to be added to the `Enabled Features` Section:

    ```php
    Section::make('Enabled Features')
        ->columns()
        ->schema(
            [
                // ...
                Toggle::make('data.addons.exampleFeature')
                    ->label('Example Feature'),
            ]
        ),
    ```

## Applying Restrictions

5. Restricting The Frontend

    ### Filament Resources

    A feature gated model's policy will often be sufficient at correctly hiding and displaying its related resource. To do so, use the `PerformsFeatureChecks` trait in the policy and reference the `Features` enum.

    ```php
    use App\Concerns\PerformsFeatureChecks;
    use App\Enums\Feature;
    use App\Models\Authenticatable;

    class ExamplePolicy
    {
        use PerformsFeatureChecks;

        public function before(Authenticatable $authenticatable): ?Response
        {
            if (! is_null($response = $this->hasFeatures())) {
                return $response;
            }

            return null;
        }

        // ...

        /**
         * @return array<Feature>
         */
        protected function requiredFeatures(): array
        {
            return [Feature::ExampleFeature];
        }
    }
    ```

    ### Other Filament Pages

    Some pages, such as a relationship manager or standalone Pages, may need a more explicit check in their `canAccess` method. Make sure to check all pages relating to the feature in question and verify that they are properly restricted. And check with leadership if you are unsure which areas need to be gated.

    ```php
    use App\Enums\Feature;
    use Illuminate\Support\Facades\Gate;

    public static function canAccess(): bool
        {
            if (! Gate::check(Feature::ExampleFeature->getGateName())) {
                return false;
            }

            //...
        }
    ```

6. Tests

    Additionally, access control tests should be modified or created for the affected pages and/or resources (one page in a resource, e.g. the list page, is acceptable; but make sure to separately test relationship managers in other resources).

    ```php
    use App\Models\User;
    use App\Settings\LicenseSettings;

    use function Pest\Laravel\actingAs;
    use function Pest\Laravel\get;

    it('is gated with proper access control', function () {
        $settings = app(LicenseSettings::class);

        $settings->data->addons->exampleFeature = false;
        $settings->save();

        $user = User::factory()->create();

        $user->givePermissionTo('example.view-any');

        actingAs($user);

        get(ListExamples::getUrl())->assertForbidden();

        $settings->data->addons->exampleFeature = true;
        $settings->save();

        $user->revokePermissionTo('example.view-any');

        get(ListExamples::getUrl())->assertForbidden();

        $user->givePermissionTo('example.view-any');

        get(ListExamples::getUrl())->assertSuccessful();
    });
    ```

    `tests/TestCase.php`: Both the `createTenant` function and the `refreshTenantTestingEnvironment` will need to be updated:

    ```php
    new LicenseAddonsData(
            // ...
            exampleFeature: true,
        )
    ```
