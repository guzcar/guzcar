<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    @livewire('contabilidad.items-sorter', ['record' => $getRecord()])
</x-dynamic-component>