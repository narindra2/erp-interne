<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    //
    public function createProductInPurchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'min_quantity' => ['required', 'numeric', 'min:0'],
            'categories_id' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all()
            ]);
        }
        $product = Product::create($request->input());
        return response()->json(['success' => 'OK', 'product' => $product]);
    }

    public function index()
    {
        $data = [];
        $data['title'] = 'Produits';
        $data['categories'] = Category::whereDeleted(0)->get();
        return view('products.index', $data);
    }

    public function getProductDataTable()
    {
        $products = Product::with(['category'])->whereDeleted(0);
        return datatables($products)
        ->addColumn('category', function($product) {
            return $product->category->name;
        })
        ->addColumn('actions', function($product) {
            return view('products.sub-view.btn-action', compact('product'));
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    public function createOrUpdate(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'min_quantity' => ['required', 'numeric', 'min:0'],
            'categories_id' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all()
            ]);
        }

        Product::saveOrUpdate($request->input());
        return response()->json(['success' => 'OK']);
    }

    public function deleteProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->deleted = 1;
            $product->save();
            return response()->json(['success'=> 'OK']);
        }
        catch(Exception $e) {
            return response()->json(['errors' => 'Produit non trouvé']);
        }
    }
}
