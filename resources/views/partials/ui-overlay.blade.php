{{-- Custom confirm modal (replaces window.confirm everywhere) --}}
<div id="z-confirm" class="fixed inset-0 z-[100] hidden" role="alertdialog" aria-modal="true" aria-labelledby="z-confirm-message">
  <div class="z-modal-backdrop absolute inset-0 bg-ink/70"></div>
  <div class="relative h-full flex items-center justify-center p-6">
    <div class="z-modal-panel bg-paper w-full max-w-sm rounded-lg shadow-xl p-6">
      <p id="z-confirm-message" class="text-ink text-base leading-relaxed mb-6">Are you sure?</p>
      <div class="flex justify-end gap-3">
        <button type="button" id="z-confirm-no" class="px-5 py-2.5 text-sm uppercase tracking-wide border border-ink/20 text-ink/70 hover:border-ink hover:text-ink transition-colors">Cancel</button>
        <button type="button" id="z-confirm-yes" class="px-5 py-2.5 text-sm uppercase tracking-wide bg-clay text-paper hover:bg-clay/90 transition-colors">Yes, continue</button>
      </div>
    </div>
  </div>
</div>

{{-- Blocking loading overlay: visible progress bar for uploads, a Cancel
     option, and nothing behind it is clickable while it's up. --}}
<div id="z-loading" class="fixed inset-0 z-[100] hidden" role="alertdialog" aria-modal="true" aria-labelledby="z-loading-label">
  <div class="z-modal-backdrop absolute inset-0 bg-ink/80"></div>
  <div class="relative h-full flex items-center justify-center p-6">
    <div class="z-modal-panel bg-paper w-full max-w-sm rounded-lg shadow-xl p-8 text-center">
      <svg class="z-spinner animate-spin h-8 w-8 mx-auto mb-4 text-clay" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
      </svg>
      <p id="z-loading-label" class="text-ink font-medium mb-4">Working on it&hellip;</p>

      <div id="z-loading-bar-wrap" class="hidden mb-4">
        <div class="w-full h-2 bg-ink/10 rounded-full overflow-hidden">
          <div id="z-loading-bar-fill" class="h-full bg-clay transition-all duration-150 ease-out" style="width:0%"></div>
        </div>
        <p id="z-loading-percent" class="text-xs font-mono text-ink/50 mt-2">0%</p>
      </div>

      <p id="z-loading-error" class="hidden text-sm text-clay border border-clay/30 bg-clay/10 rounded px-3 py-2 mb-4" role="alert"></p>

      <button type="button" id="z-loading-cancel" class="hidden text-sm uppercase tracking-wide text-ink/60 hover:text-clay transition-colors">Cancel</button>
    </div>
  </div>
</div>
