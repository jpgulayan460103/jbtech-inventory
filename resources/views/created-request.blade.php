<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Items') }}
        </h2>
    </x-slot>

    @section('content')
    <h1 style="text-align: center">REQUESTS</h1>
    <requests-created :created-request="{{ $created_request }}"></requests-created>
    @endsection
</x-app-layout>
