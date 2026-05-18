<?php
    /**
     * @var string $root
     */
?>
<footer class="bg-dark text-secondary mt-auto">
    <div class="container py-5">
        <div class="d-grid mb-4">
            <a href="#" class="btn btn-dark btn-lg btn-block" style="height: 3rem">
                Powrót do góry
            </a>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <h3 class="text-white fw-bold">Amazonix</h3>
                <p class="small">
                    Twoje kompleksowe miejsce docelowe dla wysokiej jakości produktów w niesamowitych cenach.
                </p>
            </div>

            <div class="col-md-4">
                <h4 class="text-white fw-semibold">Poznaj Nas</h4>
                <ul class="list-unstyled small">
                    <li><a href='<?= htmlspecialchars($root) ?>/about-us' class="btn btn-link text-secondary hover:text-white">O nas</a></li>
                    <li><a href='<?= htmlspecialchars($root) ?>/careers' class="btn btn-link text-secondary hover:text-white">Kariery</a></li>
                    <li><a href='<?= htmlspecialchars($root) ?>/investor-relations' class="btn btn-link text-secondary hover:text-white">Relacje inwestorskie</a></li>
                    <li><a href='<?= htmlspecialchars($root) ?>/affiliate' class="btn btn-link text-secondary hover:text-white">Afiliacje</a></li>
                </ul>
            </div>

            <div class="col-md-4">
                <h4 class="text-white fw-semibold">Obsługa klienta</h4>
                <ul class="list-unstyled small">
                    <li><a href='<?= htmlspecialchars($root) ?>/help' class="btn btn-link text-secondary hover:text-white">Centrum Pomocy</a></li>
                    <li><a href='<?= htmlspecialchars($root) ?>/returns' class="btn btn-link text-secondary hover:text-white">Zwroty i Reklamacje</a></li>
                    <li><a href='<?=htmlspecialchars( $root) ?>/contact' class="btn btn-link text-secondary hover:text-white">Skontaktuj się z nami</a></li>
                </ul>
            </div>
        </div>

        <div class="border-top border-secondary pt-3">
            <div class="d-flex flex-column flex-md-row gap-3 small">
                <p class="w-100">© 2026 Amazonix. Wszelkie prawa zastrzeżone.</p>
                <div class="d-flex gap-3 flex-row-reverse">
                    <img
                            src="https://upload.wikimedia.org/wikipedia/commons/8/81/Visa_Brandmark_2021.svg"
                            alt="Visa" style="width: 5%" />
                    <img
                            src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg"
                            alt="Mastercard" style="width: 5%" />
                </div>
            </div>
        </div>
    </div>
</footer>