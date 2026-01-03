@push('styles')
<style>
    /* Styling the clear button in Select2 */
    .select2-container .select2-selection__clear {
        position: absolute; /* Position absolute to place it relative to the container */
        right: 30px;        /* Adjust position */
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.2rem;
        color: #858796;
        font-weight: bold;
        line-height: 1;
        z-index: 10;
        margin-right: 0;
    }
    .select2-container .select2-selection__clear:hover {
        color: #e74a3b;
    }

    /* Style for single select to mimic multi-select tag (Chip) */

    /* Handle placeholder (don't show as chip if possible, or style differently) */
    /* Note: Select2 puts placeholder text in rendered. If we want it to NOT look like a chip, we need JS or accept it. */
    /* We'll accept the chip look for now or try to style based on color if it's default placeholder color */
    .select2-container .select2-selection--single .select2-selection__rendered[title*="Pilih"] {
         /* Heuristic: if title contains 'Pilih', it might be placeholder. But title is set to text. Placeholder usually has specific class inside? */
         /* Actually, when empty, Select2 inserts <span class="select2-selection__placeholder">...</span> inside rendered. */
    }

    /* If placeholder exists inside rendered, reset the rendered container style? CSS Selectors 4 :has() would work, but safety first. */
    /* Let's style the placeholder span to cover the parent background if needed, or just let it be. */
    /* Better: Use the fact that placeholder span exists to modify look if we can. 
       Since we can't do parent selector easily without :has, we will style the rendered element to look like a chip 
       BUT we will make the background transparent for placeholder using :has if supported or just accept it. 
       Let's stick to a robust simpler style that looks good even for placeholder (maybe lighter background). */
    
    .select2-container .select2-selection--single {
        height: auto !important; /* Allow growing if needed */
        min-height: calc(1.5em + 0.75rem + 2px);
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
        top: 0;
    }

    /* Specific fix for the placeholder element to try and hide chip background? */
    /* Actually, we can use the fact that invalid/empty selects might have a class? No. */

</style>
@endpush

@push('scripts')
<script>
/**
 * Shared Select2 Initialization Helper
 * Standardizes behavior for allowClear and placeholders across pages.
 */
function initSelect2WithClear(selector, extraOptions = {}) {
    const defaultOptions = {
        width: '100%',
        allowClear: true,
        placeholder: function() {
            // Retrieve placeholder from data attribute
            return $(this).data('placeholder') || '— Pilih —';
        },
        // Ensure consistent language/behavior if needed
        language: {
            noResults: function() {
                return "Tidak ada data ditemukan";
            }
        }
    };

    // Merge defaults with any extra options provided
    const settings = $.extend({}, defaultOptions, extraOptions);

    // Initialize Select2
    const $element = $(selector);
    $element.select2(settings);

    // UX: Ensure 'x' clears effectively and focus management (optional refinements)
    $element.on('select2:unselecting', function (e) {
        // Optional: logic if specific cleanup needed before unselect
    });
}
</script>
@endpush
