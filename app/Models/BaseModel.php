<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

class BaseModel extends Model
{
    use HasFactory;
    private $operatorAdvanced = ["in", "notIn", "between", "null"];
    //protected $debug = true;
    

    private function createListOfOperatorAdvanced()
    {
        $attributes = get_object_vars($this);
        dd($attributes);
        $this->operatorAdvanced = $attributes;
    }

    public function find($array = [], $with = [], $operators = [])
    {
        $this->createListOfOperatorAdvanced();
        $model = (new static)->newQuery();
        if (count($with) > 0)   $model->with($with);
        $this->createCondition($model, $array, $operators);
        return $model;
    }

    private function createCondition($model, $array = [], $operators = [])
    {
        foreach ($array as $key => $value) {
            $operator = get_array_value($operators, $key);
            if ($operator == null)  {
                $operator = "=";
                $model->where($key, $operator, $value);
            }
            else if (in_array($operator, $this->operatorAdvanced))  {
                $this->useOperatorAdvanced($model, $key, $operator, $value);
            }
            
        }
    }

    private function useOperatorAdvanced($model, $key, $operator, $value)
    {
        if ($operator == "in")                      $model->whereIn($key, $value);
        else if ($operator == "notIn")              $model->whereNotIn($key, $value);
        else if ($operator == "between")            $this->useBetween($model, $key, $value);
        else if ($operator == "null")               $model->whereNull($key);
    }

    public function useBetween($model, $key, $value) {
        $value = explode("-", $value);
        $model->whereBetween($key, to_date($value[0]), to_date($value[1]));
    }
}
