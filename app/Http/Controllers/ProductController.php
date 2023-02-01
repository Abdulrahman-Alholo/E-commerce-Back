<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Carbon\Carbon;
// use App\Http\Controllers\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str as SupportStr;
use Str as GlobalStr;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::select('id', 'title','description','image')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required',
            'description'=>'required',
            'image'=>'required|image'
        ]);
        $imageName = SupportStr::random().'.'.$request->image->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('product/image',$request->image,$imageName);
        Product::create($request->post()+['image'=> $imageName]);
        return response()->json([
            'message'=>'Itemadded successfuly'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json([
            'product'=>$product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title'=>'required',
            'description'=>'required',
            'image'=>'nullable'
        ]);
        $product->fill($request->post())->update();

        if ($request->image) {

            $exist = Storage::disk('public')->exists("product/image/{$product->image}");
            if ($exist) {
                $exist = Storage::disk('public')->delete("product/image/{$product->image}");
            }

        }
        $imageName = SupportStr::random().'.'.$request->image->getClientOriginalExtension();
        Storage::disk('public')->putFileAs('product/image',$request->image,$imageName);
        $product->image = $imageName;
        $product->save();
        return response()->json([
            'message'=>'Item updated successfuly'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if ($product->image) {

            $exist = Storage::disk('public')->exists("product/image/{$product->image}");
            if ($exist) {
                $exist = Storage::disk('public')->delete("product/image/{$product->image}");
            }

        }
        $product->delete();
        return response()->json([
            'message'=>'Item deleted successfuly'
        ]);
    }
}
