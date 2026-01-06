# Month Aggregation Testing

## Introduction

Many features in our application aggregate data by month over a rolling period (e.g., 12 months) or a user-defined date range. These features are prone to intermittent test failures when the current date or filter boundaries fall on days that don't exist in all months (29th, 30th, 31st).

This document outlines the standardized approach for testing month aggregation logic to prevent date overflow issues.

## The Problem

Carbon's date manipulation can cause unexpected behavior when subtracting months from dates like the 31st. For example:

- `Carbon::parse('2024-08-31')->subMonth()` returns July 31st ✓
- `Carbon::parse('2024-03-31')->subMonth()` returns March 2nd or 3rd (February overflow) ✗

This causes tests to fail intermittently depending on when they run. A test that passes on the 15th may fail on the 31st.

**Note:** Carbon provides `subMonthNoOverflow()` and other "NoOverflow" methods which clamps to the last day of the target month instead of overflowing. This can be useful in production code or niche scenarios where you need to stay within the correct month boundary.

## Required Test Cases

### 1. Standard Date (No Filters)

Tests the default behavior when the current date is a "safe" mid-month date (e.g., 15th).

**Purpose:** Baseline test that verifies core functionality without date edge cases.

### 2. Overflow-Risk Dates (No Filters)

Tests when the current date falls on the 29th, 30th, or 31st of a month.

**Purpose:** Ensures month boundary calculations work correctly when looking back from a date that doesn't exist in all months.

**Implementation:** Use a test with a dataset containing multiple date scenarios (29th, 30th, 31st).

### 3. February Edge Cases (No Filters)

Two specific tests:

- **Feb 28th in a non-leap year** - Tests the last day of February when it only has 28 days
- **Feb 29th in a leap year** - Tests a date that only exists every 4 years

**Purpose:** February is the shortest month and requires special handling when calculating month-based lookbacks.

---

**Note:** Test cases #4 and #5 below are only required if the feature being tested supports user-defined date filters. If the feature only uses a fixed rolling period (e.g., always the last 12 months), you can skip these.

---

### 4. Standard Filter Boundaries

Tests user-defined date ranges where both start and end dates are "safe" (1st-28th).

**Purpose:** Verifies filtering logic works correctly without date overflow concerns.

### 5. Non-Standard Filter Boundaries

Tests user-defined date ranges where start and/or end dates are on the 29th, 30th, or 31st.

**Purpose:** Ensures boundary calculations handle overflow-risk dates in user-provided filters.

## Implementation Guidelines

### Always Use Static Dates

Never use dynamic dates like `now()->subMonths(3)` in tests. Always use `Carbon::parse('YYYY-MM-DD')` with explicit dates.

```php
// ❌ Bad - will fail on certain days
$startDate = now()->subMonths(3);

// ✅ Good - deterministic
$startDate = Carbon::parse('2024-09-15');
```

### Always Use travelTo()

Pin the "current" date at the start of each test to ensure consistent behavior regardless of when the test actually runs.

```php
travelTo(Carbon::parse('2024-12-15'));
```

### Include Boundary Data

Create test records on dates that stress the boundaries:

- Records just outside the expected range (should be excluded)
- Records on Feb 29th (leap year handling)
- Records on 29th, 30th, 31st of various months
- Gaps in data to verify empty periods are handled correctly
- Multiple records in the same period to verify aggregation

### Use Snapshots Carefully

Use `->toMatchSnapshot()` for data assertions. This captures the full data structure and makes it easy to verify correct behavior.

**⚠️ Important:** When a snapshot is first generated or updated, you must carefully validate that the output is correct. A snapshot only asserts that output matches a previous run—it does not inherently verify correctness. Ensure that:

1. The snapshot data reflects the expected aggregation based on your test input
2. Records outside the date range are properly excluded
3. Records within the range are properly included and aggregated
4. Empty periods are represented correctly
5. The test setup combined with the snapshot actually asserts meaningful behavior

A passing snapshot test is only as good as the initial validation of that snapshot.

## Test Template

```php
it('returns correct data when today is a safe mid-month date', function () {
    travelTo(Carbon::parse('2024-12-15'));

    // Create test data with various edge case dates
    Model::factory()
        ->has(
            RelatedModel::factory()->count(X)->sequence(
                // Record outside range - should NOT be counted
                ['created_at' => Carbon::parse('2023-12-31')],
                // Records within range including edge cases
                ['created_at' => Carbon::parse('2024-02-29')], // Leap year
                ['created_at' => Carbon::parse('2024-03-31')], // 31st
                // Gap in data (no records for some months)
                ['created_at' => Carbon::parse('2024-12-01')],
            ),
            'relationship'
        )
        ->create();

    $result = // ... execute the logic being tested

    expect($result)->toMatchSnapshot();
});

it('returns correct data when today falls on an overflow-risk date', function (Carbon $testDate) {
    travelTo($testDate);

    // Create test data...

    $result = // ... execute the logic being tested

    expect($result)->toMatchSnapshot();
})
    ->with([
        '31st of month' => [Carbon::parse('2024-08-31')],
        '30th of month' => [Carbon::parse('2024-08-30')],
        '29th of month' => [Carbon::parse('2024-08-29')],
    ]); // Pest dataset
```

## Reference Implementation

See [StudentInteractionLineChartTest.php](../app-modules/report/tests/Tenant/Filament/Widgets/StudentInteractionLineChartTest.php) for a complete example of this testing pattern applied to a chart widget.
