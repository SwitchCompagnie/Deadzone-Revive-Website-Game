<x-filament-panels::page>
    <div class="space-y-6">
        @if(App\Models\Setting::isMaintenanceMode())
            <div class="bg-red-50 dark:bg-red-950 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">
                            <strong>Maintenance Mode is Currently ACTIVE</strong>
                        </p>
                        <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                            Users cannot login or access the game. Only administrators can access the site.
                        </p>
                        <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                            <strong>Auto-broadcast:</strong> Your maintenance message is being automatically broadcast to all players every 30 seconds.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-green-50 dark:bg-green-950 border-l-4 border-green-500 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            <strong>Site is Operating Normally</strong>
                        </p>
                        <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                            Users can login and access the game without restrictions.
                        </p>
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
