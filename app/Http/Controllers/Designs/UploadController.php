<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use App\Jobs\UploadImage;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request){
        $this->validate($request,[
            'image' => ['required', 'mimes:jpeg,gif,png,bmp', 'max:2048'],
        ]);

        //get image
        $image = $request->file('image');

        //get image path
        $image_path = $image->getPathname();

        //get image name and replace any space with _
        // My new image.png = my_new_image.png

        $filename =time().'_'.preg_replace('/\s+/','_',strtolower($image->getClientOriginalName()));

        //move image to tmp storage
        $tmp = $image->storeAs('uploads/original', $filename, 'tmp');

        $design = auth()->user()->create([
            'image' => $filename,
            'disk' => config('site.upload_disk')
        ]);

        // create a job to handle the image manipulation
        $this->dispatch(new UploadImage($design));

        return response()->json($design, 200);

    }
}
