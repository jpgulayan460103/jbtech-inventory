<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Items') }}
        </h2>
    </x-slot>

    @section('contentFluid')
    <h1 style="text-align: center">Reports</h1>
    <reports-index :warehouses="{{ $warehouses }}" :items="{{ $items }}" :user="{{ $user }}"></reports-index>
    @endsection
</x-app-layout>
