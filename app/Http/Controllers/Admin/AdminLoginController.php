<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Routing\Controller;
use App\Models\AdminLogin;
use App\Models\Access_permission;
use App\Models\user_permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class adminloginController extends Controller
{

    /**
     * @param  email,password
     * @return json Response
     */
    public function login(Request $request)
    {
        // validate fields
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:admin_logins',
            'password' => 'required',
        ]);

    // if validation failed
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors(),
            ], 400);
        }

        $credentials = $request->only('email', 'password');

        // if invalid credentials 
        if (!Auth::guard('admin')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => "Invalid credentials",
            ], 400);
        }

        $user = Auth::guard('admin')->user();
        // if login successfully then  create token 
        $token = $user->createToken('authToken')->plainTextToken;
        $staffdata=[];

        // if admin log in 
        if($user->role==1)
        {
            $staffdata[] =[
            $permission="Super Admin",
        ];
        }

        // if staff log in 
        elseif($user->role==0)
        {
           $permission_id = user_permission::where('user_id', $user->id)->pluck('permission_id');
            $permissions = Access_permission::whereIn('id', $permission_id)->pluck('permission');
            $staffdata[] = [
            
                'permissions' => $permissions,
            ];
        
        }
// json response
        return response()->json([
            'success' => true,
            'message' => "Login successfully",
            'token' => $token,
            'permision'=>$staffdata,
        ], 200);
    }


        /** Log out User
         */
public function logout(request $request)
{
    // Delete Current user token
    $request->user()->currentAccessToken()->delete();

//   json response
    return response()->json([
        'id' => $request->user()->id,
        'success' => true,
        'message' => 'User logged out successfully'  
    ],200);
}

          /** Show profile 
           * @param url /id
         */
    public function profile()
    {

        $user = Auth::user(); // check log in user

        $profile=AdminLogin::select("name","email",'image')->find($user);
        
        
        // json response 
        return response()->json([
        'success'=>true,
        'message'=>"successfully",
        'data'=>$profile
        ],200);
    
    }

       /** Update profile 
           * @param email name image
         */
    public function editprofile(request $request)
    {
        // validate fields
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|required|email',
            'name' => 'sometimes|required',
            'image' => 'sometimes|required',
        ]);

        // if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()
                 ], 400);
        }

   
        $user=Auth::user(); // current Log in user

        // if email update
        if($request->has('email'))
        {
            $user->email=$request['email'];
        }
        // if name update
        if($request->has('name'))
        {
            $user->name=$request['name'];
        }
        // if image update
        if($request->has('image'))
        {
            $user->image=$request['image'];
        }
        $user->save();

        // json response
        return response()->json([
'success'=>true,
'message'=>"successfully",
'data'=>$user
        ],200);
    }

         /** change password
           * @param  current_password, new_password , confirm_password
           *         
           */
    public function changepassword(request $request)
    {
        // validate fields
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|max:16',
            'confirm_password' => 'required|same:new_password',
        ]);
    // if validation failed
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()
            ], 400);
        }
    
        $user = Auth::user(); // check current user
    
        // if password not match 
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid current password.'
            ], 400);
        }
    
        $user->password = bcrypt($request->new_password);
        $user->save();

    // json response
        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
            'data' => $user
        ], 200);

    }
}
