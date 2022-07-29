<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            //get products by user
            $products = Product::where('user_id', Auth::user()->id)->get();

            return $this->sendResponse(ProductResource::collection($products), 'Products retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage());
        }
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $input       = $request->all();
            $categoires  = (new Category())->getAllCategories();
            $categoryIds = array_keys($categoires);

            // Request validation
            $validator = \Validator::make($input, [
                'name' => 'required|max:255',
                'description' => 'required',
                'price' => 'required|numeric|gte:1',
                'category_id' => ['required', 'numeric', Rule::in($categoryIds)],
                'avatar' => 'required|file|mimes:jpeg,jpg,png'
            ]);
            

            if ($validator->fails()) {
                throw new \Exception($validator->errors());
            }

            // Uploaded folder path
            $uploadFolder = 'product';
            $avatar       = $request->file('avatar');

            // Image store in public disk  
            $avatar->store($uploadFolder, ['disk' =>  'public']);

            // Image hash name store in database
            $avatarName       = $avatar->hashName();
            $input['avatar']  = $avatarName;

            // Login user id
            $input['user_id'] = Auth::user()->id;

            $product = Product::create($input);

            return $this->sendResponse(new ProductResource($product), 'Product created successfully.');
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            
            $product = Product::where('user_id', Auth::user()->id)->where('id', $id)->first();

            if (is_null($product)) {
                throw new \Exception('Product not found.');
            }

            return $this->sendResponse(new ProductResource($product), 'Product retrieved successfully.');
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $product = Product::where('user_id', Auth::user()->id)->where('id', $id)->first();

            if (is_null($product)) {
                throw new \Exception('Product not found.');
            }
            $product->delete();

            return $this->sendResponse([], 'Product deleted successfully.');
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
