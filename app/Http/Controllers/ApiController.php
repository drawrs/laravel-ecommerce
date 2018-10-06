<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Product;
use App\ProductPhoto;
use App\ShippingAddress;
use App\ShoppingCart;
use App\Category;
use App\Order;
use App\ProductOrder;
use Auth;
use Carbon\Carbon;
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
    protected $orderTable;
    protected $productOrderTable;

    function __construct()
    {
        $this->userTable = new User;
        $this->categoryTable = new Category;
        $this->productTable = new Product;
        $this->productPhotoTable = new ProductPhoto;
        $this->shippingAddressTable = new ShippingAddress;
        $this->shoppingCartTable = new ShoppingCart;
        $this->orderTable = new Order;
        $this->productOrderTable = new ProductOrder;
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
        } elseif ($request->has('category_id')) {
            $products = $this->productTable
                            ->where('category_id', $request->category_id)
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

        $data['selected_shipping_address'] = null;
        $data['selected_shipping_city'] = null;
        $data['selected_shipping_province'] = null;
        $data['selected_shipping_zip_code'] = null;
        foreach ($data->shippingAddress as $shippingAddress) {
            if ($shippingAddress->is_main_address == "1") {
                $data['selected_shipping_address'] = $shippingAddress->address;
                $data['selected_shipping_city'] = $shippingAddress->city;
                $data['selected_shipping_province'] = $shippingAddress->province;
                $data['selected_shipping_zip_code'] = $shippingAddress->zip_code;
            }
        }

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

        $total_price = 0;
        foreach ($data as $cart) {
            $product = $cart->product;
            $product->category;

            //append total price
            $total_price += $cart->qty * $product->price;

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

        return response()->json(compact('isSuccess', 'message', 'data', 'total_price'));
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
    public function insertShoppingCart(Request $request){
        $data = $this->shoppingCartTable->create([
            'user_id' => $request->user_id,
            'product_id' => $request->product_id,
            'qty' => $request->qty
        ]);
        $isSuccess = false;
        $message = "Gagal meng-insert ke keranjang";

        if (!empty($data)) {
            $isSuccess = true;
            $message = "Berhasil menginsert ke keranjang";
        }
        return response()->json(compact('isSuccess', 'message', 'data'));
    }
    public function str_char_rand($length = 10) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function insertOrder(Request $request){
        $user_id = $request->user_id;
        $isSuccess = false;
        $message = "Gagal order";
        $data = null;

        /************
        * TODO : 1
        * ORDER CODE GENERATOR
        ************/
        // get last oder id
        $last_order_id = is_null($order = $this->orderTable->orderBy('id', 'desc')
                                        ->first()) ? 0: $order->id;
        $random_str = strtoupper($this->str_char_rand(3));

        if ($last_order_id < 9) {
            $order_code = $random_str . "-0000" . ($last_order_id + 1);
        } elseif ($last_order_id < 99) {
            $order_code = $random_str . "-000" . ($last_order_id + 1);
        } elseif ($last_order_id < 999) {
            $order_code = $random_str . "-00" . ($last_order_id + 1);
        } elseif ($last_order_id < 999) {
            $order_code = $random_str . "-0" . ($last_order_id + 1);
        } else {
            $order_code = $random_str . "-" . ($last_order_id + 1);
        }
        // get shipping address
        $mainShippingAddress = $this->shippingAddressTable->where(['user_id' => $user_id, 'is_main_address' => '1'])->first();
        $shipping_address = $mainShippingAddress->address;
        //todo 1 : Create 1 row data in order -> goals, get id of order
        $order = $this->orderTable->create(compact('user_id', 'order_code', 'shipping_address'));

        //todo 2 : Insert product to product order -> goals, product information and order id
        $carts = $this->shoppingCartTable
                        ->where('user_id', $user_id)
                        ->get();
        $dataInsertProductOrder = [];
        foreach ($carts as $cart) {
            $product = $cart->product;

            $order_id = $order->id;
            $category_id = $product->category_id;
            $product_code = $product->product_code;
            $title = $product->title;
            $price = $product->price;
            $description = $product->description;
            $qty_order = $cart->qty;
            $rating = $product->rating;

            //insert cover
            //open photos
            foreach($product->productPhotos as $photo){
                if ($photo->is_cover == "1"){
                    $cover = $photo->file_name;
                }
            }


            $dataInsertProductOrder[] = compact('order_id', 'category_id', 'product_code', 'title', 'price', 'description', 'qty_order', 'rating', 'cover');
        }
        // insert
        $insert_product_order = $this->productOrderTable->insert($dataInsertProductOrder); //boolean
        

        //todo 3 : Remove user item in shopping cart
        $delete_cart = $this->shoppingCartTable->where('user_id', $user_id)->delete();

        if (!empty($order) && $insert_product_order && ($delete_cart !== 0)) {
            $isSuccess = true;
            $message = "Order berhasil";
            $data = $order;
        }
        return response()->json(compact('isSuccess', 'message', 'data'));
    }

    public function getOrders(Request $request)
    {
        $data = $this->orderTable
                        ->where('user_id', $request->user_id)
                        ->get();
        $isSuccess = false;
        $message = "Tidak ada data order";

        if (!empty($data)) {
            $isSuccess = true;
            $message = "";

            foreach ($data as $order) {
                $products = $order->products;

                foreach ($products as $product) {
                    //open category
                    $product->category;

                    //set cover product
                    $product['product_cover'] = null;

                    //open photos
                    foreach($product->productPhotos as $photo){
                        if ($photo->is_cover == "1"){
                            $product['product_cover'] = $photo->file_name;
                        }
                    }
                }
            }
        }

        return response()->json(compact('isSuccess', 'message', 'data'));
    }

    public function getOrderDetail(Request $request)
    {
        $data = $this->orderTable
                        ->find($request->order_id);
        $isSuccess = false;
        $message = "Tidak ada data order";

        if (!empty($data)) {
            $isSuccess = true;
            $message = "";

            $products = $data->products;

            $data['total_price'] = 0;
            foreach ($products as $product) {
                //append total price
                $data['total_price'] += $product->qty_order * $product->price;
                //open category
                $product->category;
                //set cover product
                $product['product_cover'] = null;
                //open photos
                foreach($product->productPhotos as $photo){
                    if ($photo->is_cover == "1"){
                        $product['product_cover'] = $photo->file_name;
                    }
                }
            }
        }

        return response()->json(compact('isSuccess', 'message', 'data'));
    }

    public function postUpdatePaymentProof(Request $request){
        //$file_name = $request->file_name;
        $order_id = $request->order_id;
        $foto = function () use($request) {
                                if (isset($request->file)) {
                                    if ($request->hasFile('file')) {
                                        //echo "Ini foto";
                                        $file = $request->file('file');
                                        //$request->file('photo')->move($destinationPath);
                                        // Siapkan nama file
                                        $picName = $file->getClientOriginalName();
                                        $fileExtension = $file->getClientOriginalExtension();
                                        // tambahkan markup waktu
                                        $fileName = str_slug(Carbon::now() . "_" . md5($picName)) . "." . $fileExtension;
                                        // tujuan folder
                                        $destinationPath = 'document';
                                        // pindahkan ke folder tujuan
                                        $file->move($destinationPath, $fileName);
                                    } else {
                                        // nama foto kalau ngga ada
                                        $fileName = "no_pic.png";
                                    }

                                    return $fileName;
                                } else {
                                    return null;
                                }
                            };
            $payment_proof = $foto();

            $update = $this->orderTable
                        ->find($order_id)
                        ->update(compact('payment_proof'));

            $isSuccess = false;
            $data = null;
            $message = "Gagal menyimpan bukti pembayaran";

            if ($update) {
                $isSuccess = true;
                $data = $this->orderTable->find($order_id);
                $message = "Berhasil Menyimpan";
            }
            $result = compact('isSuccess', 'message', 'data');
            return response()->json($result);
    }

    public function postUpdatePhotoProfile(Request $request){
        //$file_name = $request->file_name;
        $user_id = $request->user_id;
        $foto = function () use($request) {
                                if (isset($request->file)) {
                                    if ($request->hasFile('file')) {
                                        //echo "Ini foto";
                                        $file = $request->file('file');
                                        //$request->file('photo')->move($destinationPath);
                                        // Siapkan nama file
                                        $picName = $file->getClientOriginalName();
                                        $fileExtension = $file->getClientOriginalExtension();
                                        // tambahkan markup waktu
                                        $fileName = str_slug(Carbon::now() . "_" . md5($picName)) . "." . $fileExtension;
                                        // tujuan folder
                                        $destinationPath = 'user_photo';
                                        // pindahkan ke folder tujuan
                                        $file->move($destinationPath, $fileName);
                                    } else {
                                        // nama foto kalau ngga ada
                                        $fileName = "no_pic.png";
                                    }

                                    return $fileName;
                                } else {
                                    return null;
                                }
                            };
            $photo = $foto();

            $update = $this->userTable
                        ->find($user_id)
                        ->update(compact('photo'));

            $isSuccess = false;
            $data = null;
            $message = "Gagal menyimpan foto profil";

            if ($update) {
                $isSuccess = true;
                $data = $this->userTable->find($user_id);
                $message = "Berhasil Menyimpan";
            }
            $result = compact('isSuccess', 'message', 'data');
            return response()->json($result);
    }
}
