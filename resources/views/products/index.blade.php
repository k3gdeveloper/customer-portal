@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Produtos</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($products as $product)
                <div class="bg-white p-4 rounded-lg shadow-md flex items-center justify-between">
                    <span class="{{ $product->is_active ? 'text-black' : 'text-gray-400' }}">
                        {{ $product->name }}
                    </span>
                    @if (!$product->is_active)
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 12V8a4 4 0 118 0v4m-6 4h8m-8-4v2a2 2 0 004 0v-2m8-2v4a2 2 0 01-2 2h-6a2 2 0 01-2-2v-4m10 0V8a6 6 0 10-12 0v4">
                            </path>
                        </svg>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection
