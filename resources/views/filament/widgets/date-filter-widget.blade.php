<x-filament-widgets::widget>
    <x-filament::section heading="Filtrar por fechas" icon="heroicon-o-calendar">
        <form wire:submit.prevent class="space-y-4">
            {{ $this->form }}
        </form>
    </x-filament::section>
</x-filament-widgets::widget>