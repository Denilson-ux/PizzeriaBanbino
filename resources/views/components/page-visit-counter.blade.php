{{-- Componente contador de visitas integrado al diseño de la pizzería --}}
@php
    use App\Models\PageVisit;
    $currentRoute = request()->route()?->getName() ?? 'home';
    $visitCount = PageVisit::getVisitCount($currentRoute);
    $maxVisits = PageVisit::max('visit_count') ?? 1;
    $progressPercentage = min(100, ($visitCount / $maxVisits) * 100);
@endphp

<div class="visit-counter-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="visit-counter-card">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-center text-md-start">
                            <div class="visit-info">
                                <div class="visit-icon">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <div class="visit-details">
                                    <h6 class="visit-label">Visitas a esta página</h6>
                                    <div class="visit-count">{{ number_format($visitCount) }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="popularity-section">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="popularity-label">Popularidad relativa</span>
                                    <span class="popularity-percentage">{{ number_format($progressPercentage, 1) }}%</span>
                                </div>
                                <div class="custom-progress">
                                    <div class="progress-fill" style="width: {{ $progressPercentage }}%"></div>
                                </div>
                                <div class="update-time mt-2">
                                    <i class="fas fa-clock me-1"></i>
                                    Actualizado: {{ now()->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Estilos CSS integrados al diseño del sitio --}}
<style>
.visit-counter-section {
    background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
    padding: 25px 0;
    border-top: 3px solid #f7931e;
    position: relative;
    overflow: hidden;
}

.visit-counter-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(247,147,30,0.1)"/></svg>') repeat;
    opacity: 0.3;
    animation: float 20s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.visit-counter-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(247, 147, 30, 0.2);
    border-radius: 15px;
    padding: 20px 25px;
    position: relative;
    z-index: 2;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
}

.visit-counter-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(247, 147, 30, 0.2);
    border-color: rgba(247, 147, 30, 0.4);
}

.visit-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.visit-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #f7931e, #ff6b35);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 15px rgba(247, 147, 30, 0.3);
}

.visit-icon i {
    color: white;
    font-size: 20px;
}

.visit-details {
    flex: 1;
}

.visit-label {
    color: #ecf0f1;
    font-size: 14px;
    margin: 0 0 5px 0;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.visit-count {
    color: #f7931e;
    font-size: 28px;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.popularity-section {
    padding-left: 20px;
}

.popularity-label {
    color: #bdc3c7;
    font-size: 13px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.popularity-percentage {
    color: #f7931e;
    font-size: 14px;
    font-weight: 600;
}

.custom-progress {
    height: 8px;
    background: rgba(44, 62, 80, 0.6);
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #f7931e, #ff6b35, #e74c3c);
    border-radius: 10px;
    transition: width 1.5s ease-out;
    position: relative;
    overflow: hidden;
}

.progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

.update-time {
    color: #95a5a6;
    font-size: 12px;
    text-align: center;
    font-weight: 400;
}

.update-time i {
    color: #f7931e;
}

/* Responsividad mejorada */
@media (max-width: 768px) {
    .visit-counter-section {
        padding: 20px 0;
    }
    
    .visit-counter-card {
        padding: 15px 20px;
        margin: 0 15px;
    }
    
    .visit-info {
        justify-content: center;
        margin-bottom: 20px;
    }
    
    .popularity-section {
        padding-left: 0;
        text-align: center;
    }
    
    .visit-count {
        font-size: 24px;
    }
    
    .visit-icon {
        width: 45px;
        height: 45px;
    }
    
    .visit-icon i {
        font-size: 18px;
    }
}

@media (max-width: 480px) {
    .visit-counter-card {
        margin: 0 10px;
        padding: 15px;
    }
    
    .visit-info {
        gap: 10px;
    }
    
    .visit-count {
        font-size: 22px;
    }
}

/* Integración con el tema oscuro del sitio */
body.dark-theme .visit-counter-section,
.footer_section + .visit-counter-section {
    border-top-color: #f7931e;
}

/* Animación de entrada */
.visit-counter-section {
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Efecto hover en móviles */
@media (hover: none) {
    .visit-counter-card:active {
        transform: scale(0.98);
    }
}
</style>