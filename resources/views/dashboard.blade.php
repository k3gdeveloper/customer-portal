@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-center">
            <div class="w-full">
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="bg-gray-800 text-white px-6 py-4 flex justify-between items-center">
                        <h2 class="text-2xl">{{ __('Tickets') }}</h2>
                        <form method="GET" action="{{ route('dashboard') }}" class="flex space-x-4">
                            <div>
                                <label for="start_date"
                                    class="block text-sm font-medium text-gray-200">{{ __('Data de início') }}</label>
                                <input type="date" name="start_date" id="start_date"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="end_date"
                                    class="block text-sm font-medium text-gray-200">{{ __('Data de fim') }}</label>
                                <input type="date" name="end_date" id="end_date"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="flex items-end">
                                <button type="submit"
                                    class="ml-4 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('Filtrar') }}
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="px-6 py-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="w-1/6 px-4 py-2">{{ __('ID') }}</th>
                                        {{-- <th class="w-1/6 px-4 py-2">{{ __('Localização') }}</th> --}}
                                        {{-- <th class="w-1/6 px-4 py-2">{{ __('Categoria') }}</th> --}}
                                        {{-- <th class="w-1/6 px-4 py-2">{{ __('Descrição') }}</th> --}}
                                        <th class="w-1/6 px-4 py-2">{{ __('Nome') }}</th>
                                        <th class="w-1/6 px-4 py-2">{{ __('Status') }}</th>
                                        <th class="w-1/6 px-4 py-2">{{ __('Data de abertura') }}</th>
                                        <th class="w-1/6 px-4 py-2">{{ __('Data da solução') }}</th>
                                        <th class="w-1/6 px-4 py-2">{{ __('Tempo de solução') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tickets as $ticket)
                                        <tr class="hover:bg-gray-100">
                                            <td
                                                class="border px-4 py-2 whitespace-nowrap overflow-hidden text-ellipsis max-w-xs transition-all duration-300 ease-in-out hover:animate-slide">
                                                {{ $ticket['id'] }}</td>
                                            <td
                                                class="border px-4 py-2 whitespace-nowrap overflow-hidden text-ellipsis max-w-xs transition-all duration-300 ease-in-out hover:animate-slide">
                                                {{ $ticket['name'] }}</td>
                                            <td
                                                class="border px-4 py-2 whitespace-nowrap overflow-hidden text-ellipsis max-w-xs transition-all duration-300 ease-in-out hover:animate-slide">
                                                {{ $ticket['status'] }}</td>
                                            <td
                                                class="border px-4 py-2 whitespace-nowrap overflow-hidden text-ellipsis max-w-xs transition-all duration-300 ease-in-out hover:animate-slide">
                                                {{ $ticket['date'] }}</td>
                                            <td
                                                class="border px-4 py-2 whitespace-nowrap overflow-hidden text-ellipsis max-w-xs transition-all duration-300 ease-in-out hover:animate-slide">
                                                {{ $ticket['solvedate'] }}</td>
                                            <td
                                                class="border px-4 py-2 whitespace-nowrap overflow-hidden text-ellipsis max-w-xs transition-all duration-300 ease-in-out hover:animate-slide">
                                                {{ $ticket['solve_delay_formatted'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom CSS for Animation -->
    <style>
        @keyframes slide {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }

        .animate-slide {
            animation: slide 0.5s forwards;
        }
    </style>
@endsection
