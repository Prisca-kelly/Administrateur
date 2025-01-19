document.querySelectorAll('.menu-item').forEach(item => {
    item.addEventListener('click', function() {
        // Supprimer la classe 'active' de tous les éléments
        document.querySelectorAll('.menu-item').forEach(menu => {
            menu.classList.remove('active');
        });
        // Ajouter la classe 'active' à l'élément cliqué
        item.classList.add('active');
    });
});
