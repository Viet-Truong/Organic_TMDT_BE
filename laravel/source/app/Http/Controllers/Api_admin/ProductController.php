<?php

namespace App\Http\Controllers\Api_admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Size;
use App\Models\Variation;
use App\Models\Image;
use App\Models\category;
use Illuminate\Support\Facades\DB;
use File;
use Storage;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $query = Product::with('Images');

        // Kiểm tra xem có truyền tham số 'q' không
        if ($request->has('q')) {
            $query->where('name', 'like', '%' . $request->input('q') . '%');
        }

        $products = $query->get();

        $productsInfo = [];
        foreach ($products as $product) {
            $productsInfo[] = $this->formatProductData($product);
        }

        return response()->json([
            'status' => 'success',
            'data' => $productsInfo
        ]);
    }
    public function store(Request $request)
    {
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            $imageUrls = [];

            foreach ($images as $image) {
                $fileName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('Image/Product'), $fileName);
                $fileData = File::get(public_path('Image/Product/' . $fileName));
                Storage::disk('product')->put($fileName, $fileData);
                $storagePath = Storage::disk('product')->url($fileName);
                array_push($imageUrls, $storagePath);
            }

            $categoryName = $request->input('category');

            $category = category::where('name_category', $categoryName)->first();

            if ($category) {
                $categoryId = $category->id;
            } else {
                return response()->json(['error' => 'Category not found'], 404);
            }

            $product = Product::create([
                'shop_id' => $request->input('shop_id'),
                'name' => $request->input('name'),
                'price' => $request->input('price'),
                'quantity' => $request->input('quantity'),
                'description' => $request->input('description'),
                'category_id' => $categoryId,
            ]);

            foreach ($imageUrls as $imageUrl) {
                Image::create([
                    'product_id' => $product->product_id,
                    'url' => $imageUrl,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => 'Images received',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No image provided',
            ], 400);
        }
    }

    public function show($id)
    {
        // return Product::find($id);
        $product = DB::table('product')
            ->where('product.id', '=', $id)->first();

        $variants = DB::table('variation')
            ->join('color', 'variation.colorId', '=', 'color.Id')
            ->where('variation.productId', '=', $product->id)
            ->select('variation.*', 'color.name AS colorName')->get();
        foreach ($variants as $variant) {
            $variant->Sizes = DB::table('size')
                ->where('size.variantId', '=', $variant->id)->get();
            $variant->Images = DB::table('image')
                ->where('image.variantId', '=', $variant->id)->get();
        }
        $product->Variants = $variants;
        return $product;
    }


    
    public function update(Request $request, $id)
    {
        $product = Product::find($request->id);

        //delete variant, size, image
        $variants = DB::table('variation')
            ->where('variation.productId', '=', $product["id"])->get();

        foreach ($variants as $variant) {

            $oldVariant = Variation::find($variant->id);
            $oldVariant->deleted = 1;
            $oldVariant->save();

            DB::table('size')
                ->where('size.variantId', '=', $variant->id)->delete();

            DB::table('image')
                ->where('image.variantId', '=', $variant->id)->delete();
        }

        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->img = $request->img;
        $product->categoryId = $request->categoryId;
        $product->deleted = $request->deleted;

        $product->save();

        $listVariant = $request->variant;
        foreach ($listVariant as $item) {
            $variant = new Variation();
            $variant->productId = $product->id;
            $variant->colorId = $item["colorId"];
            $variant->thumbnail = $item["thumbnail"];
            $variant->deleted = $item["deleted"];

            $variant->save();

            $listSize = $item["sizes"];
            foreach ($listSize as $s) {
                $sizeVariant = new Size();
                $sizeVariant->variantId = $variant->id;
                $sizeVariant->size = $s["size"];
                $sizeVariant->quantity = $s["quantity"];
                $sizeVariant->deleted = $s["deleted"];

                $sizeVariant->save();
            }
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        $product->deleted = 1;
        $rs = $product->save();
        if ($rs) {
            return "200";
        } else {
            return "500";
        }
    }

    //GET FIELD URL ABOUT TABLE IMAGES
    private function getImageUrls($images){
        return $images->pluck('url')->toArray();
    }

    private function formatProductData($product){
        return [
            'product_id' => $product->product_id,
            'shop_id' => $product->shop_id,
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => $product->quantity,
            'description' => $product->description,
            'category' => $product->category_id,
            'images' => $this->getImageUrls($product->Images),
        ];
    }
}
