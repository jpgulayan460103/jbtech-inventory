<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Items') }}
        </h2>
    </x-slot>

    @section('content')
    <h1 style="text-align: center">PROCESS REQUESTS</h1>
    <process-request :created-request="{{ $created_request }}" :user="{{ $user }}"></process-request>
    @endsection
</x-app-layout>
