<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Items') }}
        </h2>
    </x-slot>

    @section('content')
    <h1 style="text-align: center">ITEMS</h1>
    <div class="row">
        <div class="col-sm-12">
            <example-component></example-component>
        </div>
    </div>
    @endsection
</x-app-layout>
