{{--
    Componente Theme Toggle Button
    Botón para cambiar entre modo claro y oscuro
    
    Parámetros opcionales:
    - position: 'navbar' (default) | 'fixed' | 'inline'
    - size: 'sm' | 'md' (default) | 'lg'
    - class: clases CSS adicionales
--}}

@props([
    'position' => 'navbar',
    'size' => 'md',
    'class' => ''
])

@php
    $sizeClasses = [
        'sm' => 'width: 30px; height: 30px; font-size: 0.9rem;',
        'md' => 'width: 40px; height: 40px; font-size: 1.2rem;',
        'lg' => 'width: 50px; height: 50px; font-size: 1.4rem;'
    ];
    
    $positionClasses = [
        'navbar' => '',
        'fixed' => 'position: fixed; top: 20px; right: 20px; z-index: 9999;',
        'inline' => 'display: inline-flex; margin: 0 5px;'
    ];
@endphp

@if($position === 'navbar')
    <li class="nav-item">
        <button 
            class="theme-toggle nav-link {{ $class }}" 
            id="theme-toggle"
            title="Cambiar tema"
            aria-label="Cambiar entre modo claro y oscuro"
            style="{{ $sizeClasses[$size] }}"
        >
            <i class="fas fa-moon"></i>
        </button>
    </li>
@else
    <button 
        class="theme-toggle {{ $class }}" 
        id="theme-toggle"
        title="Cambiar tema"
        aria-label="Cambiar entre modo claro y oscuro"
        style="{{ $sizeClasses[$size] }} {{ $positionClasses[$position] }}"
    >
        <i class="fas fa-moon"></i>
    </button>
@endif

@push('styles')
<style>
    .theme-toggle {
        background: none;
        border: 1px solid var(--border-color, #dee2e6);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        color: var(--text-primary, #212529);
    }
    
    .theme-toggle:hover {
        background-color: var(--bg-tertiary, #e9ecef);
        transform: scale(1.1);
        text-decoration: none;
    }
    
    .theme-toggle i {
        transition: transform 0.3s ease;
    }
    
    .theme-toggle:hover i {
        transform: rotate(180deg);
    }
    
    /* Estilos específicos para navbar */
    .navbar .theme-toggle {
        margin: 0 10px;
    }
    
    /* Estilos para modo oscuro */
    [data-theme="dark"] .theme-toggle {
        border-color: var(--border-color, #404040);
        color: var(--text-primary, #ffffff);
    }
    
    [data-theme="dark"] .theme-toggle:hover {
        background-color: var(--bg-tertiary, #3c3c3f);
    }
</style>
@endpush

@push('scripts')
<script>
    // Asegurar que el tema se aplique inmediatamente al cargar la página
    (function() {
        const savedTheme = localStorage.getItem('theme');
        const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        const theme = savedTheme || systemTheme;
        
        document.documentElement.setAttribute('data-theme', theme);
        document.body.classList.add(`${theme}-mode`);
        
        // Actualizar el ícono del botón cuando esté disponible
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.querySelector('.theme-toggle');
            if (toggleButton) {
                const icon = toggleButton.querySelector('i');
                if (icon) {
                    icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                }
            }
        });
    })();
</script>
@endpush