{{-- Componente contador de visitas con Bootstrap --}}
@php
    use App\Models\PageVisit;
    $currentRoute = request()->route()?->getName() ?? 'home';
    $visitCount = PageVisit::getVisitCount($currentRoute);
@endphp

<div class="page-visit-counter bg-light border-top py-3 mt-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center text-muted">
                    <i class="fas fa-eye me-2" style="font-size: 1.1rem;"></i>
                    <span class="fw-medium">Visitas a esta página:</span>
                    <span class="badge bg-primary ms-2 fs-6">{{ number_format($visitCount) }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-md-end text-start mt-2 mt-md-0">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Actualizado: {{ now()->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
        
        {{-- Barra de progreso opcional para mostrar popularidad relativa --}}
        @if($visitCount > 0)
            @php
                // Obtener el máximo de visitas para calcular porcentaje
                $maxVisits = PageVisit::max('visit_count') ?? 1;
                $progressPercentage = min(100, ($visitCount / $maxVisits) * 100);
            @endphp
            
            <div class="mt-2">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted">Popularidad relativa</small>
                    <small class="text-muted">{{ number_format($progressPercentage, 1) }}%</small>
                </div>
                <div class="progress" style="height: 4px;">
                    <div class="progress-bar bg-success" 
                         role="progressbar" 
                         style="width: {{ $progressPercentage }}%" 
                         aria-valuenow="{{ $progressPercentage }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Estilos CSS adicionales --}}
<style>
.page-visit-counter {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-top: 2px solid #dee2e6;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
}

.page-visit-counter .badge {
    font-size: 0.85rem;
    padding: 0.4rem 0.8rem;
    border-radius: 0.5rem;
}

.page-visit-counter .progress {
    background-color: rgba(0,0,0,0.1);
    border-radius: 2px;
}

.page-visit-counter .progress-bar {
    transition: width 0.3s ease;
}

@media (max-width: 768px) {
    .page-visit-counter .col-md-4 {
        text-align: center !important;
    }
}
</style>