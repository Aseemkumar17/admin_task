<?php
namespace App\Http\Controllers\Admin;
use App\Models\User;
use Illuminate\Routing\Controller;
use App\Jobs\sendnotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class notificationController extends Controller
{
     /** Send Notification
     * @param subject ,,description, selected_user=allusers|selectedusers
     * @return json Response
     */
    public function notification(request $request)
    {
        //validate fields 
        $validator= Validator::make($request->all(),[
            'subject' => 'required',
            'description' => 'required',
            'select_user'=> 'required|in:allusers,selectedusers',
        ]);
        #IF VALIDATION FAIL SEND RESPONSE
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'error' => $validator->errors(),
            ], 400);
        }

$subject=$request['subject'];
$description=$request['description'];
$select_user=$request['select_user'];
//if send  to all user 
if($select_user === "allusers")
{
    $recipient = User::pluck('email')->all();
}
//selected users
elseif($select_user === "selectedusers")
{
    $selected = $request->input('selected_users', []);
    $recipient= User::whereIn('id', $selected)->pluck('email');
}
foreach ($recipient as $user) {


    dispatch(new sendnotification($user, $subject, $description));//dispatch job
    //json response
    return response()->json([
        'success'=>true,
        'message'=>"mail successfully send"
    ]);
}
    }
}
