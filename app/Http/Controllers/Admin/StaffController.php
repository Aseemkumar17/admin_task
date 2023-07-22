<?php

namespace App\Http\Controllers\Admin;
use App\Models\AdminLogin;
use App\Models\user_permission;
use App\Models\Access_permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
class staffController extends Controller
{
     /** Add Staff
     * @param name,email ,password, image,roles
     * @return json Response
     */
    public function addstaff(request $request)
 {
         // validation fields
         $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:admin_logins,email',
            'password' => 'required|min:8|max:16',
            'image' => 'required',
            'roles' => 'required|array|exists:access_permissions,id',
           ]);
        // if validation fails 
         if ($validator->fails())
        {
        //json response
      return response()->json([
        'success' => false,
        'error' => $validator->errors()
      ], 400);
  }
  //save data in table
$record=new AdminLogin;
$record->name=$request['name'];
$record->email=$request['email'];
$record->password=Hash::make($request['password']);
$record->image=$request['image'];
$record->role="0";
$record->save();
$roles=$request['roles'];

//add permissions
foreach($roles as $permission)
{
    $user_permission=new user_permission;
    $user_permission->user_id = $record->id;
    $user_permission->permission_id = $permission;
    $user_permission->save();
  }
  //json response
  return response()->json([
    'success' => true,
    'message' => 'Staff added successfully.',
    'data'=>$record
  ],200);
}

   /** View Staff
     * @param url/id
     * @return json Response
     */
public function viewstaff($id)
{
$staff=AdminLogin::where('role','0')->find($id);
// if data not found
if(!$staff)
{
    //json response
    return response()->json([
        'success'=>false,
        'message'=>"staff not found"
    ],400);
}
//View permissions
$staffdata=[];

   $permission_id = user_permission::where('user_id', $staff->id)->pluck('permission_id');
        $permissions = Access_permission::whereIn('id', $permission_id)->pluck('permission');
        $staffdata[] = [
            'id' => $staff->id,
            'name' => $staff->name,
            'email' => $staff->email,
            'image'=> $staff->image,
            'phone_number'=> $staff->phone_number,
            'block'=>$staff->block,
            'permissions' => $permissions,
        ];
    

//json response
    return response()->json([
        'success' => true,
        'message' => "successful",
        'data' => $staffdata
    ], 200);
}


/** block staff
     * @param url/id
     * @return json Response
     */
public function blockstaff($id)
{
    $staff=AdminLogin::find($id); //find data
//if data not foound
    if(!$staff)
{
    //json response 
    return response()->json([
        'success'=>false,
        'message'=>"user not found",
    ],400);
}
//if user is unblock then chng to block
if ($staff->block === null) {
    $staff->block = "1";
    //block to unblock
} else {
    $staff->block= null;
}

$staff->save();

//json response
return response()->json([
    'success' => true,
    'message' => "Successfully",
    'data' => $staff,
],200);
}

/** Delete Staff
     * @param url/id
     * @return json Response
     */
public function deletestaff($id)
{
    
 $staff=AdminLogin::find($id);//find data

    //if data not foound
    if(!$staff)
    {
        //json response
        return response()->json([
            'success'=>false,
            'message'=>"user not found",
        ],400);
    }
$staff->delete();
//return response
return response()->json([
'success'=>true,
'message'=>"delete suucessfully",
],200);
}


    
/** Staff Listing & Search 
     * @param search
     * @return json Response
     */
    public function list_search(Request $request)
{
    $records = AdminLogin::where('role', '0')->orderBy('id', 'DESC');

    //search staff list 
    if (isset($request->search)) {
        $records = $records->where('name', 'LIKE', "%{$request->search}%")
                           ->orWhere('email', 'LIKE', "%{$request->search}%");
    }

    $data = $records->get();
    //staff listing 
        $staffdata=[];
        foreach($data as $admin)
{
   $permission_id = user_permission::where('user_id', $admin->id)->pluck('permission_id');
        $permissions = Access_permission::whereIn('id', $permission_id)->pluck('permission');
        $staffdata[] = [
            'id' => $admin->id,
            'name' => $admin->name,
            'email' => $admin->email,
            'permissions' => $permissions,
        ];
    }

//json response


    return response()->json([
        'success' => true,
        'message' => "successful",
        'data' => $staffdata
    ], 200);
}
   
/** edit  Staff
     * @param required email,roles
     * @return json Response
     */
public function edit(request $request, $id)
{ //validate fields
       $validation = Validator::make($request->all(), [
     'email' => 'required|email|unique:admin_logins,email',
     'roles' => 'required|array|exists:access_permissions,id',
    ]
    );
    //if validation failed
    if ($validation->fails()) {
        return response()->json([
            'success' => false,
            'error' => $validation->errors(),
        ], 400);
    }

    $staff=AdminLogin::where('role','0')->find($id);// find staff id
    
    //if not found @return response
    if(!$staff)
    {
        //json response 
        return response()->json([
            'success'=>false,
            'message'=>"staff not found"
        ],400);
    }
      // Edit data if provided
      if ($request->has('name')) {
        $staff->name = $request['name'];
     }
     if ($request->has('email')) {
        $staff->email = $request['email'];
     }
     if ($request->has('image')) {
        $staff->image =  $request['image'];
     }
     if ($request->has('roles')) {
        $roles = $request->input('roles');
        user_permission::where('user_id', $staff->id)->delete();
        foreach ($roles as $roleId) {
            $userPermission = new  user_permission();
            $userPermission->user_id = $staff->id;
            $userPermission->permission_id = $roleId;
            $userPermission->save();
        }
}
//json response
return response()->json([
'success'=>true,
'message'=>"successfully edit"

],200);
}
}
