
function toggleFavorite(btn, productId) {
    fetch('/favorites/toggle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            const icon = btn.querySelector('i');
            if (result.isFavorite) {
                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill', 'text-danger');
            } else {
                icon.classList.remove('bi-heart-fill', 'text-danger');
                icon.classList.add('bi-heart');
            }
        }
    });
}
