<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('USERS') }}
        </h2>
    </x-slot>

    @section('content')
    <h1 style="text-align: center">USERS</h1>
    <users :warehouses="{{ $warehouses }}" :user="{{ $user }}"></users>
    @endsection
</x-app-layout>
