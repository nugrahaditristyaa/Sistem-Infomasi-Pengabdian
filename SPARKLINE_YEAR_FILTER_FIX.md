# Sparkline Year Filter Fix

## Problem
The sparkline line charts on the dashboard were displaying total data across all years (from the first year until now), ignoring the Year filter selection. This meant that when users selected a specific year (e.g., 2024), the sparkline would still show historical data from all years instead of data specific to the selected year.

## Solution
Modified the sparkline data API and frontend to respect the year filter:

### Backend Changes (DekanController.php)

**File**: `app/Http/Controllers/Dekan/DekanController.php`

**Method**: `getSparklineData()`

**Changes**:
1. Added year filter parameter handling from the request
2. Implemented two different data modes:
   - **Specific Year**: Shows monthly data (12 months) for the selected year
   - **All Years**: Shows yearly data across all available years
3. Updated the response to include a `period` field indicating whether data is monthly or yearly

**Logic**:
- When `year=2024` (or any specific year): Returns 12 data points representing each month of that year
- When `year=all`: Returns data points for each year in the database (historical trend)

### Frontend Changes

**Files Modified**:
- `resources/views/admin/dashboard.blade.php`
- `resources/views/dekan/dashboard.blade.php`

**Changes**:
1. Updated the `loadSparklineCharts()` function to pass the current year filter as a URL parameter
2. Changed from: `fetch('{{ route('dekan.api.sparkline-data') }}')`
3. Changed to: `fetch('{{ route('dekan.api.sparkline-data') }}?year=' + currentYear)`

## How It Works

### When a specific year is selected (e.g., 2024):
1. Frontend sends: `GET /api/sparkline-data?year=2024`
2. Backend returns monthly data for 2024:
   ```json
   {
     "pengabdian": [5, 8, 3, 12, 7, 9, 4, 6, 10, 8, 5, 7],
     "dosen": [3, 5, 2, 8, 5, 6, 3, 4, 7, 5, 3, 5],
     "mahasiswa": [2, 4, 1, 6, 3, 4, 2, 3, 5, 4, 2, 3],
     "years": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
     "period": "monthly"
   }
   ```
3. Sparkline displays 12 data points (one per month)

### When "All Years" is selected:
1. Frontend sends: `GET /api/sparkline-data?year=all`
2. Backend returns yearly data:
   ```json
   {
     "pengabdian": [45, 67, 89, 102, 95],
     "dosen": [25, 34, 42, 48, 45],
     "mahasiswa": [15, 22, 28, 32, 30],
     "years": [2020, 2021, 2022, 2023, 2024],
     "period": "yearly"
   }
   ```
3. Sparkline displays data points for each year

## Benefits

1. **Consistency**: Sparklines now match the selected year filter, just like all other dashboard statistics
2. **Better Insights**: Users can see monthly trends when viewing a specific year
3. **Historical View**: Users can still see yearly trends when "All Years" is selected
4. **Improved UX**: The dashboard is now more intuitive and consistent across all visualizations

## Testing

To test the fix:
1. Navigate to the Admin or Dekan dashboard
2. Select a specific year (e.g., 2024) from the year filter dropdown
3. Observe that the sparkline charts now show monthly data for that year only
4. Select "All Years" from the dropdown
5. Observe that the sparkline charts now show yearly historical data

## Files Changed

1. `app/Http/Controllers/Dekan/DekanController.php` - Backend API logic
2. `resources/views/admin/dashboard.blade.php` - Admin dashboard frontend
3. `resources/views/dekan/dashboard.blade.php` - Dekan dashboard frontend
