<?php
namespace App\Http\Traits;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait ImageTrait
{
    
    public function uploadImage(Request $request)
    // update gallery image
    {
        // validation
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
           
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
             // upload image

        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('uploads',$imageName, 's3'); 
         $url = Storage::disk('s3')->url($path);
            
             return response()->json([
            'success' => 'true',
            'message' => 'data upload successfully',
            'path'=> $url,
            'image'=> $imageName]);
    }

}




