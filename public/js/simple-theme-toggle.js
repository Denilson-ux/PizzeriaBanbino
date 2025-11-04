/**
 * Modo Oscuro/Claro Simple - Solo Bootstrap
 * No requiere compilación npm - Funciona directamente
 */

(function() {
    'use strict';
    
    // Obtener tema guardado o usar el del sistema
    function getStoredTheme() {
        return localStorage.getItem('theme');
    }
    
    function getPreferredTheme() {
        const storedTheme = getStoredTheme();
        if (storedTheme) {
            return storedTheme;
        }
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }
    
    // Aplicar tema
    function setTheme(theme) {
        if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        } else {
            document.documentElement.setAttribute('data-bs-theme', theme);
        }
        
        // Actualizar icono del botón
        updateToggleButton(theme);
    }
    
    // Actualizar icono del botón
    function updateToggleButton(theme) {
        const toggleBtn = document.querySelector('#theme-toggle');
        if (!toggleBtn) return;
        
        const icon = toggleBtn.querySelector('i');
        const currentTheme = document.documentElement.getAttribute('data-bs-theme');
        
        if (icon) {
            if (currentTheme === 'dark') {
                icon.className = 'fas fa-sun';
                toggleBtn.title = 'Cambiar a modo claro';
            } else {
                icon.className = 'fas fa-moon';
                toggleBtn.title = 'Cambiar a modo oscuro';
            }
        }
    }
    
    // Crear botón de toggle
    function createToggleButton() {
        // Buscar donde insertar el botón
        const navbar = document.querySelector('.navbar .navbar-nav');
        if (!navbar) return;
        
        // Crear elemento del botón
        const navItem = document.createElement('li');
        navItem.className = 'nav-item';
        
        const button = document.createElement('button');
        button.id = 'theme-toggle';
        button.className = 'theme-toggle-btn nav-link';
        button.type = 'button';
        button.setAttribute('aria-label', 'Cambiar tema');
        
        const icon = document.createElement('i');
        icon.className = 'fas fa-moon';
        
        button.appendChild(icon);
        navItem.appendChild(button);
        navbar.appendChild(navItem);
        
        // Agregar event listener
        button.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            localStorage.setItem('theme', newTheme);
            setTheme(newTheme);
            
            // Mostrar mensaje opcional (puedes quitar esta línea)
            console.log('Tema cambiado a:', newTheme);
        });
    }
    
    // Inicializar cuando el DOM esté listo
    function init() {
        // Aplicar tema inmediatamente
        const theme = getPreferredTheme();
        setTheme(theme);
        
        // Crear botón cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                createToggleButton();
                updateToggleButton(theme);
            });
        } else {
            createToggleButton();
            updateToggleButton(theme);
        }
        
        // Escuchar cambios en las preferencias del sistema
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
            const storedTheme = getStoredTheme();
            if (storedTheme !== 'light' && storedTheme !== 'dark') {
                setTheme(getPreferredTheme());
            }
        });
    }
    
    // Funciones globales para uso manual
    window.toggleTheme = function() {
        const currentTheme = document.documentElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        localStorage.setItem('theme', newTheme);
        setTheme(newTheme);
    };
    
    window.setThemeMode = function(theme) {
        if (theme === 'light' || theme === 'dark') {
            localStorage.setItem('theme', theme);
            setTheme(theme);
        }
    };
    
    window.getCurrentTheme = function() {
        return document.documentElement.getAttribute('data-bs-theme') || 'light';
    };
    
    // Inicializar
    init();
})();