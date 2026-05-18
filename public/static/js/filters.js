/**
 * Aktualizuje podwójny suwak zakresu cen.
 * Zapewnia, że wartość minimalna nie jest większa od maksymalnej,
 * oblicza pozycję wypełnienia paska i aktualizuje wyświetlane wartości.
 * 
 * @returns {void}
 */
function updateDualRange() {
    let min = parseInt(document.getElementById('priceMin').value);
    let max = parseInt(document.getElementById('priceMax').value);
    if (min > max) { [min, max] = [max, min]; }
    const pctMin = (min / 20000) * 100;
    const pctMax = (max / 20000) * 100;
    document.getElementById('rangeFill').style.left  = pctMin + '%';
    document.getElementById('rangeFill').style.width = (pctMax - pctMin) + '%';
    document.getElementById('minDisplay').value = min;
    document.getElementById('maxDisplay').value = max >= 20001 ? '20000+' : max;
}

// Inicjalizacja suwaka przy załadowaniu skryptu
updateDualRange();

/**
 * Filtruje listę marek na podstawie wpisanej frazy.
 * Ukrywa elementy, które nie pasują do zapytania.
 * 
 * @param {string} query - Fraza wyszukiwania
 * @returns {void}
 */
function filterBrands(query) {
    document.querySelectorAll('.brand-item').forEach(item => {
        item.style.display = item.dataset.name.includes(query.toLowerCase()) ? '' : 'none';
    });
}

/**
 * Czyści wszystkie filtry w formularzu i resetuje suwak cen.
 * 
 * @returns {void}
 */
function clearAll() {
    document.getElementById('filterForm').reset();
    updateDualRange();
}
