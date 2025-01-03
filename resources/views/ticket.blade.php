@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <div class="flex flex-col items-center">
            <div class="w-full bg-white shadow-md rounded-lg ">
                <div class="bg-gray-800 text-white py-4 px-6 text-lg font-semibold">
                    {{ __('Tickets') }}
                </div>
                <div class="p-6">
                    @if ($ticketId)
                        <iframe src="https://meta.k3gsolutions.com.br/public/dashboard/{{ $ticketId }}" frameborder="0"
                            height="1280" allowtransparency class="w-full h-150 rounded-md shadow-md mb-4 z-0"></iframe>
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

                function openModalWithDetails(ticket) {
                    const modal = document.getElementById('ticket-modal');
                    const modalDetails = document.getElementById('modal-ticket-details');

                    let modalContent = `
                       <p><b>ID:</b> ${ticket.id}</p>
                       <p><b>Name:</b> ${ticket.name}</p>
                       <p><b>Date:</b> ${ticket.date_creation}</p>
                       <p><b>Status:</b> ${statuses[ticket.status] || 'Unknown'}</p>
                       <p><b>Content:</b> ${ticket.content}</p>
                       <!-- Add more details as needed -->
                   `;

                    modalDetails.innerHTML = modalContent;
                    modal.classList.add('open');
                    modal.classList.remove('hidden');
                }

                function applyFilter(filteredTickets) {
                    let detailsHtml = `<b>${location}</b><br><br>`;
                    detailsHtml += `<b>Total de Tickets:</b> ${filteredTickets.length}<br><br>`;
                    detailsHtml += filteredTickets.map(ticket => {
                        const statusText = statuses[ticket.status] || 'Status desconhecido';
                        return `<b><a href="#" class="ticket-link" data-id="${ticket.id}">${ticket.id}.</a></b>
                           <a href="#" class="ticket-link" data-id="${ticket.id}">${ticket.name}</a>
                           (Status: ${statusText})`;
                    }).join('<br><br>');

                    if (filteredTickets.length === 0) {
                        detailsHtml += "Nenhum ticket encontrado com os critérios selecionados.";
                    }

                    ticketInfo.innerHTML = detailsHtml;

                    // Add click event listeners to each ticket link
                    document.querySelectorAll('.ticket-link').forEach(link => {
                        link.addEventListener('click', function(e) {
                            e.preventDefault();
                            const ticketId = this.getAttribute('data-id');
                            const ticket = filteredTickets.find(t => t.id === parseInt(ticketId));
                            if (ticket) {
                                openModalWithDetails(ticket);
                            }
                        });
                    });
                }

                // Initialize modal close event
                document.getElementById('close-modal').addEventListener('click', function() {
                    const modal = document.getElementById('ticket-modal');
                    modal.classList.remove('open');
                    modal.classList.add('hidden');
                });

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
                            // Re-add all markers
                            Object.keys(markers).forEach(loc => {
                                const locTickets = markers[loc];
                                const locMarker = L.marker([locTickets[0].latitude, locTickets[
                                        0].longitude])
                                    .bindPopup(
                                        `<b>${loc}</b><br>Incidentes: ${locTickets.length}`)
                                    .on('click', () => updateTicketDetails(loc,
                                        locTickets)); // Ensure updateTicketDetails is called
                                locMarker.addTo(markerGroup);
                            });

                            currentVisibleLocation = null;
                            buttonsContainer.querySelectorAll('button').forEach(btn => btn.classList
                                .remove('btn-deselected'));

                            // Hide the details if any location is deselected
                            const ticketDetails = document.getElementById('ticket-details');
                            ticketDetails.classList.add('opacity-0', 'hidden');
                            ticketDetails.classList.remove('opacity-100');

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

                            // Update details: Ensure ticket details are updated and visible
                            updateTicketDetails(location, locationTickets);
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
