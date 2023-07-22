<?php
namespace App\Http\Controllers\Admin;
use App\Models\faq;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
class faqController extends Controller
{
  /** Add Faqs
     * @param question , Answer
     * @return json Response
     */
    public function addfaq(request $request)
    {
         // Validate fields
         $validator = Validator::make($request->all(), [
            'question' => 'required',
            'answer' => 'required',
          ]);
 
      // If validation fails
        if ($validator->fails()) {
         // JSON response
          return response()->json([
             'success' => false,
             'error' => $validator->errors()
           ], 400);
     }
     //add data to table
      $record=new faq;
      $record->question=$request['question'];
      $record->answer=$request['answer'];
      $record->save();
      
//json response
return response()->json([
'status'=>true,
'message'=>"succesfully add faq",
'data'=>$record,
],200);
    }

      /** Edit Faqs
     * @param question , Answer
     * @return json Response
     */

public function edit(request $request,$id)
{
       // Validate fields
       $validator = Validator::make($request->all(), [
        'question' => 'sometimes|required',
        'answer' => 'sometimes|required',
      ]);

  // If validation fails
    if ($validator->fails()) {
     // JSON response
      return response()->json([
         'success' => false,
         'error' => $validator->errors()
       ], 400);
}
   // Find id in table
   $faq=faq::find($id);
     // Edit data if provided
     if ($request->has('question')) {
        $faq->question = $request['question'];
     }
     if ($request->has('answer')) {
        $faq->answer = $request['answer'];
     }
        $faq->save();
 
        // JSON response
        return response()->json([
            'success' => true,
            'message' => 'Task edited successfully',
            'updated-data' => $faq,
        ],200);
    }

      /** faqlist_search
     * @param search
     * @return json Response
     */
    public function faqlist_search(request $request)
    {
      $limit= $request->input('limit',5);
       //search input with question
        if( $search = $request->input('search'))

        {
     
         $faq = faq::where('question', 'like', '%' . $search . '%')->select("id", "question","answer")->paginate();//search with question
        }
        else
        {
         $faq = faq::select("id", "question","answer")->paginate($limit); //faq list
        }

     //json response
         return response()->json([
             'success' => true,
             'message' => "successful",
             'data' => $faq
         ], 200);
     }
     
     
    }

