<?php
namespace App\Http\Controllers\user;
use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Mail\userverifymail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\OTP;


class AuthUserController extends Controller
{
    public function signup(Request $request)
    {
    //    validate fields
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|max:16',
            'confirm_password' => 'required|same:password',
        ]);
    //   if validation failed
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()
            ], 400);
        }

       $password= bcrypt($request->password);

        $otp = mt_rand(100000, 999999); //Generate OTP
        // save data in table
   $record=new User;
   $record->name=$request['name'];
   $record->email=$request['email'];
   $record->password=$password;
   $record->verification_code=$otp;
   $record->otp_created_at = Carbon::now();
   $record->save();

        

        // Send verification email to the user
        $details = [
            'name' => $request->name,
            'email' => $request->email,
            'title' => 'Verification Code',
            'otp' => $otp,
        ];

        Mail::to($request->email)->send(new userverifymail($details));
        //   json response
        return response()->json([
            'success' => true,
            'message' => 'successfully sent otp to your mail.now  enter the otp for verify your account'
        ], 201);
    }

    /** Verify email
    * @param email, verification_code
    * @return json Response
    */
    public function verifyEmail(Request $request)
    {
        // validate fields
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'verification_code' => 'required',
        ]);
    //  if validation failed
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()
            ], 400);
        }
    
        $user = User::where('email', $request->email)->first(); // verify email

    //  if email not found
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid email.'
            ], 400);
        }
    // if enter wrong otp @return response
        if ($user->verification_code !== $request->verification_code) {
            return response()->json([
                'success' => false,
                'error' => 'you enter wrong verification code.'
            ], 400);
        }
    // otp expire time
        $otpExpiration = Carbon::parse($user->otp_created_at)->addminutes(10);
        if (Carbon::now()->gt($otpExpiration)) {
            $user->verification_code = null;
            $user->otp_created_at = null;
            $user->save();
            // json response
            return response()->json([
                'success' => false,
                'error' => 'OTP has expired. Please request a new one.'
            ], 400);
        }
    
        $token = $user->createToken('authToken')->plainTextToken;
    
        $user->email_verified_at = now(); 
        $user->save();
    

    // json response
        return response()->json([
            'success' => true,
            'message' => 'Email verification successful.'
        ], 200);
    }


   /** User Login
    * @param emails, password
    * @return json Response
    */
public function userlogin(request $request){
    // validate fields
    $validator = Validator::make($request->all(), [
     'email' => 'required|exists:users',
     'password' => 'required',
 ]);
 
 // if validation failed
 if ($validator->fails()) {
     return response()->json([
         'success' => false,
         'error' => $validator->errors(),
     ], 400);
 }  
 $user=user::where('email',$request->email)->first();//verify email
 
 //if data not found @return response
 if(!$user)
 {
     //json response
     return response()->json([
 'success'=>false,
 'message'=>"incorrect email"
     ],401);
 }
 
 //cheak pasword
 if (!Hash::check($request->password, $user->password)) {
     //json response
     return response()->json([
         'success'=>true,
         'message' => 'Incorrect password'
     ], 401);
 }
 //create token
 $token = $user->createToken('authToken')->plainTextToken;
 
 //json response 
 return response()->json([
 'success'=>true,
 'message'=>"log in successfully",
 'token'=>$token
 ],200);
 }
 public function userlogout(request $request)
 {
    $request->user()->currentAccessToken()->delete();

    //   json response
        return response()->json([
            'id' => $request->user()->id,
            'success' => true,
            'message' => 'User logged out successfully'  
        ],200);
    }
 }

