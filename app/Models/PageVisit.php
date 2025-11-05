<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PageVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_name',
        'page_url',
        'visit_count',
        'last_visit'
    ];

    protected $dates = [
        'last_visit',
        'created_at',
        'updated_at'
    ];

    /**
     * Incrementar contador de visitas para una página específica
     * 
     * @param string $pageName
     * @param string $pageUrl
     * @return PageVisit
     */
    public static function incrementVisit(string $pageName, string $pageUrl): PageVisit
    {
        $visit = self::firstOrCreate(
            ['page_name' => $pageName],
            [
                'page_url' => $pageUrl,
                'visit_count' => 0,
                'last_visit' => now()
            ]
        );

        $visit->increment('visit_count');
        $visit->update(['last_visit' => now()]);

        return $visit->fresh();
    }

    /**
     * Obtener el contador de visitas de una página
     * 
     * @param string $pageName
     * @return int
     */
    public static function getVisitCount(string $pageName): int
    {
        $visit = self::where('page_name', $pageName)->first();
        return $visit ? $visit->visit_count : 0;
    }

    /**
     * Obtener todas las páginas con sus contadores ordenadas por visitas
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTopPages(int $limit = 10)
    {
        return self::orderBy('visit_count', 'desc')
                  ->limit($limit)
                  ->get();
    }

    /**
     * Formatear el número de visitas
     * 
     * @return string
     */
    public function getFormattedVisitCountAttribute(): string
    {
        if ($this->visit_count >= 1000000) {
            return number_format($this->visit_count / 1000000, 1) . 'M';
        } elseif ($this->visit_count >= 1000) {
            return number_format($this->visit_count / 1000, 1) . 'K';
        }
        
        return number_format($this->visit_count);
    }
}