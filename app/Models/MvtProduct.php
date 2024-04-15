<?php

namespace App\Models;

use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MvtProduct extends Model
{
    use HasFactory;
    protected $fillable = ['unit_price', 'mvt_date', 'products_id', 'users_id', 'input_quantity', 'output_quantity'];
    public static $type = ['0', '1'];       //0 means purchase and 1 means consumable
    public static $_PURCHASE = '0';
    public static $_CONSUMPTION = '1';
    public static $_NORMAL = '1';
    public static $_WARNING = '2';
    public static $_DANGER = '3';

    public function user() 
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'products_id');
    }

    public function getStateAttribute()
    {
        if ($this->stock_quantity > $this->min_quantity)     return "OK";
        if ($this->stock_quantity <= 0)                      return "Rupture";
        if ($this->stock_quantity <= $this->min_quantity)    return "Avertissement";
    }

    public function getTotalAmountAttribute()
    {
        return $this->input_quantity * $this->unit_price;
    }

    public function getAmountAttribute()
    {
        return $this->input_quantity * $this->unit_price;
    }

    public static function totalAmountPurchase($purchases)
    {
        $totalAmount = 0;
        foreach($purchases as $purchase) 
        {
            $totalAmount += $purchase->input_quantity * $purchase->unit_price;
        }
        return $totalAmount;
    }

    public static function getPurchases($year=null, $month=null)
    {
        $purchases = MvtProduct::with(['product.category'])->whereDeleted(0)->where('input_quantity', '>', 0);
        if ($year) {
            $purchases->whereRaw("YEAR(mvt_date) = ?", $year);
        }
        if ($month) {
            $purchases->whereRaw("MONTH(mvt_date) = ?", $month);
        }
        return $purchases;
    }

    public static function getConsumptions($year=null, $month=null)
    {
        $consumptions = MvtProduct::with(['product.category'])->whereDeleted(0)->where('output_quantity', '>', 0);
        if ($year) {
            $consumptions->whereRaw("YEAR(mvt_date) = ?", $year);
        }
        if ($month) {
            $consumptions->whereRaw("MONTH(mvt_date) = ?", $month);
        }
        return $consumptions;
    }

    public static function saveOrUpdatePurchase($input)
    {
        $id = null;
        $stockProduct = MvtProduct::globality(null)->where('id', $input['products_id'])->first();
        if (isset($input['id'])) {
            $id = $input['id'];
        }  
        
        return MvtProduct::updateOrCreate([
            'id' => $id
        ], [
            'products_id' => $input['products_id'],
            'input_quantity' => $input['quantity'],
            'unit_price' => $input['unit_price'],
            'mvt_date' => $input['mvt_date'],
            'users_id' => auth()->user()->id
        ]);
    }

    public static function saveOrUpdateConsumption($input)
    {
        $id = null;
        $stockProduct = MvtProduct::globality(null)->where('id', $input['products_id'])->first();

        if (isset($input['id'])) {
            $id = $input['id'];
            $consumption = MvtProduct::getConsumptions()->where('id', $id)->first();
            if ($consumption->products_id != $input['products_id']) {
                if ($stockProduct->stock_quantity < floatval($input['quantity'])) {
                    throw new Exception('Quantitée restant: ' . $stockProduct->stock_quantity);
                }
            }
            else {
                if ($stockProduct->stock_quantity + $consumption->output_quantity < $input['quantity']) {
                    throw new Exception('Quantitée restant: ' . $stockProduct->stock_quantity);
                }
            }
        }  
        else {
            if ($stockProduct->stock_quantity < floatval($input['quantity'])) {
                throw new Exception('Quantitée restant: ' . $stockProduct->stock_quantity);
            }
        }
        
        return MvtProduct::updateOrCreate([
            'id' => $id
        ], [
            'products_id' => $input['products_id'],
            'output_quantity' => $input['quantity'],
            'unit_price' => 0,
            'mvt_date' => $input['mvt_date'],
            'users_id' => auth()->user()->id
        ]);
    }

    public static function globality($state)
    {
        $products_query = Product::query();
        $products = Product::with(['category'])->selectRaw('*')->from(
            $products_query->selectRaw('products.*, SUM(input_quantity - output_quantity) as stock_quantity')
            ->leftJoin('mvt_products', 'products.id', 'mvt_products.products_id')
            ->where('products.deleted', 0)
            ->where('mvt_products.deleted', 0)
            ->groupBy('products_id')
        );
        if ($state == self::$_NORMAL)   $products->whereColumn('stock_quantity', '>', 'min_quantity');
        else if ($state == self::$_DANGER) $products->where('stock_quantity', '<=', 0);
        else if ($state == self::$_WARNING) $products->whereColumn('stock_quantity', '<=', 'min_quantity');
        return $products;
    }

    public static function getPurchaseExpenditure($year=null)
    {
        //return [0, 0, 0, 0, 0, 0, 0, 104000, 207600, 189000, 104000, 0];

        if ($year == null)  $year = date('Y');
        $subQuery = MvtProduct::query();
        $purchases = MvtProduct::select('*')->from(
            $subQuery->selectRaw('SUM(unit_price * input_quantity) as expense, MONTH(mvt_date) as month, YEAR(mvt_date) as year')
                ->groupByRaw('MONTH(mvt_date), YEAR(mvt_date)')
        );
        $purchases->where('year', $year);
        $purchases = $purchases->get();
        $expenses = [];

        //Fill in the missing data
        for($i=1; $i<=12; $i++) {
            $j=1;
            if ($purchases->count() == 0)
                return [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            foreach($purchases as $purchase) {
                if ($purchase->month == $i) {
                    $expenses[] = floatval($purchase->expense);
                    break;
                }
                if ($j == $purchases->count())
                    $expenses[] = 0;
                $j++;
               
            }
        }
        return $expenses;
    }



    public static function getMvts($date, $type)
    {
        $mvt = MvtProduct::query();
        if ($type == self::$_PURCHASE)   $mvt->with(['product'])->where('input_quantity', '>', 0);
        else                             $mvt->with(['product'])->where('output_quantity', '>', 0);
        $mvt->whereDeleted(0);
        if ($date)
        {
            $date = DateTime::createFromFormat('Y-m-d', $date);
            $month = $date->format('m');
            $year = $date->format('Y');
            $mvt->whereRaw('MONTH(mvt_date) = ?', $month);
            $mvt->whereRaw('YEAR(mvt_date) = ?', $year);
        }
        $mvt->orderByDesc('mvt_date');
        return $mvt;
    }
    public static function buildFilterOfMvt($date, $type)
    {
        $mvt = MvtProduct::query();
        if ($type == self::$_PURCHASE)   $mvt->with(['product'])->where('input_quantity', '>', 0);
        else                             $mvt->with(['product'])->where('output_quantity', '>', 0);
        $mvt->whereDeleted(0);
        if ($date)
        {
            $date = DateTime::createFromFormat('Y-m-d', $date);
            $month = $date->format('m');
            $year = $date->format('Y');
            $mvt->whereRaw('MONTH(mvt_date) = ?', $month);
            $mvt->whereRaw('YEAR(mvt_date) = ?', $year);
        }
        $mvt->orderByDesc('mvt_date');
        return $mvt;
    }
}