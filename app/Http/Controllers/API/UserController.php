<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\User;
use App\Purchase_item;
use App\Purchase;
use App\Item;
use App\Rating;

use DB;
use Validator;

class UserController extends Controller
{
    public $successStatus = 200;

    public function login() {

        if (Auth::attempt(['email' => request('email'),'password' => request('password')])) {
                $user = Auth::user();
                $success['token'] = $user->createToken('nApp')->accessToken;

                $id = Auth::user()->id;

                $data = User::where('id', $id)->get();

                return response()->json([
                    'success' => $success,
                    'data' => $data
                ], $this->successStatus);
        } else {
            return response()->json([
                'error' => 'Unauthorized',
            ], 401);
        }

    }

    public function register(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 401);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('nApp')->accessToken;
        $success['name'] = $user->name;

        return response()->json([
            'success' => $success
        ], $this->successStatus);
    
    }

    public function itemList() {
        $id = Auth::user()->id;

        $item = Item::select('item.item_id', 'item.name', 'item.selling_price', 'users.name AS seller')
        ->join('users', 'users.id', '=', 'item.id')
        ->whereNotIn('item.id', [$id])
        ->get();

        return response()->json([
            'item' => $item,
        ], $this->successStatus);
    
    }

    public function transactionList() {
        $id = Auth::user()->id;
        
        // $cartList = Purchase_item::select('users.name AS seller', 'purchase_item.amount', 'purchase_item.selling_price', 'item.item_id', 'item.name')
        // ->join('users', 'purchase_item.seller_id', '=', 'users.id')
        // ->join('item', 'purchase_item.item_id', '=', 'item.item_id')
        // ->where('purchase_item.buyer_id', $id)->get();

        $cartSeller = Purchase_item::select('users.id', 'users.name AS seller', 'purchase_item.purchase_id')
        ->join('users', 'purchase_item.seller_id', '=', 'users.id')
        ->join('item', 'purchase_item.item_id', '=', 'item.item_id')
        ->where('purchase_item.buyer_id', $id)
        ->distinct()
        ->get();

        return response()->json([
            'cartSeller' => $cartSeller
        ], $this->successStatus);
    
    }

    public function cartList(Request $request) {
        // $id = Auth::user()->id;

        $cartList = Purchase_item::select('users.name AS seller', 'purchase_item.amount', 'purchase_item.selling_price', 'item.item_id', 'item.name')
        ->join('users', 'purchase_item.seller_id', '=', 'users.id')
        ->join('item', 'purchase_item.item_id', '=', 'item.item_id')
        ->where('purchase_item.purchase_id', $request['purchase_id'])
        // ->where('purchase_item.buyer_id', $id)
        ->get();

        return response()->json([
            'cartList' => $cartList
        ], $this->successStatus);
    
    }

    public function itemDetail($item_id) {
        $id = Auth::user()->id;
        
        // $usr_buyer = Location::select('city_id')->where('user_id', $id)->get();

        $usr_seller = Item::select('item.id', 'item.item_id', 'item.name', 'item.description', 'item.stock', 'item.selling_price', 'item.id AS seller_id', 'users.name AS seller')
        ->join('users', 'item.id', '=', 'users.id')
        ->where('item.item_id', $item_id)->get();

        $rating = Item::select('rating.rating', 'rating.review', 'rating.time')
        ->join('rating', 'item.item_id', '=', 'rating.item_id', 'LEFT OUTER')
        ->where('item.item_id', $item_id)->get();

        $ratingLapak = Rating::select(DB::raw('avg(rating.rating) AS ratingLapak'))
        ->join('item', 'item.item_id', '=', 'rating.item_id')
        ->where('rating.id', $usr_seller[0]['id'])    
        ->get();

        return response()->json([
            // 'usr_buyer' => $usr_buyer,
            'usr_seller' => $usr_seller,
            'rating' => $rating,
            'ratingLapak' => $ratingLapak,
        ], $this->successStatus);

    }

    public function storeCart(Request $request) {
        $id = Auth::user()->id;

        // $this->validate($request, [
        //     'seller_id' => ['required'],
        //     'item_id' => ['required'],
        //     'amount' => ['required'],
        // ]);

        $purchase_count = Purchase::select('seller_id', 'buyer_id')
                        ->where('seller_id', $request['seller_id'])
                        ->where('buyer_id', $id)
                        ->whereNull('time')
                        ->count();
        
        if($purchase_count == 0) {
            Purchase::create([
                'seller_id' => $request['seller_id'],
                'buyer_id' => $id,
            ]);
        }

        $purchase_id = Purchase::select('purchase_id')
                        ->where('seller_id', $request['seller_id'])
                        ->where('buyer_id', $id)
                        ->whereNull('time')
                        ->orderBy('purchase_id', 'DESC')
                        ->first();
        $purchase_detail = Item::select('purchasing_price', 'selling_price', 'id')
                        ->where('item.item_id', $request['item_id'])
                        ->first();

        $purchaseItem_count = Purchase_item::select('item_id')
                            ->where('buyer_id', $id)
                            ->where('item_id', $request['item_id'])
                            ->count();
        $purchaseItem_amount = Purchase_item::select('amount')
                            ->where('buyer_id', $id)
                            ->where('item_id', $request['item_id'])
                            ->get();

        if($purchaseItem_count == 0) {
            Purchase_item::create([
                'amount' => $request['amount'],
                'purchasing_price' => $purchase_detail['purchasing_price'],
                'selling_price' => $purchase_detail['selling_price'],
                'purchase_id' => $purchase_id['purchase_id'],
                'item_id' => $request['item_id'],
                'seller_id' => $purchase_detail['id'],
                'buyer_id' => $id,
            ]);
        } else {          
            Purchase_item::where('item_id', $request['item_id'])
                            ->where('buyer_id', $id)
                            ->update([
                                'amount' => ($purchaseItem_amount[0]['amount']+$request['amount']) 
                            ]);
        }
    }

    public function deleteCart($item_id) {
        $id = Auth::user()->id;

        $usr = Purchase_item::where('item_id', $item_id)->where('buyer_id', $id);
        $usr->delete();   
    }

}
