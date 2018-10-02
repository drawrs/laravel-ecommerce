<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Product;
use App\ProductPhoto;
use App\ShippingAddress;
use App\ShoppingCart;
use App\Category;
use Auth;
use DB;

class ApiController extends Controller
{
    //
    protected $userTable;
    protected $productTable;
    protected $categoryTable;
    protected $productPhotoTable;
    protected $shippingAddressTable;
    protected $shoppingCartTable;

    function __construct()
    {
        $this->userTable = new User;
        $this->categoryTable = new Category;
        $this->productTable = new Product;
        $this->productPhotoTable = new ProductPhoto;
        $this->shippingAddressTable = new ShippingAddress;
        $this->shoppingCartTable = new ShoppingCart;
    }
    public function userLogin(Request $request){
        $email = $request->email;
        $password = $request->password;

        $isSuccess = false;
        $message = "Login gagal!";
        $data = null;
        $response_status = 200;

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            // Authentication passed...
            $user = User::where('email', $email)->first();
            $isSuccess = true;
            $message = "Login berhasil";
            $data = $user;
        } 
        return response()->json(
                        compact('isSuccess', 
                        'response_status', 
                        'message', 
                        'data')
                    );
    }
    public function registerUser(Request $request){
        $user = new User;

        $email = $request->email;
        $password = bcrypt($request->password);
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        //'name', 'email', 'password', 'first_name', 'last_name', 'status'
        $isSuccess = false;
        $message = "Register gagal!";
        $data = null;
        $response_status = 200;

        $create = null;
        try {
            // insert info user tbl
            $create = $user->create(compact('email', 'password', 'first_name', 'last_name'));
        } catch (\Exception $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                $isSuccess = false;
                $message = "Email telah terdaftar";
                $data = null;
            }
        }

        if (!is_null($create)) {
            // insert seller detail
            $isSuccess = true;
            $message = "Register berhasil";
            $data = $create;
        }

        return response()->json(compact('isSuccess', 'response_status', 'message', 'data'));
    }

    public function getHomeProducts(Request $request){
        if ($request->has('q')) {
            $keyword = $request->q;
            $products = $this->productTable
                            ->where('title', 'LIKE', '%'. $keyword .'%')
                            ->orWhere('description', 'LIKE', '%'. $keyword .'%')
                            ->get();
        } else {
            $products = $this->productTable->all();
        }

        foreach ($products as $product) {
            $product['product_cover'] = null;
            foreach($product->productPhotos as $photo){
                if ($photo->is_cover == "1"){
                    $product['product_cover'] = $photo->file_name;
                }
            }
        }

        if (!empty($products)) {
            $isSuccess = true;
            $response_status = 200;
            $message = "Berhasil mendapatkan data";
        } else {
            $isSuccess = false;
            $response_status = 200;
            $message = "Gagal mendapatkan data";
        }
        $data = $products;

        return response()->json(compact('isSuccess', 'response_status', 'message', 'data'));
    }

    public function getPromotionProducts(Request $request){
        $products = $this->productTable->where('show_in_slider', '1')->get();

        foreach ($products as $product) {
            $product['product_cover'] = null;
            foreach($product->productPhotos as $photo){
                if ($photo->is_cover == "1"){
                    $product['product_cover'] = $photo->file_name;
                }
            }
        }

        if (!empty($products)) {
            $isSuccess = true;
            $response_status = 200;
            $message = "Berhasil mendapatkan data";
        } else {
            $isSuccess = false;
            $response_status = 200;
            $message = "Gagal mendapatkan data";
        }
        $data = $products;

        return response()->json(compact('isSuccess', 'response_status', 'message', 'data'));
    }

    public function getCategories(Request $request){
        $data = $this->categoryTable->all();

        if (!empty($data)) {
            $isSuccess = true;
            $response_status = 200;
            $message = "Berhasil mendapatkan data";
        } else {
            $isSuccess = false;
            $response_status = 200;
            $message = "Gagal mendapatkan data";
        }

        return response()->json(compact('isSuccess', 'response_status', 'message', 'data'));
    }
    public function getProduct(Request $request){
        $data = $this->productTable->find($request->product_id);

        if (!empty($data)) {
            $data['product_cover'] = null;
            foreach($data->productPhotos as $photo){
                if ($data->is_cover == "1"){
                    $product['product_cover'] = $photo->file_name;
                }
            }
            $data->category;
            // response message
            $isSuccess = true;
            $response_status = 200;
            $message = "Berhasil mendapatkan data";
        } else {
            $isSuccess = false;
            $response_status = 200;
            $message = "Gagal mendapatkan data";
        }

        return response()->json(compact('isSuccess', 'response_status', 'message', 'data'));
    }

    public function searchProduct(Request $request){
        $keyword = $request->q;
        $products = $this->productTable
                        ->where('title', 'LIKE', '%'. $keyword .'%')
                        ->orWhere('description', 'LIKE', '%'. $keyword .'%')
                        ->get();

        foreach ($products as $product) {
            $product['product_cover'] = null;
            foreach($product->productPhotos as $photo){
                if ($photo->is_cover == "1"){
                    $product['product_cover'] = $photo->file_name;
                }
            }
        }

        if (!empty($products)) {
            $isSuccess = true;
            $response_status = 200;
            $message = "Berhasil mendapatkan data";
        } else {
            $isSuccess = false;
            $response_status = 200;
            $message = "Gagal mendapatkan data";
        }
        $data = $products;

        return response()->json(compact('isSuccess', 'response_status', 'message', 'data'));
    }

    public function getUserProfile(Request $request){
        $data = $this->userTable->find($request->user_id);
        $data->shippingAddress;

        $isSuccess = true;
        $message = "Berhasil mendapatkan data";

        if (empty($data)) {
            $isSuccess = false;
            $message = "Gagal mendapatkan data";
            $data = null;
        }

        return response()->json(compact('isSuccess', 'response_status', 'message', 'data'));
    }
    public function updateUserProfile(Request $request){
        $data = $this->userTable->find($request->user_id);
        $isSuccess = false;
        $message = "Gagal Update";

        if (!empty($data)) {
            $first_name = $request->first_name;
            $last_name = $request->last_name;
            //$email = $request->first_name;

            $updateProfile = $data->update(compact('first_name', 'last_name'));
            if ($updateProfile){
                $isSuccess = true;
                $message = "Berhasil update";
                $data = $this->userTable->find($data->id);
            }
        }
        return response()->json(compact('isSuccess', 'response_status', 'message', 'data'));
    }
    public function updateShippingAddress(Request $request){
        $data = $this->shippingAddressTable->find($request->address_id);
        $isSuccess = false;
        $message = "Gagal mengupdate data";

        if (!empty($data)) {
            $title = $request->title;
            $city = $request->city;
            $province = $request->province;
            $address = $request->address;
            $zip_code = $request->zip_code;
            $is_main_address = $request->is_main_address;
            // set all row to is_main_address = 0 first

            DB::table('shipping_addresses')
                    ->where('user_id', $data->user_id)
                    ->update(['is_main_address' => '0']);
            
            //save data
            $updateAddress = $this->shippingAddressTable
                                    ->find($request->address_id)
                                    ->update(
                                            compact('title', 
                                                    'city', 
                                                    'province', 
                                                    'address',
                                                    'zip_code', 
                                                    'is_main_address')
                                        );

            if ($updateAddress) {
                $isSuccess = true;
                $message = "berhasil di update";
                $data = $this->shippingAddressTable->find($data->id);
            }

            return response()->json(compact('isSuccess', 'response_status', 'message', 'data'));
        }
    }

    public function getShoppingCarts(Request $request){
        $data = $this->shoppingCartTable->where('user_id', $request->user_id)->get();
        $isSuccess = true;
        $message = "Berhasil mendapatkan data";

        foreach ($data as $cart) {
            $product = $cart->product;
            $product->category;

            $product['product_cover'] = null;
            foreach($product->productPhotos as $photo){
                if ($photo->is_cover == "1"){
                    $product['product_cover'] = $photo->file_name;
                }
            }
        }

        if (empty($data)) {
            $isSuccess = false;
            $message = "Tidak ada data untuk ditampilkan !";
        }

        return response()->json(compact('isSuccess', 'response_status', 'message', 'data'));
    }
    public function updateCartItemQty(Request $request)
    {
        $data = $this->shoppingCartTable->find($request->cart_id);
        $isSuccess = false;
        $message = "Gagal mengupdate";

        if (!empty($data)) {
            $qty = $request->new_qty;
            //update
            $update = $data->update(compact('qty'));
            if ($update) {
                $isSuccess = true;
                $message = "Berhasil diupdate";
                $data = $this->shoppingCartTable->find($data->id);
            }
        }
        return response()->json(compact('isSuccess', 'response_status', 'message', 'data'));
    }

    public function deleteCartItem(Request $request){
        $data = $this->shoppingCartTable->find($request->cart_id);

        $isSuccess = false;
        $message = "Gagal dihapus";

        if ($data->delete()) {
            $isSuccess = true;
            $message = "berhasil dihapus";
        }

        return response()->json(compact('isSuccess', 'message'));
    }
}
