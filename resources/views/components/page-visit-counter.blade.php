{{-- Componente contador de visitas - Diseño elegante integrado --}}
@php
    use App\Models\PageVisit;
    $currentRoute = request()->route()?->getName() ?? 'home';
    $visitCount = PageVisit::getVisitCount($currentRoute);
    $maxVisits = PageVisit::max('visit_count') ?? 1;
    $progressPercentage = min(100, ($visitCount / $maxVisits) * 100);
@endphp

<section class="visit-stats-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="stats-wrapper">
                    <div class="row align-items-center g-0">
                        <!-- Icono y contador principal -->
                        <div class="col-md-5">
                            <div class="visit-main-info">
                                <div class="visit-icon-container">
                                    <div class="visit-icon-bg">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="visit-pulse"></div>
                                </div>
                                <div class="visit-text">
                                    <h4 class="visit-title">Estadísticas de Visitas</h4>
                                    <div class="visit-number">{{ number_format($visitCount) }}</div>
                                    <p class="visit-subtitle">visitas a esta página</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Separador vertical -->
                        <div class="col-md-2 text-center d-none d-md-block">
                            <div class="vertical-separator"></div>
                        </div>
                        
                        <!-- Barra de progreso y detalles -->
                        <div class="col-md-5">
                            <div class="progress-info">
                                <div class="progress-header">
                                    <span class="progress-label">Popularidad</span>
                                    <span class="progress-value">{{ number_format($progressPercentage, 1) }}%</span>
                                </div>
                                
                                <div class="modern-progress">
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: {{ $progressPercentage }}%">
                                            <div class="progress-glow"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="last-updated">
                                    <i class="fas fa-clock"></i>
                                    <span>Actualizado: {{ now()->format('H:i') }} hrs</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Decorative elements -->
    <div class="decoration-left"></div>
    <div class="decoration-right"></div>
</section>

<style>
/* Sección principal del contador */
.visit-stats-section {
    background: linear-gradient(135deg, #1a252f 0%, #2c3e50 50%, #34495e 100%);
    padding: 30px 0;
    position: relative;
    overflow: hidden;
    border-top: 2px solid #f7931e;
    box-shadow: 0 -5px 20px rgba(0,0,0,0.1);
}

/* Elementos decorativos */
.decoration-left,
.decoration-right {
    position: absolute;
    top: 0;
    width: 100px;
    height: 100%;
    opacity: 0.1;
    background: linear-gradient(45deg, #f7931e, #ff6b35);
}

.decoration-left {
    left: -50px;
    transform: skewX(-15deg);
}

.decoration-right {
    right: -50px;
    transform: skewX(15deg);
}

/* Contenedor principal de estadísticas */
.stats-wrapper {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(247, 147, 30, 0.2);
    border-radius: 20px;
    padding: 25px 30px;
    position: relative;
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.stats-wrapper:hover {
    transform: translateY(-3px);
    box-shadow: 
        0 15px 40px rgba(247, 147, 30, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
    border-color: rgba(247, 147, 30, 0.4);
}

/* Información principal de visitas */
.visit-main-info {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 10px 0;
}

/* Contenedor del icono */
.visit-icon-container {
    position: relative;
    flex-shrink: 0;
}

.visit-icon-bg {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #f7931e 0%, #e74c3c 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    z-index: 2;
    box-shadow: 
        0 10px 20px rgba(247, 147, 30, 0.3),
        inset 0 2px 4px rgba(255, 255, 255, 0.2);
}

.visit-icon-bg i {
    color: white;
    font-size: 24px;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
}

/* Efecto de pulso */
.visit-pulse {
    position: absolute;
    top: 0;
    left: 0;
    width: 70px;
    height: 70px;
    border: 2px solid #f7931e;
    border-radius: 50%;
    animation: pulse 2s infinite;
    opacity: 0.6;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 0.6;
    }
    50% {
        transform: scale(1.1);
        opacity: 0.3;
    }
    100% {
        transform: scale(1.2);
        opacity: 0;
    }
}

/* Texto de visitas */
.visit-text {
    flex: 1;
}

.visit-title {
    color: #ecf0f1;
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 8px 0;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.visit-number {
    color: #f7931e;
    font-size: 36px;
    font-weight: 800;
    line-height: 1;
    margin: 5px 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    background: linear-gradient(135deg, #f7931e, #ff6b35);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.visit-subtitle {
    color: #bdc3c7;
    font-size: 14px;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
}

/* Separador vertical */
.vertical-separator {
    width: 2px;
    height: 60px;
    background: linear-gradient(to bottom, transparent, #f7931e, transparent);
    margin: 0 auto;
    opacity: 0.6;
}

/* Información de progreso */
.progress-info {
    padding: 10px 0;
}

.progress-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 15px;
}

.progress-label {
    color: #ecf0f1;
    font-size: 16px;
    font-weight: 600;
    flex: 1;
}

.progress-value {
    color: #f7931e;
    font-size: 18px;
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

/* Barra de progreso moderna */
.modern-progress {
    margin: 15px 0;
}

.progress-track {
    height: 10px;
    background: rgba(44, 62, 80, 0.8);
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #f7931e 0%, #e74c3c 50%, #ff6b35 100%);
    border-radius: 10px;
    transition: width 2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.progress-glow {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, 
        transparent, 
        rgba(255, 255, 255, 0.4), 
        transparent
    );
    animation: slide 3s infinite;
}

@keyframes slide {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Última actualización */
.last-updated {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #95a5a6;
    font-size: 13px;
    margin-top: 12px;
    justify-content: center;
}

.last-updated i {
    color: #f7931e;
    font-size: 12px;
}

/* Responsividad */
@media (max-width: 768px) {
    .visit-stats-section {
        padding: 25px 0;
    }
    
    .stats-wrapper {
        padding: 20px;
        margin: 0 15px;
    }
    
    .visit-main-info {
        flex-direction: column;
        text-align: center;
        gap: 15px;
        margin-bottom: 25px;
    }
    
    .visit-icon-bg {
        width: 60px;
        height: 60px;
    }
    
    .visit-icon-bg i {
        font-size: 20px;
    }
    
    .visit-pulse {
        width: 60px;
        height: 60px;
    }
    
    .visit-number {
        font-size: 32px;
    }
    
    .visit-title {
        font-size: 16px;
    }
    
    .progress-info {
        text-align: center;
    }
}

@media (max-width: 480px) {
    .stats-wrapper {
        padding: 15px;
        margin: 0 10px;
    }
    
    .visit-number {
        font-size: 28px;
    }
    
    .visit-icon-bg {
        width: 55px;
        height: 55px;
    }
    
    .visit-pulse {
        width: 55px;
        height: 55px;
    }
}

/* Animación de entrada */
.visit-stats-section {
    animation: fadeInUp 0.8s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Efectos hover adicionales */
.stats-wrapper:hover .visit-icon-bg {
    transform: scale(1.05);
    box-shadow: 
        0 15px 30px rgba(247, 147, 30, 0.4),
        inset 0 2px 4px rgba(255, 255, 255, 0.3);
}

.stats-wrapper:hover .progress-fill {
    box-shadow: 0 0 20px rgba(247, 147, 30, 0.5);
}
</style>