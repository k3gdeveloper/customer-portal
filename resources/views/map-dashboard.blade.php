@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <div class="flex flex-col items-center">
            <div class="w-full bg-white shadow-md rounded-lg ">
                <div class="bg-gray-800 text-white py-4 px-6 text-lg font-semibold">
                    {{ __('Incidentes por localidade') }}
                </div>
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row items-center justify-end gap-4 mb-6">
                        <!-- Filtros -->
                        <form action="{{ route('map-dashboard') }}" method="GET" id="filter-form">
                            <div class="flex gap-4 items-center" id="filter-button">
                                <input type="date" name="start_date" id="start-date"
                                    class="form-input w-full sm:w-auto border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <input type="date" name="end_date" id="end-date"
                                    class="form-input w-full sm:w-auto border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="submit"
                                    class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Filtrar
                                </button>
                                <button type="button" id="clear-filters" onclick="clearFilters()"
                                    class="bg-gray-400 text-white px-6 py-2 rounded shadow hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    Limpar Filtros
                                </button>
                            </div>


                        </form>
                    </div>
                    <div class="flex gap-4">
                        <!-- Mapa -->
                        <div id="map" class="w-full h-96 rounded-md shadow-md mb-4"></div>

                        <!-- Detalhes dos Tickets -->
                        <div id="ticket-details"
                            class="w-1/3 bg-white p-4 rounded-md shadow-md ml-4  max-h-96 overflow-y-auto transition-opacity hidden opacity-0">
                            <button id="close-details" class="text-red-500 hover:text-red-800">&times;</button>
                            <h4 class="text-lg font-semibold mb-2">Detalhes dos Tickets</h4>

                            <!-- Filtros para ordenação -->
                            <div class="flex flex-col gap-4 mb-6">
                                <label for="ticket-filter" class="font-semibold">Filtrar Tickets:</label>
                                <select id="ticket-filter"
                                    class="form-select w-full sm:w-auto border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="id-asc">ID Crescente</option>
                                    <option value="id-desc">ID Decrescente</option>
                                    <option value="status">Por Status</option>
                                </select>
                                <div id="status-filter">
                                    <label for="status-select" class="font-semibold">Escolha o Status:</label>
                                    <select id="status-select"
                                        class="form-select w-full sm:w-auto border rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="1">Novo</option>
                                        <option value="2">Em atendimento (atribuído)</option>
                                        <option value="3">Em atendimento (planejado)</option>
                                        <option value="4">Pendente</option>
                                        <option value="5">Solucionado</option>
                                        <option value="6">Fechado</option>
                                    </select>
                                </div>
                            </div>

                            <div id="ticket-info" class="text-sm text-gray-700">
                                <!-- Detalhes dos tickets serão inseridos aqui -->
                            </div>
                        </div>

                    </div>
                    <!-- Botões de Localização -->
                    <div id="location-buttons" class="flex flex-wrap justify-center gap-2 mt-4"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet CSS e Scripts -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const map = L.map('map').setView([-3.1190300, -58.8217314], 5);

            // Adicionando camada do OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data © <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            const tickets = @json($tickets);

            const statuses = {
                1: 'Novo',
                2: 'Em atendimento (atribuído)',
                3: 'Em atendimento (planejado)',
                4: 'Pendente',
                5: 'Solucionado',
                6: 'Fechado',
            };

            let markerGroup = L.layerGroup().addTo(map);
            let heatLayer;
            let currentVisibleLocation = null;

            const buttonsContainer = document.getElementById('location-buttons');

            // Função para adicionar marcadores no mapa
            function updateTicketDetails(location, locationTickets) {
                const ticketDetails = document.getElementById('ticket-details');
                const ticketInfo = document.getElementById('ticket-info');
                const filterSelect = document.getElementById('ticket-filter');
                const statusFilter = document.getElementById('status-filter');
                const statusSelect = document.getElementById('status-select');

                ticketDetails.classList.remove('hidden', 'opacity-0');
                ticketDetails.classList.add('opacity-100');

                function applyFilter(filteredTickets) {
                    let detailsHtml = `<b>${location}</b><br><br>`;
                    detailsHtml += `<b>Total de Tickets:</b> ${filteredTickets.length}<br><br>`;
                    detailsHtml += filteredTickets.map(ticket => {
                        const statusText = statuses[ticket.status] || 'Status desconhecido';
                        return `<b>${ticket.id}.</b> ${ticket.name} (Status: ${statusText})`;
                    }).join('<br><br>');

                    if (filteredTickets.length === 0) {
                        detailsHtml += "Nenhum ticket encontrado com os critérios selecionados.";
                    }

                    ticketInfo.innerHTML = detailsHtml;
                }

                filterSelect.addEventListener('change', function() {
                    let filteredTickets = [...locationTickets];

                    if (filterSelect.value === 'id-asc') {
                        filteredTickets.sort((a, b) => a.id - b.id);
                    } else if (filterSelect.value === 'id-desc') {
                        filteredTickets.sort((a, b) => b.id - a.id);
                    } else if (filterSelect.value === 'status') {
                        statusFilter.classList.remove('hidden');
                        filteredTickets = filteredTickets.filter(ticket => ticket.status === parseInt(
                            statusSelect.value));
                    }

                    applyFilter(filteredTickets);
                });

                statusSelect.addEventListener('change', function() {
                    let filteredTickets = [...locationTickets];
                    filteredTickets = filteredTickets.filter(ticket => ticket.status === parseInt(
                        statusSelect.value));
                    applyFilter(filteredTickets);
                });

                applyFilter(locationTickets);
            }

            document.getElementById('close-details').onclick = function() {
                const ticketDetails = document.getElementById('ticket-details');
                ticketDetails.classList.add('opacity-0', 'hidden');
                ticketDetails.classList.remove('opacity-100');
            };


            function addMarkers(tickets) {
                markerGroup.clearLayers();
                if (heatLayer) map.removeLayer(heatLayer);

                let markers = {};
                let heatData = [];

                buttonsContainer.innerHTML = '';

                tickets.forEach(ticket => {
                    if (ticket.latitude && ticket.longitude) {
                        if (!markers[ticket.completename]) markers[ticket.completename] = [];
                        markers[ticket.completename].push(ticket);
                        heatData.push([ticket.latitude, ticket.longitude, 50]);
                    }
                });

                Object.keys(markers).forEach(location => {
                    const locationTickets = markers[location];
                    const marker = L.marker([locationTickets[0].latitude, locationTickets[0].longitude])
                        .bindPopup(`<b>${location}</b><br>Incidentes: ${locationTickets.length}`)
                        .on('click', () => updateTicketDetails(location, locationTickets));

                    marker.addTo(markerGroup);

                    const button = document.createElement('button');
                    button.classList.add('btn-custom');
                    button.innerText = location;

                    button.onclick = function() {
                        if (currentVisibleLocation === location) {
                            markerGroup.clearLayers();
                            Object.keys(markers).forEach(loc => {
                                const locTickets = markers[loc];
                                const locMarker = L.marker([locTickets[0].latitude, locTickets[
                                        0].longitude])
                                    .bindPopup(
                                        `<b>${loc}</b><br>Incidentes: ${locTickets.length}`)
                                    .on('click', () => updateTicketDetails(loc, locTickets));
                                locMarker.addTo(markerGroup);
                            });

                            currentVisibleLocation = null;
                            buttonsContainer.querySelectorAll('button').forEach(btn => btn.classList
                                .remove('btn-deselected'));
                        } else {
                            markerGroup.clearLayers();
                            const singleMarker = L.marker([locationTickets[0].latitude, locationTickets[
                                    0].longitude])
                                .bindPopup(
                                    `<b>${location}</b><br>Incidentes: ${locationTickets.length}`)
                                .on('click', () => updateTicketDetails(location, locationTickets));
                            singleMarker.addTo(markerGroup);

                            currentVisibleLocation = location;
                            buttonsContainer.querySelectorAll('button').forEach(btn => btn.classList
                                .add('btn-deselected'));
                            button.classList.remove('btn-deselected');
                        }
                    };

                    buttonsContainer.appendChild(button);
                });

                heatLayer = L.heatLayer(heatData, {
                    radius: 25,
                    blur: 15,
                    maxZoom: 17,
                    gradient: {
                        0.2: 'blue',
                        0.4: 'lime',
                        0.6: 'yellow',
                        0.8: 'orange',
                        1.0: 'red'
                    }
                }).addTo(map);
            }

            // Inicializa o mapa com todos os tickets
            addMarkers(tickets);

            // Ação para o botão de limpar filtros
            document.getElementById('clear-filters').addEventListener('click', () => {
                document.getElementById('start-date').value = '';
                document.getElementById('end-date').value = '';
                addMarkers(tickets); // Re-adiciona todos os tickets ao mapa
            });

            // Atualiza o mapa quando o botão de filtro é acionado
            document.getElementById('filter-form').addEventListener('submit', function(event) {
                event.preventDefault(); // Evita o reload da página

                const startDate = new Date(document.getElementById('start-date').value);
                const endDate = new Date(document.getElementById('end-date').value);

                let filteredTickets = tickets;

                if (!isNaN(startDate) && !isNaN(endDate)) {
                    filteredTickets = filteredTickets.filter(ticket => {
                        const ticketDate = new Date(ticket
                            .date_creation); // Use a data correta no ticket
                        return ticketDate >= startDate && ticketDate <= endDate;
                    });
                }

                console.log('Filtered Tickets:', filteredTickets); // Log para depuração
                addMarkers(filteredTickets);
            });
        });
    </script>
@endsection

@push('vite')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endpush
