<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Items') }}
        </h2>
    </x-slot>

    @section('content')
    <h1 style="text-align: center">Reports</h1>
    <reports-index :warehouses="{{ $warehouses }}" :report-data="{{ $items }}" :user="{{ $user }}" warehouse-id="{{ $warehouse_id }}" date="{{ $date }}"></reports-index>
    @endsection
</x-app-layout>
