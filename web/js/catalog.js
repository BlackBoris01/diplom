document.addEventListener('DOMContentLoaded', function() {
   

    // Grid functionality
    function addFillerItems() {
        const grid = document.querySelector('.catalog-items-grid');
        if (!grid) return;

        const items = grid.querySelectorAll('.book-card');
        const itemsPerRow = getItemsPerRow();
        
        if (items.length === 0) return;

        // Удаляем существующие пустые элементы
        const existingFillers = grid.querySelectorAll('.filler-item');
        existingFillers.forEach(filler => filler.remove());

        // Вычисляем количество необходимых пустых элементов
        const remainder = items.length % itemsPerRow;
        if (remainder !== 0) {
            const fillersNeeded = itemsPerRow - remainder;
            for (let i = 0; i < fillersNeeded; i++) {
                const filler = document.createElement('div');
                filler.className = 'filler-item';
                grid.appendChild(filler);
            }
        }
    }

    function getItemsPerRow() {
        const width = window.innerWidth;
        if (width <= 411) return 1;
        if (width <= 1100) return 2;
        return 4;
    }

    // Вызываем функции
    addFillerItems();
    window.addEventListener('resize', addFillerItems);
}); 