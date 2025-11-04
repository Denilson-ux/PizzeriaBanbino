/**
 * Theme Toggle JavaScript
 * Maneja el cambio entre modo claro y oscuro
 * Persiste la preferencia en localStorage
 */

class ThemeToggle {
    constructor() {
        this.theme = localStorage.getItem('theme') || 'light';
        this.init();
    }

    init() {
        // Aplicar tema guardado al cargar la página
        this.applyTheme(this.theme);
        
        // Crear el botón de toggle si no existe
        this.createToggleButton();
        
        // Agregar event listeners
        this.addEventListeners();
    }

    createToggleButton() {
        // Verificar si ya existe el botón
        if (document.querySelector('.theme-toggle')) {
            return;
        }

        // Crear el botón de cambio de tema
        const toggleButton = document.createElement('button');
        toggleButton.className = 'theme-toggle';
        toggleButton.id = 'theme-toggle';
        toggleButton.title = 'Cambiar tema';
        toggleButton.setAttribute('aria-label', 'Cambiar entre modo claro y oscuro');
        
        // Ícono inicial basado en el tema actual
        const icon = this.theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        toggleButton.innerHTML = `<i class="${icon}"></i>`;
        
        // Insertar el botón en la navbar
        const navbar = document.querySelector('.main-header .navbar-nav');
        if (navbar) {
            const navItem = document.createElement('li');
            navItem.className = 'nav-item';
            navItem.appendChild(toggleButton);
            navbar.appendChild(navItem);
        } else {
            // Fallback: agregar al body si no se encuentra la navbar
            toggleButton.style.position = 'fixed';
            toggleButton.style.top = '20px';
            toggleButton.style.right = '20px';
            toggleButton.style.zIndex = '9999';
            document.body.appendChild(toggleButton);
        }
    }

    addEventListeners() {
        // Event listener para el botón de toggle
        document.addEventListener('click', (e) => {
            if (e.target.closest('.theme-toggle')) {
                this.toggleTheme();
            }
        });

        // Detectar cambios en la preferencia del sistema
        if (window.matchMedia) {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            mediaQuery.addListener((e) => {
                if (!localStorage.getItem('theme')) {
                    this.theme = e.matches ? 'dark' : 'light';
                    this.applyTheme(this.theme);
                }
            });
        }
    }

    toggleTheme() {
        this.theme = this.theme === 'light' ? 'dark' : 'light';
        this.applyTheme(this.theme);
        localStorage.setItem('theme', this.theme);
        
        // Disparar evento personalizado para que otros componentes puedan reaccionar
        const event = new CustomEvent('themeChanged', {
            detail: { theme: this.theme }
        });
        document.dispatchEvent(event);
    }

    applyTheme(theme) {
        // Aplicar el tema al documento
        document.documentElement.setAttribute('data-theme', theme);
        
        // Actualizar el ícono del botón
        const toggleButton = document.querySelector('.theme-toggle');
        if (toggleButton) {
            const icon = toggleButton.querySelector('i');
            if (icon) {
                icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
            }
            toggleButton.title = theme === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro';
        }

        // Aplicar clases adicionales para compatibilidad con AdminLTE
        document.body.classList.remove('dark-mode', 'light-mode');
        document.body.classList.add(`${theme}-mode`);
        
        // Mensaje de confirmación (opcional, se puede quitar)
        console.log(`Tema cambiado a: ${theme}`);
    }

    getCurrentTheme() {
        return this.theme;
    }

    setTheme(theme) {
        if (theme === 'light' || theme === 'dark') {
            this.theme = theme;
            this.applyTheme(theme);
            localStorage.setItem('theme', theme);
        }
    }
}

// Función para inicializar el theme toggle cuando el DOM esté listo
function initThemeToggle() {
    // Verificar si ya se inicializó
    if (window.themeToggle) {
        return;
    }

    window.themeToggle = new ThemeToggle();
}

// Inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initThemeToggle);
} else {
    initThemeToggle();
}

// Función global para uso manual
window.toggleTheme = function() {
    if (window.themeToggle) {
        window.themeToggle.toggleTheme();
    }
};

// Función global para establecer tema específico
window.setTheme = function(theme) {
    if (window.themeToggle) {
        window.themeToggle.setTheme(theme);
    }
};

// Función global para obtener tema actual
window.getCurrentTheme = function() {
    return window.themeToggle ? window.themeToggle.getCurrentTheme() : 'light';
};

// Exportar para uso como módulo (si es necesario)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ThemeToggle;
}