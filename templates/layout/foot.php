    <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
            crossorigin="anonymous">
    </script>
    <script>
        document.querySelectorAll('a[href]').forEach(link => {
            if (link.hostname !== window.location.hostname) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const confirmed = confirm('Zaraz opuścisz tą stronę. Czy na pewno chcesz to zrobić?');
                    if (confirmed) {
                        window.location.href = this.href;
                    }
                });
            }
        });
        function toggleFavorite(btn, productId, userId) {
            const domain = `<?= $_SERVER["SERVER_NAME"] !== 'localhost' ? "api." : ""?>${window.location.hostname.split('.').slice(-2).join('.')}`;
            const apiUrl = `${window.location.protocol}//${domain}<?= $_SERVER["SERVER_NAME"] === 'localhost' ? ":$_ENV[API_PORT]" : "" ?>/api/favorites`;
            
            fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json' ,
                    <?php if (isset($_SESSION['JWT_TOKEN'])): ?>
                    'Authorization': 'Bearer <?= $_SESSION['JWT_TOKEN'] ?>'
                    <?php endif;?>
                },
                body: JSON.stringify({ product_id: productId, user_id: userId })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    try {
                        const icon = btn.querySelector('i');
                        if (result.isFavorite) {
                            icon.classList.remove('bi-heart');
                            icon.classList.add('bi-heart-fill', 'text-danger');
                        } else {
                            icon.classList.remove('bi-heart-fill', 'text-danger');
                            icon.classList.add('bi-heart');
                        }
                    }
                    catch (e) { }
                }
            });
        }
    </script>
    <?php
/** @var array $scripts */
    foreach ($scripts as $src) : ?>
    <script src="<?=  htmlspecialchars($root . "/" . $src, ENT_QUOTES, 'UTF-8') ?>"></script>
    <?php endforeach; ?>
</body>
</html>
