# Word Cloud Implementation for Kaprodi SI Dashboard

## Overview

Added a word cloud visualization feature to the Kaprodi SI dashboard that displays frequently used words from pengabdian (community service) titles. The word cloud updates based on the selected year filter.

## Implementation Details

### 1. Controller Changes

**File:** `app/Http/Controllers/Kaprodi/KaprodiController.php`

#### Added Data Collection for Word Cloud

-   Modified the `dashboard()` method to collect judul (titles) of pengabdian for Sistem Informasi prodi
-   Data is filtered by:
    -   Prodi: Only "Sistem Informasi"
    -   Year: Based on selected year filter (or all years if "all" is selected)
-   Added `$judulPengabdianSI` array to store the titles
-   Added `$prodiFilter` variable to the view compact to enable conditional display

```php
// Collect titles for word cloud (only for Kaprodi SI)
$judulPengabdianSI = [];
if ($prodiFilter === 'Sistem Informasi') {
    $judulQuery = Pengabdian::whereExists(function ($query) use ($prodiFilter) {
        $query->select(DB::raw(1))
            ->from('pengabdian_dosen')
            ->join('dosen', 'pengabdian_dosen.nik', '=', 'dosen.nik')
            ->whereColumn('pengabdian_dosen.id_pengabdian', 'pengabdian.id_pengabdian')
            ->where('dosen.prodi', $prodiFilter);
    });

    if ($filterYear !== 'all') {
        $judulQuery->whereYear('tanggal_pengabdian', $filterYear);
    }

    $judulPengabdianSI = $judulQuery->pluck('judul')->toArray();
}
```

### 2. View Changes

**File:** `resources/views/dekan/dashboard.blade.php`

#### Added CSS Styles

Added styles for the word cloud container in the `@push('styles')` section:

```css
/* Word Cloud Styles */
#wordCloudContainer {
    position: relative;
    width: 100%;
    height: 400px;
    background: linear-gradient(
        135deg,
        rgba(78, 115, 223, 0.03) 0%,
        rgba(28, 200, 138, 0.03) 100%
    );
    border-radius: 8px;
}

.wordcloud-empty {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 400px;
    color: #6c757d;
}
```

#### Added HTML Card Section

Added a new card section after the Jenis Luaran Treemap that displays only for Kaprodi SI:

-   Conditional rendering using `@if(isset($prodiFilter) && $prodiFilter === 'Sistem Informasi')`
-   Shows year filter indicator
-   Displays empty state if no data available
-   Shows total count of pengabdian titles

```blade
@if(isset($prodiFilter) && $prodiFilter === 'Sistem Informasi')
<!-- Word Cloud Section (Only for Kaprodi SI) -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow modern-card">
            <div class="card-header py-3">
                <h6 class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                    <i class="fas fa-cloud mr-2"></i>Word Cloud - Judul Pengabdian Sistem Informasi
                    @if ($filterYear !== 'all')
                        <span class="text-primary">({{ $filterYear }})</span>
                    @else
                        <small class="text-muted">(Semua Tahun)</small>
                    @endif
                </h6>
            </div>
            <div class="card-body">
                @if(count($judulPengabdianSI) > 0)
                    <div id="wordCloudContainer"></div>
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Total:</strong> {{ count($judulPengabdianSI) }} judul pengabdian
                        </small>
                    </div>
                @else
                    <div class="wordcloud-empty">
                        <div class="text-center">
                            <i class="fas fa-cloud fa-3x mb-3"></i>
                            <div class="h6">Belum ada data judul pengabdian</div>
                            <p class="text-muted small">Word cloud akan muncul di sini ketika ada data</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
```

#### Added JavaScript Function

Created `createWordCloud()` function using D3.js in the `@push('scripts')` section:

**Features:**

1. **Text Processing:**

    - Combines all pengabdian titles into one text string
    - Converts to lowercase
    - Removes punctuation
    - Filters out common stopwords (Indonesian and English)
    - Filters out words shorter than 4 characters

2. **Word Frequency Analysis:**

    - Counts frequency of each word
    - Sorts by frequency
    - Takes top 50 most frequent words

3. **Visual Rendering:**

    - Uses D3.js force simulation to prevent word overlaps
    - Font size scales based on word frequency (14px to 60px)
    - Color scale using 8 distinct colors
    - Smooth animations on load and hover

4. **Interactive Features:**

    - Hover effect: words grow 20% larger
    - Opacity transitions
    - Force simulation prevents text overlaps
    - Responsive to window resize

5. **Stopwords Filter:**
    - Indonesian: dan, atau, yang, di, ke, dari, pada, untuk, dengan, adalah, etc.
    - English: the, a, an, of, to, in, for, on, with, as, by, etc.

### 3. Integration Points

#### Document Ready Handler

Added word cloud initialization to the document ready event:

```javascript
$(document).ready(function () {
    loadFundingSourcesChart();
    createJenisLuaranChart();
    createWordCloud(); // Initialize word cloud
});
```

#### Window Resize Handler

Added word cloud recreation on window resize:

```javascript
$(window).resize(function () {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(function () {
        createJenisLuaranChart();
        createWordCloud(); // Recreate on resize
    }, 250);
});
```

#### Year Filter Integration

The word cloud automatically updates when the year filter changes because:

-   The year dropdown triggers a form submission
-   The page reloads with new filtered data
-   The `$judulPengabdianSI` array is regenerated with the filtered data
-   The word cloud renders with the new data

## Display Rules

### Visibility

-   **Kaprodi SI:** ✅ Word cloud is visible
-   **Kaprodi TI:** ❌ Word cloud is hidden
-   **Dekan:** ❌ Word cloud is hidden

### Data Filtering

-   If "Semua Tahun" (all years) is selected: Shows all pengabdian titles from Sistem Informasi prodi
-   If specific year is selected: Shows only pengabdian titles from that year

### Empty State

If no pengabdian data exists for the selected year:

-   Shows empty state message
-   Displays cloud icon
-   Provides helpful text: "Word cloud akan muncul di sini ketika ada data"

## Technical Details

### Dependencies

-   **D3.js v7:** Already included in the dashboard for other charts
-   **jQuery:** Already included in the project
-   **Bootstrap 4:** Already included for styling

### Algorithm

1. Collect all titles from database
2. Combine into single text string
3. Normalize text (lowercase, remove punctuation)
4. Split into individual words
5. Filter out stopwords and short words
6. Count word frequencies
7. Sort by frequency (descending)
8. Take top 50 words
9. Calculate font sizes based on frequency
10. Use force simulation to position words without overlap
11. Render with D3.js SVG

### Performance Considerations

-   Limits to top 50 words to prevent overcrowding
-   Force simulation stops after 3 seconds to save CPU
-   Debounced resize handler (250ms delay) to prevent excessive recalculations

## Testing Checklist

-   [ ] Word cloud appears on Kaprodi SI dashboard
-   [ ] Word cloud does NOT appear on Kaprodi TI dashboard
-   [ ] Word cloud does NOT appear on Dekan dashboard
-   [ ] Year filter updates the word cloud correctly
-   [ ] Empty state displays when no data exists
-   [ ] Word hover effects work properly
-   [ ] Word cloud recreates on window resize
-   [ ] Word frequencies are accurate
-   [ ] Stopwords are properly filtered out
-   [ ] Text shows proper year indicator in header

## Files Modified

1. `app/Http/Controllers/Kaprodi/KaprodiController.php`

    - Added `$judulPengabdianSI` data collection
    - Added `$prodiFilter` to compact array

2. `resources/views/dekan/dashboard.blade.php`
    - Added word cloud CSS styles
    - Added word cloud HTML card section
    - Added `createWordCloud()` JavaScript function
    - Added word cloud initialization calls

## Future Enhancements

Potential improvements for future versions:

1. Add tooltip showing word frequency on hover
2. Make word cloud clickable to filter/search pengabdian by keyword
3. Add option to toggle between different layouts (spiral, rectangular)
4. Export word cloud as image
5. Add more sophisticated text processing (stemming, lemmatization)
6. Allow custom stopword list configuration
7. Add animation when transitioning between years
