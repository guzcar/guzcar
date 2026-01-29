<div 
    x-data
    x-init="
        new Sortable($refs.items, {
            animation: 150,
            ghostClass: 'bg-gray-100',
            onEnd: (evt) => {
                let sortable = evt.to;
                let list = [];
                Array.from(sortable.children).forEach((el) => {
                    list.push({ value: el.getAttribute('data-id') });
                });
                $wire.updateOrder(list);
            }
        });
    "
>
    <ul x-ref="items" class="space-y-2">
        @foreach($items as $item)
            <li 
                data-id="{{ $item['type'] }}-{{ $item['id'] }}" 
                class="p-3 border rounded flex justify-between items-center cursor-move bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:border-gray-600"
            >
                <div class="flex items-center gap-3">
                    <x-heroicon-o-bars-3 class="w-5 h-5 text-gray-400" />
                    <div>
                        <span class="text-xs px-2 py-0.5 rounded opacity-70 w-20 inline-block {{ $item['type'] == 'articulo' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300' }}">
                            {{ $item['type'] == 'articulo' ? 'Artículo' : 'Otro' }}
                        </span>
                        <span>{{ $item['descripcion'] }}</span>
                    </div>
                </div>
                <div class="text-sm text-gray-500 opacity-70 dark:text-gray-400 dark:opacity-80">
                    {{ $item['cantidad'] }} x S/ {{ $item['precio'] }}
                </div>
            </li>
        @endforeach
    </ul>
</div>