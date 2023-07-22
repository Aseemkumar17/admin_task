<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Routing\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Gallery;
use App\Models\ProductCategoryId;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class productcontroller extends Controller
{
   /** Add Product
     * @param product_name,description,image,price,sale_price,discount,category
     * @return json Response
     */
    public function addProduct(request $request)
    {
   // validation fields
   $validator = Validator::make($request->all(),
   [
    'product_name' => 'required',
    'description' => 'required',
    'image' => 'required',
    'price'=>'required',
    'sale_price'=>'required',
    'discount' => 'required',
    'category' => 'required|exists:categories,id',
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
  //add data in product table
   $record=new Product;
   $record->product_name = $request['product_name'];
   $record->description = $request['description'];
   $record->image = $request['image'];
   $record->price = $request['price'];
   $record->sale_price=$request['sale_price'];
   $record->discount = $request['discount'];
   $record->save();
   $category=$request['category'];
//add category
   foreach($category as $categories)
   {
       $product_category=new ProductCategoryId;
       $product_category->product_id= $record->id;
       $product_category->category_id = $categories;
       $product_category->save();
     }
 // json response
    return response()->json([
      'success'=> true,
      'message'=>"Task successfully add",
      'data'=>$record
    ],200);
    }


    /** Product Listing ,filters nd search
     * @param filter category[], url?search=name?price=low_to_high,high to low
     * @return json Response
     */
  public function productList(Request $request)
{
    $categoryIds = $request->input('category');
    $price = $request->input('price');
    $limit= $request->input('limit',5);
    $products = Product::query();
       //search products 
    if ($search = $request->input('search')) {
        $products->where(function ($query) use ($search) {
            $query->where('product_name', 'like', '%' . $search . '%');
                
        });
    }
        //category filter
    if ($categoryIds) {
        $products->whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('categories.id', $categoryIds);
        });
    }
      //Price filter 
    if ($price === 'low_to_high') {
        $products->orderBy('price', 'asc');
    } elseif ($price === 'high_to_low') {
        $products->orderBy('price', 'desc');
    }

    $products = $products->with('categories')->select("id", 'product_name','image',  'price')->paginate($limit);
// if result is empty
    if ($products->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => "Products empty"
        ]);
    }
//json response
    return response()->json([
        'success' => true,
        'message' => "Successful",
        'data' => $products
    ], 200);
}

    
       /** Edit products
     * @param url/id 
     * @return json Response
     */
    
    public function editproduct(Request $request, string $id)
    {  
     // Validate fields
         $validator = Validator::make($request->all(), [
            'product_name' => 'sometimes|required',
            'description' => 'sometimes|required',
            'image' => 'sometimes|required',
            'price'=>'sometimes|required',
        'sale_price'=>'sometimes|required',
        'discount' => 'sometimes|required',
    
        'category' => 'sometimes|required|exists:categories,id',
         ]);
 
      // If validation fails
        if ($validator->fails()) {
         // JSON response
          return response()->json([
             'success' => false,
             'error' => $validator->errors()
           ], 400);
     }
 
       // Find task in model
        $product = Product::with('categories')->find($id);
 
       // If task not found
       if (!$product) {
         // JSON response
         return response()->json([
             'success' => false,
             'message' => "Task not found",
           ], 400);
     }
    
     // Edit data if provided
     if ($request->has('product_name')) {
        $product->product_name = $request['product_name'];
     }
     if ($request->has('description')) {
        $product->description = $request['description'];
     }
     if ($request->has('image')) {
        $product->image =  $request['image'];
     }
     if ($request->has( 'price')) {
        $product->price =  $request['price'];
    } 
     if ($request->has('sale_price')) {
        $product->sale_price=  $request['sale_price'];
    } 
     if ($request->has('discount')) {
        $product->discount=  $request['discount'];
    }

    if ($request->has('category')) {
        $category = $request->input('category');
        ProductCategoryId::where('product_id', $id)->delete();
         
            $Product_category = new ProductCategoryId();
            $Product_category->product_id = $id;
            $Product_category->category_id = $category;
            $Product_category->save();
        
}
    
  
    $product->save();
 
     // JSON response
     return response()->json([
         'success' => true,
         'message' => 'Task edited successfully',
         'updated-data' => $product,
     ],200);
 }



    /** Product Show
     * @param url/id 
     * @return json Response
     */
public function product_show($id)

{
    $product= Product::find($id);// find Product
  
    //if not founds
    if(empty(($product)) )
    {
      //json response
return response()->json([
    'status'=>'false',
"message"=>"record not found"
],404);
    }
    else{

$products =  Product::where('id', $id)->with('categories','gallery')->select('id','product_name','description','price','discount','image','visibility')->paginate();
 //json response
    return response()->json  ([
        "success" => true,
        "message" => "successfully",
        "data" => $products],200);
    }
}

  /** Export Product data
     * @param start_date,end_date
     * @return json Response
     */
public function exportProduct(Request $request)
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
    $data = product::whereBetween('created_at', [$startDate, $endDate])->paginate();

    //json response
    return response()->json([
        'success' => true,
        'message' => "Successfully exported data",
        'data' => $data,
    ], 200);
}


  /** Add Products gallery
     * @param @body image ,url/id 
     * @return json Response
     */
public function AddGallery($id , request $request)
{
$product=Product::find($id);//Find Product

//if product is not found then show error msg
if(!$product)
{
    //json response
    return response()->json([
'success'=> false,
'message'=> "Product not found"
    ],404);
}

else
{
    //Add images in gallery model
    $gallery= new Gallery;
    $gallery->product_id=$id;// add product_id
    $gallery->images=$request['image'];
    $gallery->save();
}

return response()->json([
'success'=>true,
'message'=>"Image add successfully",
'data'=>$gallery
],200);

}

}


