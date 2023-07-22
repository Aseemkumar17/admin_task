<?php

namespace App\Http\Controllers\Common;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;

use Intervention\Image\Facades\Image;

class ImageUploadController extends Controller
{
  /** Upload image0
     * @param image
     * @return json Response
     */
 public function uploadImage(Request $request)
    // update gallery image
    {
        // validation
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
           
        ]);
          //if validation fails
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
             // upload image

        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();//random name
        $path = $image->storeAs('original',$imageName, 's3'); 
         $url = Storage::disk('s3')->url($path);
            

          // create medium image
    $mediumImage = Image::make($image)->resize(600, 600); //resize image
    $mediumImageName = 'medium' . $imageName; 
    $mediumPath = 'medium/' . $mediumImageName;
    Storage::disk('s3')->put($mediumPath, (string)$mediumImage->encode()); //store image
    $mediumUrl = Storage::disk('s3')->url($mediumPath);//Image path

      // create thumbnail image
      $thumbnailImage = Image::make($image)->resize(150, 150);  //resize image
      $thumbnailImageName = 'thumbnail' . $imageName;
      $thumbnailPath = 'thumbnail/' . $thumbnailImageName;
      Storage::disk('s3')->put($thumbnailPath, (string)$thumbnailImage->encode()); //store image
      $thumbnailUrl = Storage::disk('s3')->url($thumbnailPath); //Image path

         //json response
             return response()->json([
            'success' => 'true',
            'message' => 'data upload successfully',
            'original_path' => $url,
            'medium_path' => $mediumUrl,
            'thumbnail_path' => $thumbnailUrl,
            'original_image' => $imageName,
            'medium_image' => $mediumImageName,
            'thumbnail_image' => $thumbnailImageName,
        ],200);
    }
 

  
}
    
    


    