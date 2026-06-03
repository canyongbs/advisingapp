---
title: Phone Number Lookup Feature
created: 2026-05-22
---

## Feature Flags

- App\Features\PhoneNumberLookupFeature

## Temporary Migrations

- `app-modules/student-data-model/database/migrations/2026_05_21_172638_tmp_data_backfill_phone_number_lookups.php`
- `database/migrations/2026_06_03_164809_data_activate_phone_number_lookup_feature.php`

## Additional Cleanup

The PhoneNumberLookupFeature gates the transition from the legacy `can_receive_sms` column on `student_phone_numbers` / `prospect_phone_numbers` to a Telnyx-lookup-driven "is textable" derivation. When the feature is permanently on for every tenant, the legacy column and every code path that reads, writes, or surfaces it must be removed.

- In `app-modules/student-data-model/src/Models/Concerns/IsTextable.php`: in both `isTextable()` and `scopeTextable()`, remove the `if (PhoneNumberLookupFeature::active())` branches and keep **only** the body that reads/queries `phoneNumberLookup` against `PhoneNumberLookupStatus::textableStatuses()`. Delete the legacy `$this->getAttribute('can_receive_sms')` / `->where('can_receive_sms', true)` fallbacks. Drop the `use App\Features\PhoneNumberLookupFeature;` import.
- In `app-modules/student-data-model/src/Models/StudentPhoneNumber.php`: in `getHealthStatus()`, remove the feature-flag check and the `$this->can_receive_sms` fallback — keep only the `phoneNumberLookup()->whereIn('status', PhoneNumberLookupStatus::textableStatuses())` derivation. Drop the `use App\Features\PhoneNumberLookupFeature;` import.
- In `app-modules/prospect/src/Models/ProspectPhoneNumber.php`: same as above for the Prospect side.

- In `app-modules/student-data-model/src/Models/StudentPhoneNumber.php`: remove `'can_receive_sms'` from `$fillable`, and delete the entire `$casts` array (it currently has only the `can_receive_sms → boolean` entry; if another cast has been added by cleanup time, keep that one and delete only `can_receive_sms`).
- In `app-modules/prospect/src/Models/ProspectPhoneNumber.php`: same — drop `'can_receive_sms'` from `$fillable`, drop `$casts`.
- In `app-modules/student-data-model/src/Filament/Resources/Students/Pages/CreateStudent.php` and `EditStudent.php` (same directory): delete the `Checkbox::make('can_receive_sms')` block. Drop the `use App\Features\PhoneNumberLookupFeature;` import.
- In `app-modules/prospect/src/Filament/Resources/Prospects/Pages/CreateProspect.php` and `EditProspect.php` (same directory): same — delete the `Checkbox::make('can_receive_sms')` block. Drop the `use App\Features\PhoneNumberLookupFeature;` import.
- In `app-modules/student-data-model/src/Http/Controllers/Api/V1/Students/CreateStudentController.php`: delete the `...(! PhoneNumberLookupFeature::active() ? ['phone_numbers.*.can_receive_sms' => ['sometimes', 'boolean']] : [])` spread from the validation rules. Drop the `use App\Features\PhoneNumberLookupFeature;` import.
- In `app-modules/student-data-model/src/Http/Controllers/Api/V1/Students/StudentPhoneNumbers/CreateStudentPhoneNumberController.php` and `UpdateStudentPhoneNumberController.php` (same directory): delete the `...(! PhoneNumberLookupFeature::active() ? ['can_receive_sms' => ['sometimes', 'boolean']] : [])` spread from the validation rules. Drop the `use App\Features\PhoneNumberLookupFeature;` import.
- In `app-modules/student-data-model/src/DataTransferObjects/CreateStudentPhoneNumberData.php` and `UpdateStudentPhoneNumberData.php` (same directory): drop the `$canReceiveSms` constructor property.
- In `app-modules/student-data-model/src/Http/Resources/Api/V1/StudentPhoneNumberResource.php`: delete the `'can_receive_sms' => $this->resource->can_receive_sms` line.
- In `app-modules/student-data-model/src/Filament/Imports/StudentPhoneNumberImporter.php`: delete the `...(! PhoneNumberLookupFeature::active() ? [ ImportColumn::make('can_receive_sms')... ] : [])` spread. Drop the `use App\Features\PhoneNumberLookupFeature;` import.
- In `app-modules/prospect/src/Imports/ProspectImporter.php`: delete the three `...(! PhoneNumberLookupFeature::active() ? [ ImportColumn::make('phone_{N}_can_receive_sms')... ] : [])` spreads, and delete the corresponding `...(! PhoneNumberLookupFeature::active() ? ['can_receive_sms' => $this->data["phone_{$iteration}_can_receive_sms"] ?? false] : [])` spread from the `phoneNumbers()->create([...])` writer block. Drop the `use App\Features\PhoneNumberLookupFeature;` import.
- In `app-modules/engagement/src/Models/Engagement.php`: in `resolvePhoneHealthStatus()`, delete the `...(! PhoneNumberLookupFeature::active() ? ['can_receive_sms' => true] : [])` spread from the `phoneNumbers()->make([...])` call. Drop the `use App\Features\PhoneNumberLookupFeature;` import.
- In `app-modules/ai/src/Http/Controllers/QnaAdvisors/RegisterProspectController.php`, `app-modules/application/src/Http/Controllers/ApplicationWidgetController.php`, and `app-modules/form/src/Http/Controllers/FormWidgetController.php`: delete the `...(! PhoneNumberLookupFeature::active() ? ['can_receive_sms' => true] : [])` spread from the `$prospect->phoneNumbers()->create([...])` call in each. Drop the `use App\Features\PhoneNumberLookupFeature;` import.

- In `app-modules/report/src/Filament/Widgets/StudentDeliverableTable.php`: delete the legacy `IconColumn::make('can_receive_sms')` block entirely, and remove the `->visible(fn (): bool => PhoneNumberLookupFeature::active())` call from the `IconColumn::make('is_textable')` block. Drop the `use App\Features\PhoneNumberLookupFeature;` import.
- In `app-modules/report/src/Filament/Exports/EmailPhoneHealthExporter.php`: collapse the `...(PhoneNumberLookupFeature::active() ? [...] : [...])` spread to keep only the `is_textable` column branch (delete the `can_receive_sms` branch). Drop the `use App\Features\PhoneNumberLookupFeature;` import.

- In `app-modules/student-data-model/database/factories/StudentPhoneNumberFactory.php`: delete the `...(! PhoneNumberLookupFeature::active() ? ['can_receive_sms' => ...] : [])` spread in `definition()`. In `canReceiveSms()` and `canNotReceiveSms()`, remove the feature-flag check and the legacy `->state(['can_receive_sms' => ...])` fallback — keep only the `afterCreating(...)` block that creates the appropriate `PhoneNumberLookup`. Drop the `use App\Features\PhoneNumberLookupFeature;` import.
- In `app-modules/prospect/database/factories/ProspectPhoneNumberFactory.php`: same as above.

- Grep the test suite for `can_receive_sms` and `canReceiveSms` (excluding the factory state methods named after the feature). For each hit: if it's a test that explicitly sets `'can_receive_sms' => true/false` in a request body or create array, delete the key. If it's a validation-test case for `can_receive_sms`, delete the dataset entry.
  - Likely hits include: `app-modules/student-data-model/tests/Tenant/Http/Controllers/Api/V1/Students/CreateStudentControllerTest.php`, `app-modules/student-data-model/tests/Tenant/Http/Controllers/Api/V1/Students/StudentPhoneNumbers/CreateStudentPhoneNumberControllerTest.php`, `app-modules/student-data-model/tests/Tenant/Http/Controllers/Api/V1/Students/StudentPhoneNumbers/UpdateStudentPhoneNumberControllerTest.php`, `app-modules/student-data-model/tests/Tenant/Filament/Resources/Students/Pages/CreateStudentTest.php`, `app-modules/prospect/tests/Tenant/Prospect/CreateProspectTest.php`, `app-modules/notification/tests/Tenant/Notifications/Channels/SmsChannelSmsMessageTest.php`, and the three RequestFactories under `app-modules/student-data-model/tests/Tenant/Http/Controllers/Api/V1/Students/StudentPhoneNumbers/RequestFactories/` and `app-modules/prospect/tests/Tenant/Prospect/RequestFactories/`.

Create one drop-column migration per app-module, matching the pattern used by the original `create_*_phone_numbers_table` migrations (each table is owned by the module that introduced it):

- `app-modules/student-data-model/database/migrations/<timestamp>_drop_can_receive_sms_from_student_phone_numbers_table.php`
  ```php
  Schema::table('student_phone_numbers', fn (Blueprint $table) => $table->dropColumn('can_receive_sms'));
  ```
- `app-modules/prospect/database/migrations/<timestamp>_drop_can_receive_sms_from_prospect_phone_numbers_table.php`
  ```php
  Schema::table('prospect_phone_numbers', fn (Blueprint $table) => $table->dropColumn('can_receive_sms'));
  ```
Both should provide a `down()` that re-adds the column with `boolean()->default(false)` for rollback safety.

- After working through everything above, do a global search across the entire codebase for `can_receive_sms` AND `canReceiveSms`. Every remaining reference must be deleted — including comments, docblocks, dev fixtures, seeders, helper variables, debug logs, anything. Anything that survives the explicit per-file list is a straggler that needs to go. Re-grep until both queries return zero hits in source code (a few historical references may remain in pre-cleanup migrations like `2025_02_04_*_create_*_phone_numbers_table.php` and the new drop-column migrations created above — those are fine to leave).

- Delete `app/Features/PhoneNumberLookupFeature.php`.
- Remove every remaining `use App\Features\PhoneNumberLookupFeature;` import that becomes unused after the above edits.
