<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Routing\Controller;
use App\Models\User;
use App\Models\Product;

use Illuminate\Http\Request;

class DashboardController extends Controller
{


    /** Dashboard Count
     * @param /id , page_name,title,image,description
     * @return json Response
     */
    public function counts()
    {

        $productsCount=Product::count(); //product table count
        $UserCount=User::count(); //user table count

         // json response
        return response()->json([
        'success'=>true,
        'message'=>"successful",
        'data'=>[
        'productcount'=>$productsCount,
        'usercount'=>$UserCount,
        ]
        ],200);
  }
   /** Dashboard Userlisting
      * @return json Response
     */
  public function userlisting(Request $request)
    {
        $limit= $request->input('limit',5);
        $users = User::select("id", 'name', 'email', 'phone_no')->orderBy('id', 'DESC')->paginate($limit);//Retrive data
    // json response 
        return response()->json([
            'success' => true,
            'message' => "successful",
            'data' => $users,
        ],200);
    }
    

}
