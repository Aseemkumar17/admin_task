<?php
namespace App\Http\Controllers;
use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Mail\userverifymail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class UserprofileController extends Controller
{
     /** Show profile 
           * @param url /id
         */
        public function userprofile()
        {
    
            $user = Auth::user(); // check log in user
    
            $profile=User::select("name","email",'image')->find($user);
            // json response 
            return response()->json([
            'success'=>true,
            'message'=>"successfully",
            'data'=>$profile
            ],200);
        
        }
}
