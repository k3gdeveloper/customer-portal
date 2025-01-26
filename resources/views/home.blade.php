@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="grid gap-6">
            <!-- Sidebar -->
            {{--             <div class="col-span-3 bg-white shadow-lg rounded-lg p-6 space-y-4">
                <h2 class="text-xl font-semibold mb-4">Menu</h2>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-600 hover:text-blue-600 transition">Dashboard</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-blue-600 transition">Profile</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-blue-600 transition">Settings</a></li>
                    <li><a href="#" class="text-red-500 hover:text-red-700 transition">Logout</a></li>
                </ul>
            </div> --}}

            <!-- Main Content -->
            <div class="col-span-9 bg-white shadow-lg rounded-lg p-6">
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2"> Bem-vindo, {{ Auth::user()->name }}!</h1>
                    <p class="text-gray-500">Visão geral da sua conta e atividades recentes.</p>
                </div>

                <!-- Cards Section -->
                <div class="grid grid-cols-3 gap-6 mb-6">
                    <div class="bg-gradient-to-r from-blue-500 via-blue-400 to-blue-300 p-4 rounded-lg shadow-lg text-white">
                        <h3 class="text-lg font-semibold">Sales</h3>
                        <p class="mt-2 text-2xl">135</p>
                        <p class="text-sm opacity-75">Hoje</p>
                    </div>
                    <div
                        class="bg-gradient-to-r from-green-500 via-green-400 to-green-300 p-4 rounded-lg shadow-lg text-white">
                        <h3 class="text-lg font-semibold">Visitas</h3>
                        <p class="mt-2 text-2xl">2,345</p>
                        <p class="text-sm opacity-75">Este mês</p>
                    </div>
                    <div
                        class="bg-gradient-to-r from-yellow-500 via-yellow-400 to-yellow-300 p-4 rounded-lg shadow-lg text-white">
                        <h3 class="text-lg font-semibold">Novos Usuários</h3>
                        <p class="mt-2 text-2xl">29</p>
                        <p class="text-sm opacity-75">Esta semana</p>
                    </div>
                </div>

                <!-- Activity Chart -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Atividade Recente</h2>
                    <div class="relative h-64">
                        <canvas id="activityChart" class="h-full"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charting scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.6.2/dist/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('activityChart').getContext('2d');

            const activityData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Atividades',
                    data: [65, 59, 80, 81, 56, 55],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    tension: 0.3 // Smoothing the line
                }]
            };

            const activityChart = new Chart(ctx, {
                type: 'line',
                data: activityData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endsection
