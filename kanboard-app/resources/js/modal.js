// Gestion des modals
document.addEventListener('DOMContentLoaded', function () {
    // Récupération des éléments nécessaires
    const modal = document.getElementById('createProjectModal');
    const openBtn = document.querySelector('.btn'); // bouton qui ouvre la modal
    const cancelBtn = document.getElementById('cancelModalBtn'); // bouton pour fermer la modal

    // Vérifier que les éléments existent
    if (openBtn && cancelBtn && modal) {
        // Ouvrir la modal
        openBtn.addEventListener('click', () => {
            modal.classList.remove('hidden');
        });

        // Fermer la modal
        cancelBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        // Fermer la modal si on clique en dehors 
        modal.addEventListener('click', (e) => {
            // Si ce qu'on clique est bien l'arrière-plan, pas le contenu
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    }
});
