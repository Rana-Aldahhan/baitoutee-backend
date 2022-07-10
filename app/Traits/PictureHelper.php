<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait PictureHelper{
    protected function storePicture($request, $imageKey,$path){
        //$path = 'mealsImages'
        //imageKey = 'image'
        $imagePath = null;
        if ($request->hasFile($imageKey)) {
            // Get filename with the extension
            $filenameWithExt = $request->file($imageKey)->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file($imageKey)->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            // Upload Image
            $imagePath = $request->file($imageKey)->storeAs('public/'.$path, $fileNameToStore);
            //$profilePath=asset('storage/profiles/'.$fileNameToStore);
            $imagePath = '/storage/'.$path.'/' . $fileNameToStore;
        }
        return $imagePath;

    }
}
