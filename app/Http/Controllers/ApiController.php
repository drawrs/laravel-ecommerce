<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Product;
use App\ProductPhoto;
use App\Category;
use Auth;

class ApiController extends Controller
{
    //
    protected $userTable;
    protected $productTable;
    protected $categoryTable;
    protected $productPhotoTable;

    function __construct()
    {
        $this->userTable = new User;
        $this->categoryTable = new Category;
        $this->productTable = new Product;
        $this->productPhotoTable = new ProductPhoto;
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
        $products = $this->productTable->all();

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
}
