<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProductController extends Controller
{
    private $jsonFilePath = 'products.json';

    public function index()
    {
        return view('welcome');
    }

    public function fetchProducts()
    {
        if (!Storage::exists($this->jsonFilePath)) {
            return response()->json([]);
        }

        $products = json_decode(Storage::get($this->jsonFilePath), true);

        usort($products, function($a, $b) {
            return strtotime($b['datetime']) <=> strtotime($a['datetime']);
        });

        return response()->json($products);
    }


    public function store(Request $request)
    {
    $request->validate([
        'product_name' => 'required|string|max:255',
        'quantity' => 'required|integer|min:0',
        'price' => 'required|numeric|min:0',
    ]);

    $products = Storage::exists($this->jsonFilePath)
        ? json_decode(Storage::get($this->jsonFilePath), true)
        : [];

    $newProduct = [
        'id' => uniqid('prod_'), // <<< ADD THIS LINE to create a unique ID
        'product_name' => $request->product_name,
        'quantity' => $request->quantity,
        'price' => $request->price,
        'datetime' => Carbon::now()->toDateTimeString(),
    ];

    $products[] = $newProduct;

    Storage::put($this->jsonFilePath, json_encode($products, JSON_PRETTY_PRINT));

    return response()->json(['success' => 'Product added successfully.']);
}

public function update(Request $request, $id)
{
    $request->validate([
        'product_name' => 'required|string|max:255',
        'quantity' => 'required|integer|min:0',
        'price' => 'required|numeric|min:0',
    ]);

    $products = json_decode(Storage::get($this->jsonFilePath), true);

    $productUpdated = false;
    foreach ($products as $key => $product) {
        
        if (isset($product['id']) && $product['id'] == $id) {
         
            $products[$key]['product_name'] = $request->product_name;
            $products[$key]['quantity'] = $request->quantity;
            $products[$key]['price'] = $request->price;
            $productUpdated = true;
            break;
        }
    }

    if (!$productUpdated) {
        return response()->json(['error' => 'Product not found.'], 404);
    }

    
    Storage::put($this->jsonFilePath, json_encode($products, JSON_PRETTY_PRINT));

    return response()->json(['success' => 'Product updated successfully.']);
}
}