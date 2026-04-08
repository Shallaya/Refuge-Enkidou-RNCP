import './stimulus_bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import './styles/navbar.css';
import './styles/pet-type.css';
import './styles/home.css';
import './styles/form.css';

// ========================
// SEARCH TOGGLE
// ========================

function initNavbarSearch() {
    const searchToggle = document.getElementById('searchToggle');
    const searchBar = document.getElementById('searchBar');
    const searchInput = document.querySelector('#searchBar .search-input');
    const searchForm = document.querySelector('#searchBar form');
    const navbarToggle = document.getElementById('navbarToggle');

    // Vérifie que les éléments nécessaires à la fonctionnalité de recherche sont présents sur la page pour éviter les erreurs JavaScript si certains éléments sont absents, notamment lors de la navigation entre différentes pages qui peuvent ne pas tous contenir ces éléments
    if (!searchToggle || !searchBar) {
        return;
    }

    // Empêche l'initialisation multiple du même code si la fonction est appelée plusieurs fois, notamment lors de la navigation avec Turbo Drive qui recharge partiellement la page sans la recharger complètement, ce qui pourrait entraîner des comportements inattendus si les événements sont attachés plusieurs fois au même élément
    if (searchToggle.dataset.searchInit === 'true') {
        return;
    }

    searchToggle.dataset.searchInit = 'true';

    // Permet d'ouvrir et de fermer la barre de recherche lorsque l'utilisateur clique sur l'icône de recherche
    searchToggle.addEventListener('click', (e) => {
        e.preventDefault();
        searchBar.classList.toggle('active');

        if (searchBar.classList.contains('active') && searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    });

    // Ferme la barre de recherche si l'utilisateur clique en dehors d'elle pour éviter qu'elle ne reste ouverte inutilement
    document.addEventListener('click', (e) => {
        if (!searchBar.contains(e.target) && !searchToggle.contains(e.target)) {
            searchBar.classList.remove('active');
        }
    });

    // Ferme la barre de recherche après la soumission pour éviter qu'elle ne reste ouverte inutilement
    if (searchForm) {
        searchForm.addEventListener('submit', () => {
            searchBar.classList.remove('active');
        });
    }

    // Ferme la barre de recherche si le menu de navigation est ouvert pour éviter les conflits d'affichage
    if (navbarToggle) {
        navbarToggle.addEventListener('click', () => {
            searchBar.classList.remove('active');
        });
    }

    // nettoie le champ de recherche après une soumission pour éviter que la valeur ne persiste lors du retour en arrière
    if (searchInput) {
        searchInput.value = '';
    }
}

document.addEventListener('DOMContentLoaded', initNavbarSearch);
document.addEventListener('turbo:load', initNavbarSearch);
