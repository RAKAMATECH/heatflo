<div>
    <div x-data x-init="if (@js($open)) { $wire.openModal(); setTimeout(() => { $wire.closeModal() }, 4000) }">
    <x-filament::modal id="admin-welcome-modal" :close-by-clicking-away="true">
            <div class="text-lg font-semibold">
                {{ $greeting }}
            </div>
            <div class="mt-2 text-sm text-gray-600">
                Welcome back to Heatflo Home Solutions Admin.
            </div>
    </x-filament::modal>
    </div>
</div>
