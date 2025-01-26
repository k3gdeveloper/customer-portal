@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-4">Configurações de Backup</h1>

        <!-- Search Form -->
        <form method="GET" action="{{ route('bkserver.index') }}" class="flex items-center mb-6">
            <input type="text" name="search" placeholder="Buscar dispositivo ou IP"
                class="p-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="p-2 bg-blue-500 text-white rounded-r-lg hover:bg-blue-600 transition">
                Buscar
            </button>
        </form>

        <!-- Backup Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            @if ($devices->isEmpty())
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nome e Status do Dispositivo
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Endereço IP
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fabricante
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acesso RSA
                            </th>
                        </tr>
                    </thead>
                </table>
                <div class="p-4">

                    <p class="text-center text-gray-500">Nenhum dispositivo disponível.</p>
                </div>
            @else
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nome e Status do Dispositivo
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Endereço IP
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fabricante
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th
                                class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acesso RSA
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach ($devices as $device)
                            <tr>
                                <td class="px-6 py-4 border-b border-gray-200">
                                    <div>
                                        <a href="{{ route('bkserver.show', $device->id) }}"
                                            class="text-dark hover:underline">
                                            {{ $device->name }}
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4 border-b border-gray-200">
                                    <div class="flex items-center">
                                        <div>
                                            @php
                                                $status = 'Unknown';
                                                $today = Carbon\Carbon::today()->toDateString();

                                                if ($device->backups->isNotEmpty()) {
                                                    $latestBackup = $device->backups->first();
                                                    if ($latestBackup->created_at->toDateString() === $today) {
                                                        $status = 'Online';
                                                    } else {
                                                        $status = 'Offline';
                                                    }
                                                }
                                            @endphp
                                            @if ($status === 'Online')
                                                <img src="{{ asset('img/svg/greencloud.svg') }}" alt="Online" />
                                            @elseif ($status === 'Offline')
                                                <img src="{{ asset('img/svg/redcloud.svg') }}" alt="Offline" />
                                            @else
                                                <img src="{{ asset('img/svg/graycloud.svg') }}" alt="Unknown" />
                                            @endif
                                        </div>
                                        <div class="ml-5">
                                            @if ($device->status === 'Online')
                                                <img src="{{ asset('img/png/zabbix_red.png') }}" alt="Online" />
                                            @elseif ($device->status === 'Offline')
                                                <img src="{{ asset('img/svg/redcloud.svg') }}" alt="Offline" />
                                            @else
                                                <img src="{{ asset('img/png/zabbix_gray.png') }}" alt="Unknown"
                                                    class="w-5" />
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 border-b border-gray-200">{{ $device->ip }}</td>
                                <td class="px-6 py-4 border-b border-gray-200">{{ $device->so }}</td>
                                <td class="px-6 py-4 border-b border-gray-200">{{ $status }}</td>
                                <td class="px-6 py-4 border-b border-gray-200">{{ $device->rsakey ? 'Sim' : 'Não' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Pagination Links -->
        <div class="mt-6">{{ $devices->links() }}</div>
    </div>
@endsection
