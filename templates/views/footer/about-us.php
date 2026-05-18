<?php
    /**
     * @var View $view
     */

    use App\View\View;

?>
<?php $view->render('partials/header', ['params' => $params ?? null]); ?>

<main>
    <section class="bg-light py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4">Kim jesteśmy?</h1>
                    <p class="lead mt-3">Tworzymy miejsce, gdzie znajdziesz produkty, które zmienią Twoje codzienne życie.</p>
                    <p>Od samego początku stawiamy na transparentność, uczciwość i indywidualne podejście do każdego klienta. Nie jesteśmy tylko sklepem – jesteśmy społecznością.</p>
                </div>
                <div class="col-lg-6">
                    <img src="" alt="#" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Nasze wartości</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 text-center border-0 shadow-sm   hover-card">
                        <div class="card-body">
                            <h3>⭐</h3>
                            <h5 class="card-title">Jakość</h5>
                            <p class="card-text">Tylko sprawdzone produkty od   zaufanych dostawców.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 text-center border-0 shadow-sm   hover-card">
                        <div class="card-body">
                            <h3>🤝</h3>
                            <h5 class="card-title">Zaufanie</h5>
                            <p class="card-text">Twoje zadowolenie jest dla nas     najważniejsze.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 text-center border-0 shadow-sm   hover-card">
                        <div class="card-body">
                            <h3>🚀</h3>
                            <h5 class="card-title">Innowacja</h5>
                            <p class="card-text">Stale się rozwijamy i szukamy  nowych rozwiązań.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--MAPA -->

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

     <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">📍 Znajdź nas</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <div class="card-body p-0">
                            <div id="map" style="height: 400px; width: 100%; border-radius: 8px 8px 0 0;">xd</div>
                            <div class="p-4 text-center">
                                <h5>Zespół Szkół Technicznych</h5>
                                <p class="mb-0">ul. Kazimiera Jagiellończyka 3, 39-300 Mielec</p>
                                <a href="https://www.google.com/maps/dir/?api=1&destination=Zespół+Szkół+Technicznych+Mielec" target="_blank" class="btn btn-outline-primary mt-3">
                                    🧭 Pokaż trasę
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-dark text-white py-5">
        <div class="container text-center">
            <h2>Masz pytania?</h2>
            <p class="lead">Skontaktuj się z nami – chętnie pomożemy!</p>
            <a href="#" class="btn btn-light btn-lg mt-3">Napisz do nas</a>
        </div>
    </section>
</main>




<script>
document.addEventListener("DOMContentLoaded", function() {

    const location = [50.28926422885905, 21.43464343385036];

    const map = L.map('map').setView(location, 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const marker = L.marker(location).addTo(map);

    marker.bindPopup('<strong>Zespół Szkół Technicznych</strong><br>Siedziba').openPopup();

});
</script>

<?php $view->render('partials/footer'); ?>