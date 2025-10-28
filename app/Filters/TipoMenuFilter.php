<?php
namespace App\Filters;
use App\Filters\ApiFilter;

class TipoMenuFilter extends ApiFilter {

    //parametros para filtras los modelos (nombre tipo, etc)
    protected $safeParams = [ 
        'id_tipo_menu' => ['eq'],
        'nombre' => ['eq'],
        'estado' => ['eq'],
    ]; 

    //Mapear columnas, de como queremos que se filtre
    protected $columnMap = [
    
    ]; 

    //Mapeo de los operadores, ejemplo, eq es =, gt es >, etc
    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>='
    ]; 
}