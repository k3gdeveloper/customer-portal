@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">

        <div class="flex flex-col items-center">
            <div class="w-full bg-white shadow-md rounded-lg overflow-hidden">
                <div class="bg-gray-800 text-white py-4 px-6 text-lg font-semibold">
                    {{ __('Incidentes por localidade') }}
                </div>
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row items-center justify-end gap-4 mb-4">
                        <input type="date" id="start-date"
                            class="form-input w-full sm:w-auto border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="date" id="end-date"
                            class="form-input w-full sm:w-auto border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button id="filter-button"
                            class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Filtrar</button>
                    </div>

                    <div id="map" class="w-full h-96 rounded-md shadow-md mb-4"></div>

                    <div class="flex items-center justify-between gap-4">
                        <button id="prev-button"
                            class="bg-gray-600 text-white px-2 py-2 rounded shadow hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">◄</button>
                        <div id="buttons" class="flex flex-wrap gap-2 overflow-hidden w-full justify-start">
                            <!-- Botões dinâmicos serão adicionados aqui -->
                        </div>
                        <button id="next-button"
                            class="bg-gray-600 text-white px-4 py-2 rounded shadow hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">►</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inclua o CSS do Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <!-- Inclua o Google Fonts para Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <!-- Inclua o JS do Leaflet -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <!-- Inclua o JS do Leaflet.heat -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .btn-custom {
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #007BFF;
            transform: scale(1.05);
        }

        .btn-custom:focus {
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.5);
        }

        .hidden {
            display: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var map = L.map('map').setView([-3.1190300, -58.8217314], 5); // Coordenadas de exemplo e nível de zoom

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {

                attribution: 'Map data © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            var tickets = @json($tickets);

            console.log(tickets); // Verificar os dados no console

            // Função para adicionar marcadores ao mapa
            function addMarkers(tickets) {
                // Criar uma matriz para o mapa de calor
                var heatData = tickets.map(function(ticket) {
                    if (ticket.latitude && ticket.longitude && ticket.incidents) {
                        return [ticket.latitude, ticket.longitude, ticket
                            .incidents
                        ]; // [lat, lng, intensidade]
                    }
                }).filter(Boolean); // Remove entradas indefinidas

                // Remover mapa de calor anterior, se existir
                if (window.heatLayer) {
                    map.removeLayer(window.heatLayer);
                }

                // Adicionar o mapa de calor ao mapa
                if (heatData.length) {
                    window.heatLayer = L.heatLayer(heatData, {
                        radius: 25, // Tamanho do raio das bolinhas de calor
                        blur: 15, // Desfoque das bolinhas de calor
                        maxZoom: 17, // Zoom máximo para exibir as bolinhas
                        gradient: {
                            0.2: 'blue',
                            0.4: 'lime',
                            0.6: 'yellow',
                            0.8: 'orange',
                            1.0: 'red'
                        } // Gradiente de cores
                    }).addTo(map);
                } else {
                    console.log('Heat data is empty');
                }

                // Crie um objeto para armazenar os marcadores
                var markers = {};
                var allMarkers = [];
                var visibleCompletename = null;

                // Adicionar os marcadores no mapa e ao objeto markers
                tickets.forEach(function(ticket) {
                    if (ticket.latitude && ticket.longitude) {
                        var marker = L.marker([ticket.latitude, ticket.longitude]).bindPopup("<b>" + ticket
                            .completename + "</b><br>Incidentes: " + ticket.incidents);
                        allMarkers.push(marker);
                        if (!markers[ticket.completename]) {
                            markers[ticket.completename] = [];
                        }
                        markers[ticket.completename].push(marker);
                        marker.addTo(map);
                    }
                });

                // Criar botões para cada completename com navegação
                var buttonsContainer = document.getElementById('buttons');
                buttonsContainer.innerHTML = ''; // Limpar botões anteriores
                var pageSize = 6;
                var currentPage = 0;
                var totalPages = Math.ceil(Object.keys(markers).length / pageSize);

                function renderButtons() {
                    buttonsContainer.innerHTML = ''; // Limpar botões anteriores
                    var start = currentPage * pageSize;
                    var end = start + pageSize;
                    var keys = Object.keys(markers).slice(start, end);
                    keys.forEach(function(completename) {
                        var button = document.createElement('button');
                        button.innerText = completename;
                        button.className =
                            'text-xs btn-custom px-4 py-2 bg-slate-800 text-white rounded shadow hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50';
                        button.onclick = function() {
                            if (visibleCompletename === completename) {
                                // Adicionar todos os marcadores de volta ao mapa
                                allMarkers.forEach(function(marker) {
                                    marker.addTo(map);
                                });
                                // Resetar a variável de controle
                                visibleCompletename = null;
                            } else {
                                // Remover todos os marcadores do mapa
                                allMarkers.forEach(function(marker) {
                                    map.removeLayer(marker);
                                });
                                // Adicionar os marcadores do completename selecionado
                                markers[completename].forEach(function(marker) {
                                    marker.addTo(map);
                                });
                                // Atualizar a variável de controle
                                visibleCompletename = completename;
                            }
                        };
                        buttonsContainer.appendChild(button);
                    });
                    // Atualizar a visibilidade dos botões de navegação
                    document.getElementById('prev-button').classList.toggle('hidden', currentPage === 0);
                    document.getElementById('next-button').classList.toggle('hidden', currentPage === totalPages -
                        1);
                }

                renderButtons();

                document.getElementById('prev-button').onclick = function() {
                    if (currentPage > 0) {
                        currentPage--;
                        renderButtons();
                    }
                };

                document.getElementById('next-button').onclick = function() {
                    if (currentPage < totalPages - 1) {
                        currentPage++;
                        renderButtons();
                    }
                };
            }

            // Adicionar os marcadores iniciais
            addMarkers(tickets);

            // Função para filtrar marcadores por data
            function filterByDate(startDate, endDate) {
                // Filtrar os tickets pelo intervalo de datas
                var filteredTickets = tickets.filter(function(ticket) {
                    var ticketDate = new Date(ticket
                        .date); // Substitua 'date' pelo nome do campo de data no seu ticket
                    return ticketDate >= startDate && ticketDate <= endDate;
                });

                // Recalcular o número de incidentes para cada completename
                var incidentsPerCompletename = {};
                filteredTickets.forEach(function(ticket) {
                    if (!incidentsPerCompletename[ticket.completename]) {
                        incidentsPerCompletename[ticket.completename] = 0;
                    }
                    incidentsPerCompletename[ticket.completename] += ticket.incidents;
                });

                // Atualizar os tickets filtrados com os incidentes recalculados
                filteredTickets = filteredTickets.map(function(ticket) {
                    return {
                        ...ticket,
                        incidents: incidentsPerCompletename[ticket.completename]
                    };
                });

                // Adicionar os marcadores filtrados
                addMarkers(filteredTickets);
            }


            // Evento de clique no botão de filtro
            document.getElementById('filter-button').onclick = function() {
                var startDate = new Date(document.getElementById('start-date').value);
                var endDate = new Date(document.getElementById('end-date').value);
                filterByDate(startDate, endDate);
            };
        });
    </script>
@endsection
