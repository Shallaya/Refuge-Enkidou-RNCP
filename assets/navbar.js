// public/js/navbar.js

/**
 * Gestion de la navigation mobile-first
 * Pour votre site e-commerce écoresponsable
 */

// Variable globale pour savoir si la navbar est déjà initialisée
let navbarInitialized = false;
let toggleHandler = null;
let clickOutsideHandler = null;
let resizeHandler = null;

// Fonction d'initialisation
function initNavbar() {
    
    // ========================================
    // SÉLECTION DES ÉLÉMENTS
    // ========================================
    const navbarToggle = document.getElementById('navbarToggle');
    const navbarMenu = document.getElementById('navbarMenu');
    const dropdowns = document.querySelectorAll('.dropdown');
    const navbarLinks = document.querySelectorAll('.navbar-link:not(.dropdown-toggle)');
    
    // Vérifier que les éléments existent
    if (!navbarToggle || !navbarMenu) {
        console.warn('Navbar elements not found');
        return;
    }
    
    // ========================================
    // NETTOYER LES ANCIENS EVENT LISTENERS SI DÉJÀ INITIALISÉ
    // ========================================
    if (navbarInitialized) {
        if (toggleHandler) {
            navbarToggle.removeEventListener('click', toggleHandler);
        }
        if (clickOutsideHandler) {
            document.removeEventListener('click', clickOutsideHandler);
        }
        if (resizeHandler) {
            window.removeEventListener('resize', resizeHandler);
        }
    }
    
    // ========================================
    // TOGGLE MENU HAMBURGER (MOBILE)
    // ========================================
    toggleHandler = function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Toggle des classes "active"
        navbarToggle.classList.toggle('active');
        navbarMenu.classList.toggle('active');
        
        // Empêcher le scroll du body quand le menu est ouvert
        document.body.style.overflow = navbarMenu.classList.contains('active') ? 'hidden' : '';
    };
    
    navbarToggle.addEventListener('click', toggleHandler);
    
    // ========================================
    // GESTION DES DROPDOWNS
    // ========================================
    dropdowns.forEach(function(dropdown) {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        
        if (toggle) {
            // Créer le handler pour ce dropdown
            const dropdownHandler = function(e) {
                // Sur mobile uniquement (moins de 768px)
                if (window.innerWidth < 768) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Ferme les autres dropdowns
                    dropdowns.forEach(function(otherDropdown) {
                        if (otherDropdown !== dropdown) {
                            otherDropdown.classList.remove('active');
                        }
                    });
                    
                    // Toggle le dropdown actuel
                    dropdown.classList.toggle('active');
                }
            };
            
            // Supprimer l'ancien listener si existe
            toggle.removeEventListener('click', toggle._dropdownHandler);
            // Ajouter le nouveau
            toggle.addEventListener('click', dropdownHandler);
            // Stocker la référence pour pouvoir le supprimer plus tard
            toggle._dropdownHandler = dropdownHandler;
        }
    });
    
    // ========================================
    // FERMER LE MENU EN CLIQUANT SUR UN LIEN
    // ========================================
    navbarLinks.forEach(function(link) {
        const linkHandler = function() {
            if (window.innerWidth < 768) {
                navbarToggle.classList.remove('active');
                navbarMenu.classList.remove('active');
                document.body.style.overflow = '';
            }
        };
        
        // Supprimer l'ancien listener si existe
        link.removeEventListener('click', link._linkHandler);
        // Ajouter le nouveau
        link.addEventListener('click', linkHandler);
        // Stocker la référence
        link._linkHandler = linkHandler;
    });
    
    // ========================================
    // FERMER LE MENU EN CLIQUANT EN DEHORS
    // ========================================
    clickOutsideHandler = function(e) {
        if (!navbarMenu.contains(e.target) && !navbarToggle.contains(e.target)) {
            navbarToggle.classList.remove('active');
            navbarMenu.classList.remove('active');
            document.body.style.overflow = '';
        }
    };
    
    document.addEventListener('click', clickOutsideHandler);
    
    // ========================================
    // GESTION DU RESIZE (MOBILE <-> DESKTOP)
    // ========================================
    resizeHandler = function() {
        if (window.innerWidth >= 768) {
            navbarToggle.classList.remove('active');
            navbarMenu.classList.remove('active');
            document.body.style.overflow = '';
            
            dropdowns.forEach(function(dropdown) {
                dropdown.classList.remove('active');
            });
        }
    };
    
    window.addEventListener('resize', resizeHandler);
    
    // ========================================
    // GESTION DU HOVER POUR LES DROPDOWNS (DESKTOP)
    // ========================================
    dropdowns.forEach(function(dropdown) {
        const mouseEnterHandler = function() {
            if (window.innerWidth >= 768) {
                dropdown.classList.add('active');
            }
        };
        
        const mouseLeaveHandler = function() {
            if (window.innerWidth >= 768) {
                dropdown.classList.remove('active');
            }
        };
        
        // Supprimer les anciens listeners si existent
        dropdown.removeEventListener('mouseenter', dropdown._mouseEnterHandler);
        dropdown.removeEventListener('mouseleave', dropdown._mouseLeaveHandler);
        
        // Ajouter les nouveaux
        dropdown.addEventListener('mouseenter', mouseEnterHandler);
        dropdown.addEventListener('mouseleave', mouseLeaveHandler);
        
        // Stocker les références
        dropdown._mouseEnterHandler = mouseEnterHandler;
        dropdown._mouseLeaveHandler = mouseLeaveHandler;
    });
    
    // Marquer comme initialisé
    navbarInitialized = true;
    console.log('Navbar initialized successfully');
}

// ========================================
// INITIALISATION AU CHARGEMENT DE LA PAGE
// ========================================
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNavbar);
} else {
    // Le DOM est déjà chargé
    initNavbar();
}

// ========================================
// RÉINITIALISATION SI NAVIGATION AVEC TURBO/AJAX
// ========================================
// Pour Symfony UX Turbo (si vous l'utilisez)
document.addEventListener('turbo:load', initNavbar);
document.addEventListener('turbo:render', initNavbar);

// Pour d'autres systèmes de navigation AJAX
window.addEventListener('load', initNavbar);
