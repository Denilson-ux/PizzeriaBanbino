<?php
namespace App\Filters;
use Illuminate\Http\Request;

class ApiFilter {

    protected $safeParams = []; //parametros para filtras los modelos (nombre tipo, etc)
    protected $columnMap = [];  //Mapear columnas, de como queremos que se filtre
    protected $operatorMap = []; //Mapeo de los operadores, ejemplo, eq es =, gt es >, etc

    public function transform(Request $request) {
        $eloQuery = [];
        foreach ($this->safeParams as $parm => $operators) {
            $query = $request->query($parm);
            if (!isset($query)) {
                continue;
            }
            $column = $this->columnMap[$parm] ?? $parm;
            foreach ($operators as $operator) {
                if (isset($query[$operator])) {
                    $eloQuery[] = [$column, $this->operatorMap[$operator], $query[$operator]];
                }
            }
        }
        return $eloQuery;
    }
}