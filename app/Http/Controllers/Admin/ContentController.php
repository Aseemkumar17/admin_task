<?php

namespace App\Http\Controllers\Admin;
use App\Models\Content_page;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
class contentcontroller extends Controller
{

    /** Add Content
     * @param title, description,image
     * @return json Response
     */
public function addcontent(request $request)
{
        // Validate Fields
        $validator= Validator::make($request->all(),[
            'title' => 'required|in:About-us,Term & Conditions,privacy-policy',
            'description' => 'required',
            'image'=> 'required',
        ]);
        #IF VALIDATION FAIL SEND RESPONSE
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'error' => $validator->errors(),
            ], 400);
        }
    //    Create data 
        $record=new Content_page;
       $record->pagetitle=$request['title'];
       $record->description=$request['description'];
       $record->images=$request['image'];
       $record->pagename= Str::slug($request->input('title'), "-");
       $record->save();
 
    //    json response
       return response()->json([
          'success'=>true,
          'message'=>"successfully create content",
          'data'=>$record,
          ],200);
        }



    /** Show  Content_page
     * @param /id
     * @return json Response
     */
      public function show($id)
 {
    $content=Content_page::find($id); // find content in database

    // If Content Not Found
     if(!$content)
  {
    // json response if failed
     return response()->json([
           'success'=>false,
           'message'=>'content not found',
           'data'=>$content,
         ],400);
}

// json response if success
      return response()->json([
        'success'=>true,
        'message'=>'successful',
        'data'=>$content,
        ],200);
       }

  

    /** Content Edit
     * @param /id , page_name,title,image,description
     * @return json Response
     */
 public function edit(request $request ,$id)
{
          // Validate fields
         $validator = Validator::make($request->all(), [
            'page_name' => 'sometimes|required',
            'title' => 'sometimes|required',
            'image' => 'sometimes|required',
            'description'=>'sometimes|required',
         ]);
 
      // If validation fails
        if ($validator->fails()) {

         // JSON response
          return response()->json([
             'success' => false,
             'error' => $validator->errors()
           ], 400);
     }
 
       // Find Content in model
        $Content =Content_page::find($id);
 
       // If task not found
       if (!$Content) {
         // JSON response
         return response()->json([
             'success' => false,
             'message' => "Task not found",
           ], 400);
     }
    
     // Edit data if provided
     if ($request->has('page_name')) {
        $Content->pagename = $request['page_name'];
     }
     if ($request->has('description')) {
        $Content->description = $request['description'];
     }
     if ($request->has('image')) {
        $Content->images=$request['image'];
     }
     if ($request->has( 'title')) {
        $Content->pagetitle =  $request['title'];
    } 

  
    $Content->save();
 
     // JSON response
     return response()->json([
         'success' => true,
         'message' => 'Task edited successfully',
         'updated-data' => $Content,
     ],200);
 }

    /** Content List
     * @param ?search
     * @return json Response
     */
 public function contentlist(request $request)
{
    $limit= $request->input('limit',5);
    // search content with pagename
   if( $search = $request->input('search'))
   {

    $content = Content_page::where('pagename', 'like', '%' . $search . '%')->select("id", "pagetitle", "description", "created_at")->paginate($limit);
   }

// content listing all data show
   else
   {
    $content = Content_page::select("id", "pagetitle", "description", "created_at")->paginate($limit);
   }
    $data = [];

    foreach ($content as $item) {
        $date = date('j/F/y', strtotime($item->created_at)); //change date form
        
        // data array
        $data[] = [
            'id' => $item->id,
            'pagetitle' => $item->pagetitle,
            'description' => $item->description,
            'created_at' => $date,
        ];
    }
// json response
    return response()->json([
        'success' => true,
        'message' => "successful",
        'data' => $data
    ], 200);
}


}
 


