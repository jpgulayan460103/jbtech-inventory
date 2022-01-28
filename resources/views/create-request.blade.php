<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Items') }}
        </h2>
    </x-slot>

    @section('content')
    <h1 style="text-align: center">CREATE REQUEST</h1>
    <requests-create :warehouses="{{ $warehouses }}"></requests-create>
    @endsection
</x-app-layout>
