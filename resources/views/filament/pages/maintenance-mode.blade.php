<x-filament-panels::page>
    <div class="space-y-6">
        @if(App\Models\Setting::isMaintenanceMode())
            <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-lg p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex-1 space-y-3">
                        <div>
                            <p class="text-base font-semibold text-red-800 dark:text-red-200">
                                Maintenance Mode is Currently ACTIVE
                            </p>
                        </div>
                        <div class="space-y-2 text-sm text-red-700 dark:text-red-300">
                            <p>
                                Users cannot login or access the game. Only administrators can access the site.
                            </p>
                            <p>
                                <strong>Auto-broadcast:</strong> Your maintenance message is being automatically broadcast to all players every 30 seconds.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-lg p-6">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 space-y-3">
                        <div>
                            <p class="text-base font-semibold text-green-800 dark:text-green-200">
                                Site is Operating Normally
                            </p>
                        </div>
                        <div class="text-sm text-green-700 dark:text-green-300">
                            <p>
                                Users can login and access the game without restrictions.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form wire:submit="saveSettings">
            {{ $this->form }}
            <div class="mt-6 flex justify-end gap-3">
                @foreach($this->getFormActions() as $action)
                    {{ $action }}
                @endforeach
            </div>
        </form>

        <x-filament-actions::modals />
    </div>
</x-filament-panels::page>
