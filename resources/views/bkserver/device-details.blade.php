@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">

        <h1 class="text-2xl font-bold mb-6">{{ $device->name }} - Detalhes</h1>

        <div class="flex flex-col sm:flex-row gap-4 shadow rounded-lg p-4 bg-white">
            <!-- Estado do Dispositivo -->
            <div class="flex-1">
                <h2 class="text-lg font-semibold">Estado do Dispositivo</h2>
                <p>Status: {{ $device->status }}</p>
                <p>Criado em: {{ $device->created_at }}</p>
            </div>

            <!-- Acesso -->
            <div class="flex-1">
                <h2 class="text-lg font-semibold">Acesso</h2>
                <p>Endereço IP: {{ $device->ip }}</p>
                <p>Porta: {{ $device->ssh }}</p>
            </div>

            <!-- Backup -->
            <div class="flex-1">
                <h2 class="text-lg font-semibold">Backup</h2>
                <form method="POST" action="{{ route('compare.backups') }}">
                    @csrf
                    <div class="flex gap-4 mb-4">
                        <div>
                            <label for="backup1">Backup 1:</label>
                            <select name="backup1" id="backup1">
                                @foreach ($backups as $backup)
                                    <option value="{{ $backup->id }}">{{ $backup->created_at }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="backup2">Backup 2:</label>
                            <select name="backup2" id="backup2">
                                @foreach ($backups as $backup)
                                    <option value="{{ $backup->id }}">{{ $backup->created_at }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="p-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">Comparar
                        Backups</button>
                </form>
            </div>
        </div>

        <!-- Resultados da Comparação -->
        @if (isset($backup1Data) && isset($backup2Data))
            <div class="mt-8 bg-white p-4 shadow rounded">
                <h2 class="text-xl font-semibold mb-4">Comparação dos Backups</h2>
                <div class="flex gap-4">
                    <div class="flex-1">
                        <h3 class="font-semibold">Backup 1</h3>
                        <pre class="bg-gray-100 p-4 rounded">{{ $backup1Data }}</pre>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold">Backup 2</h3>
                        <pre class="bg-gray-100 p-4 rounded">{{ $backup2Data }}</pre>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection
