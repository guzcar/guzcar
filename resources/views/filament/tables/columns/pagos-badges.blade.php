@php
    use Illuminate\Support\Js;

    $items   = collect($getState() ?? []);
    $limit   = 5;
    $visible = $items->take($limit);
    $hidden  = $items->skip($limit);

    $badgeClasses = 'fi-badge inline-flex items-center justify-center gap-x-1 rounded-md text-xs font-medium
        ring-1 ring-inset px-2 min-w-[theme(spacing.6)] py-1
        fi-color-custom bg-custom-50 text-custom-600 ring-custom-600/10
        dark:bg-custom-400/10 dark:text-custom-400 dark:ring-custom-400/30 fi-color-primary';
@endphp

<div x-data="{ open: false }" class="flex gap-1.5 flex-wrap relative px-3 py-4" style="width: 12rem">
    {{-- Visibles --}}
    @foreach ($visible as $pago)
        @php
            $titulo = trim($pago['fecha_pago'] ?? '');
            if (!empty($pago['detalle'])) {
                $titulo .= ' · ' . $pago['detalle'];
            }
        @endphp

        <div class="inline-flex w-max">
            <button
                type="button"
                title="{{ $titulo }}"
                class="{{ $badgeClasses }}"
                style="--c-50:var(--primary-50);--c-400:var(--primary-400);--c-600:var(--primary-600);"
                {{-- Dispara la Table Action (Infolist) con argumentos seguros --}}
                wire:click.stop.prevent="
                    mountTableAction(
                        'verPago',
                        {{ Js::from($getRecord()->getKey()) }},
                        {{ Js::from(['pago_id' => $pago['id']]) }}
                    )
                "
            >
                <span class="grid">
                    <span class="truncate">S/ {{ $pago['monto'] }}</span>
                </span>
            </button>
        </div>
    @endforeach

    {{-- Botón +N si hay más --}}
    @if ($hidden->isNotEmpty())
        <div class="inline-flex w-max">
            <button
                type="button"
                x-on:click.stop="open = !open"
                class="{{ $badgeClasses }}"
                title="+{{ $hidden->count() }}"
            >+{{ $hidden->count() }}</button>
        </div>

        {{-- Popover con los demás --}}
        <div
            x-show="open" x-transition x-on:click.outside="open = false"
            class="absolute z-10 mt-2 w-64 max-h-72 overflow-auto rounded-md border border-gray-200 bg-white p-2 shadow-lg
                   dark:bg-gray-900 dark:border-gray-700"
        >
            <div class="flex flex-col gap-1">
                @foreach ($hidden as $pago)
                    @php
                        $titulo = trim('Pago ' . ($pago['fecha_pago'] ?? ''));
                        if (!empty($pago['detalle'])) {
                            $titulo .= ' · ' . $pago['detalle'];
                        }
                    @endphp

                    <div class="inline-flex w-full">
                        <button
                            type="button"
                            title="{{ $titulo }}"
                            class="{{ $badgeClasses }} w-full justify-start text-left"
                            style="--c-50:var(--primary-50);--c-400:var(--primary-400);--c-600:var(--primary-600);"
                            wire:click.stop.prevent="
                                mountTableAction(
                                    'verPago',
                                    {{ Js::from($getRecord()->getKey()) }},
                                    {{ Js::from(['pago_id' => $pago['id']]) }}
                                )
                            "
                        >
                            <span class="grid">
                                <span class="truncate">S/ {{ $pago['monto'] }}</span>
                            </span>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
