@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <div class="flex flex-col items-center">
            <div class="w-full bg-white shadow-md rounded-lg ">
                <div class="bg-gray-800 text-white py-4 px-6 text-lg font-semibold">
                    {{ __('Incidentes por localidade') }}
                </div>
                <div class="p-6">
                    @if ($mapId)
                        <iframe src="https://meta.k3gsolutions.com.br/public/dashboard/{{ $mapId }}" frameborder="0"
                            height="1280" allowtransparency class="w-full  rounded-md shadow-md mb-4 z-0"></iframe>
                    @else
                        <p>Mapa não disponível.</p>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet CSS e Scripts -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js"></script>
@endsection

@push('vite')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endpush
