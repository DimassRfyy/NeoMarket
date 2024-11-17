<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductOrder;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function checkout(Product $product){

        return view('front.checkout',[
            'product' => $product
        ]);
    }

    public function store(Product $product,Request $request){
        $validate = $request->validate([
            'proof' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
        ]);

        if($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('proofs', 'public');
            $validate['proof'] = $proofPath;
        }

        $validate['product_id'] = $product->id;
        $validate['total_price'] = $product->price;
        $validate['is_paid'] = false;
        $validate['creator_id'] = $product->creator_id;
        $validate['buyer_id'] = auth()->id();

        $productOrder = ProductOrder::create($validate);
        return redirect()->route('front.success_order', $productOrder->id);
    }

    public function success_order(ProductOrder $id){
        return view('front.success_order',[
            'productOrder' => $id
        ]);
    }
}
