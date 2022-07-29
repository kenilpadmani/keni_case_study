<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $carts = Cart::all();

            if (Auth::check()) {
                $carts = Cart::where('user_id', Auth::user()->id)->get();
            }
            
            return $this->sendResponse(CartResource::collection($carts), 'Carts retrieved successfully.');
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
            $input     = $request->all();

            // Request Validation
            $validator = \Validator::make($input, [
                'product_id' => 'required|numeric',
                'session_id' => 'required',
            ]);

            // If request validation fail throw error
            if ($validator->fails()) {
                throw new \Exception($validator->errors());
            }

            // find product
            $product = Product::find($input['product_id']);

            if (is_null($product)) {
                throw new \Exception('Product not found.');
            }

            $input['user_id'] = null;

            if (Auth::check()) {

                $input['user_id'] = Auth::user()->id;
                $cart             = Cart::where('user_id', $input['user_id'])->where('product_id', $input['product_id'])->first();

            } else if ($input['session_id']) {

                $cart = Cart::where('session_id', $input['session_id'])->where('product_id', $input['product_id'])->first();

            }

            // Cart exists then update qty
            if (isset($cart) && !empty($cart)) {

                $cart->qty = empty($cart->qty) ? 1 : $cart->qty + 1;
                $cart->save();
            } else {
                // Create new cart
                $cartArr = [
                    "product_id" => $input['product_id'],
                    "qty"        => 1,
                    "session_id" => $input['session_id'],
                    "user_id"    => $input['user_id']
                ];
                $cart = Cart::create($cartArr);
            }


            return $this->sendResponse(new CartResource($cart), 'Cart created successfully.');
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $sessionId)
    {
        try {
            $input = $request->all();
            $input['session_id'] = $sessionId;

            $input['user_id'] = null;
            if (Auth::check()) {
                $input['user_id'] = Auth::user()->id;
            }

            // Request validation  
            $validator = \Validator::make($input, [
                'product_id' => 'required',
                'qty'        => 'required|numeric|gte:1'
            ]);

            // If request validation fail throw error
            if ($validator->fails()) {
                throw new \Exception($validator->errors());
            }

            if (Auth::check()) {

                $cart = Cart::where('user_id', $input['user_id'])->where('product_id', $input['product_id'])->first();

            } else if ($input['session_id']) {

                $cart = Cart::where('session_id', $input['session_id'])->where('product_id', $input['product_id'])->first();

            }

            // Update Cart
            if (isset($cart) && !empty($cart)) {
                $cart->qty        = $input['qty'];
                $cart->user_id    = $input['user_id'];
                $cart->session_id = $input['session_id'];
                $cart->save();
            }

            if (!isset($cart)) {
                return $this->sendResponse([], 'Cart not avaiable.');
            }

            return $this->sendResponse(new CartResource($cart), 'Cart updated successfully.');
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
            $cart = new Cart(); 
            // Remove Cart
            $cart->where('id', $id)->delete();

            return $this->sendResponse([], 'Cart deleted successfully.');
        } catch (\Throwable $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
