<?php

namespace App\Http\Controllers;

use App\Jobs\VideoDecode;
use App\Models\Movie;
use Exception;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function store(Request $request)
    {

        try {

            Movie::create($request->all());

            if ($request->hasFile('video_path')) {
                $file = $request->file('video_path');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move('videos/movies', $fileName);
            }

            VideoDecode::dispatch($request->name, $fileName);

            return response()->json('Movie added sucessfully', 201);
        } catch (Exception $e) {

            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
