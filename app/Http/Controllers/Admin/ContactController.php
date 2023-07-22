<?php

namespace App\Http\Controllers\Admin;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Validator;


class contactController extends Controller
{

          /** ContactUs search nd listing
            * @param  ?search
            * @return json Response
            */
      public function contactlist_search(request $request)
    {
        $limit= $request->input('limit',5);
        //Search ContactUs with name
        if( $search = $request->input('search'))
        {
          $contact = ContactUs::where('name', 'like', '%' . $search . '%')->select("id", "name","email","phone_no","message","status")->paginate($limit);
        }

        // ContactUs Listing 
        else
        {
            $contact = ContactUs::select("id", "name","email","phone_no","message","status")->paginate($limit);
        }

        //  json response
         return response()->json([
             'success' => true,
             'message' => "successful",
             'data' => $contact
         ], 200);
     }
     

      /** ContactUs Change Status
     * @param  /id
     * @return json Response
     */
     public function chngestatus($id)
     {

    $contact=ContactUs::find($id);
       // if contact not found
       if(!$contact)
    {
     return response()->json([
      'success'=> false,
      'message'=>"contact not found",
    ],400);
    }

     // if contact status is open then chnges to resolved
       if($contact->status === 'open')
        {
          $contact->status = 'resolved';
        } 

       // if contact status is resolved then chnge to open
        else
       {
           $contact->status = 'open';
       }

        $contact->save();

      // json response
        return response()->json([
            'success'=>true,
            'message'=>"successful",
            'data'=> $contact,
           ],200);
     }

           /** ContactUs contact delete
     * @param  /id
     * @return json Response
     */
public function delete($id)
{
    // find data 
    $contactus=ContactUs::find($id);

    // if not found
    if(!$contactus)
    {
        return response()->json([
            'success'=>false,
            'message'=>"contact not found",
        ],400);
    }
    
    $contactus->delete(); //delete find data
    
    // json response
    return response()->json([
    'success'=>true,
    'message'=>"delete suucessfully",
    ],200);
    }

    /** Export Contact data
     * @param  start_date,end_date
     * @return json Response
     */
public function exportContact(Request $request)
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
    $data = ContactUs::whereBetween('created_at', [$startDate, $endDate])->paginate();

    // json response 
    return response()->json([
        'success' => true,
        'message' => "Successfully exported data",
        'data' => $data,
    ], 200);
}

}
    

