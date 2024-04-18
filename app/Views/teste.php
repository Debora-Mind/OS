<!DOCTYPE html>
<html>
<head>
    <title>Mapa de Lojas</title>
    <!-- Incluir folhas de estilo do Leaflet.js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        /* Estilo para o mapa */
        #map { height: 400px; }
    </style>
</head>
<body>

<!-- Div para o mapa -->
<div id="map"></div>

<!-- Incluir script do Leaflet.js -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    // Criar um mapa
    var map = L.map('map').setView([-15.788497, -47.879873], 10);

    // Adicionar camada de mapa do OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Adicionar marcadores personalizados para as lojas
    var lojas = [
        {"name": "<a href='https://www.google.com.br/maps/place/Museu+de+Ci%C3%AAncia+e+Tecnologia/@-30.015488,-51.1901696,12z/data=!4m6!3m5!1s0x951977fec3400945:0xfa424b0ca6868f88!8m2!3d-30.0585523!4d-51.1759372!16s%2Fm%2F04ycyk_?entry=ttu' target='_blank'>Loja 1</a>", "description": "Descrição da Loja 1", "location": [-15.799354, -47.864155], "iconUrl": "<?= site_url('recursos/img/posto.png') ?>"},
        {"name": "Loja 2", "description": "Descrição da Loja 2", "location": [-15.815344, -47.889769], "iconUrl": "<?= site_url('recursos/img/posto.png') ?>"},
        {"name": "Loja 3", "description": "Descrição da Loja 3", "location": [-15.820001, -47.925143], "iconUrl": "<?= site_url('recursos/img/posto.png') ?>"}
    ];

    lojas.forEach(function(loja) {
        // Criar ícone personalizado
        var customIcon = L.icon({
            iconUrl: loja.iconUrl,
            iconSize: [32, 32], // Tamanho do ícone
            iconAnchor: [16, 32], // Posição do ícone
            popupAnchor: [0, -32] // Posição do popup
        });

        // Criar conteúdo do popup com nome e descrição da loja
        var popupContent = "<b>" + loja.name + "</b><br>" + loja.description;

        // Adicionar marcador com ícone personalizado e popup personalizado
        L.marker(loja.location, {icon: customIcon}).bindPopup(popupContent).addTo(map);
    });
</script>

</body>
</html>
