@props([
    'submit' => null,
    'cancel' => null,
])

<div class="mt-6 flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
    <button type="button" class="bakery-btn-ghost w-full sm:w-auto" data-modal-close>
        {{ $cancel ?? __('ui.cancel') }}
    </button>
    <button type="submit" class="bakery-btn-primary w-full sm:w-auto disabled:cursor-not-allowed disabled:opacity-60" data-submit-btn>
        <span data-submit-label>{{ $submit ?? __('ui.save') }}</span>
        <span data-submit-loading class="hidden items-center gap-2">
            <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4" />
            </svg>
            {{ __('ui.saving') }}
        </span>
    </button>
</div>
