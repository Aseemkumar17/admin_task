<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Mail\userverifymail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\OTP;


class usercontroller extends Controller
{
     /** Sign Up 
     * @param name,email ,password, confirm_password
     * @return json Response
     */
   

 /** User listing
    * @param ?action=block,active,deactive,activate
    * @return json Response
    */
    public function userlist(Request $request)
    {
        $users = User::select("id", 'name', 'email', 'phone_no');
        $limit= $request->input('limit',5);
    // filters which list you want enter that action
        if ($request->has('action')) {
            $action = $request->query('action');
    
            if ($action === 'block') {   //block user 
                $users->where('block', 1);

            } elseif ($action === 'unblock') { // unblock user
                $users->whereNull('block');

            } elseif ($action === 'active') { // active user 
            $users->whereNull('deactivate');

            } elseif ($action === 'deactivate') {  //deactivate user
                $users->where('deactivate', 1);
            }
        }
    
        $data = $users->paginate($limit);

    // json response 
        return response()->json([
            'success' => true,
            'message' => "successful",
            'data' => $data,
        ],200);
    }
    


 /** Add User
    * @param name,email,phone_no,pssword,image
    * @return json Response
    */
public function AddUser(request $request)
{
   
        // validation fields
          $validator = Validator::make($request->all(),
           [
            'name' => 'required',
            'email' => 'required',
            'phone_no' => 'required',
            'password'=>'required',
     'image'=>'required',
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

          //save data
           $record=new User;
           $record->name = $request['name'];
           $record->email = $request['email'];
           $record->phone_no = $request['phone_no'];
           $record->password = $request['password'];
           $record->image=$request['image'];
             $record->save();
   
         // json response
            return response()->json([
              'success'=> true,
              'message'=>"Task successfully add",
              'data'=>$record
            ],200);
           }


 /** user show
    * @param url/id
    * @return json Response
    */
public function usershow($id)
{
$user=User::select('id','name','email','image','phone_no')->find($id);//find data
// if user is empty
if(!$user)
{
    //json response
    return response()->json([
        'success'=>false,
        'message'=>"user not found",
    ],400);
}

//json response
return response()->json([
'success'=>true,
'message'=>"suucessful",
'data'=>$user,
]);

}


 /** Block/unblock user 
    * @param url/id
    * @return json Response
    */
public function blockuser($id)
{
$user=User::find($id);//find id in table

//if not found @return response
if(!$user)
{
    //json response
    return response()->json([
        'success'=>false,
        'message'=>"user not found",
    ],400);
}
//if user is unblock then chng to block
if ($user->block === null) {
    $user->block = "1";

    //user is block then chng to unblock
} else {
    $user->block= null;
}

$user->save();
// json response
return response()->json([
    'success' => true,
    'message' => "Successfully",
    'data' => $user,
],200);
}


 /** deactivate/activate user 
    * @param url/id
    * @return json Response
    */
public function deactivateuser($id)
{
$user=User::find($id);//find data
//if not found
if(!$user)
{
    //json response
    return response()->json([
        'success'=>false,
        'message'=>"user not found",
    ],400);
}
//if user is activate chnge to deactivate
if ($user->deactivate === null) {
    $user->deactivate = "1";

//deactivate to activate
} else {
    $user->deactivate= null;
}

$user->save();

//json response 
return response()->json([
    'success' => true,
    'message' => "Successfully",
    'data' => $user,
],200);
}


   /** Delete User
    * @param url/id
    * @return json Response
    */
public function deleteuser($id)
{
    $user=User::find($id);//find data
    //if not found
if(!$user)
{//json response
    return response()->json([
        'success'=>false,
        'message'=>"user not found",
    ],400);
}

$user->delete();
//json response
return response()->json([
'success'=>true,
'message'=>"delete suucessfully",
],200);
}



   /** search User
    * @param url/id
    * @return json Response
    */
public function searchUser(Request $request)
{

    $search = $request->input('search');//search input

    $users = User::where('name', 'like', '%' . $search . '%')->select("id","name","email","phone_no")->paginate();
//json response
    return response()->json([
        'success' => true,
        'message' => "Successful search",
        'data' => $users,
    ]);
}


   /** export  User
    * @param start_date,end_date
    * @return json Response
    */
public function exportUser(Request $request)
{
    // Validation fields
    $validator = Validator::make($request->all(), [
        'start_date' => 'required|date_format:Y-m-d',
        'end_date' => 'required|date_format:Y-m-d',
    ]);

    // If validation fails
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'error' => $validator->errors()
        ], 400);
    }

    // Retrieve users based on date range
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');
    $data = User::whereBetween('created_at', [$startDate, $endDate]);

    //Retrieve users based on your filters
    if ($request->has('action')) {
        $action = $request->query('action');

        if ($action === 'block') {
            $data->where('block', 1);
        } elseif ($action === 'unblock') {
            $data->whereNull('block');
        } elseif ($action === 'active') {
            $data->whereNull('deactivate');
        } elseif ($action === 'deactivate') {
            $data->where('deactivate', 1);
        }
    }
    $data = $data->paginate();

    //json response
    return response()->json([
        'success' => true,
        'message' => "Successfully exported data",
        'data' => $data,
    ], 200);
}


}
