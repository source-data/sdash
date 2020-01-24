<?php

namespace App\Http\Controllers\API;

use API;
use App\User;
use App\Models\Panel;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Repositories\ImageRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\PdfToImage\Pdf as PdfConverter;
use App\Repositories\PanelRepository as PanelQuery;
use Intervention\Image\Facades\Image as ImageService;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function show(Image $image)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function edit(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Image $image)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function destroy(Image $image)
    {
        //
    }

    /**
     * Stream the image file for the given panel ID
     *
     * @param Panel $panel
     * @return Response
     */
    public function showPanelImage(Panel $panel)
    {
        if(Gate::allows('view-panel', $panel)) {

            $image = $panel->image()->first();
            if(!$image) abort(404, "Resource not found.");
            $filepath = config("filesystems.disks.panels.root") . DIRECTORY_SEPARATOR . $image->panel_id. DIRECTORY_SEPARATOR . $image->preview_filename;
            if(file_exists($filepath)){
                return response()->file($filepath);
            }else{
                abort(404, "Resource not found.");
            }


        }else{
            abort(401,"Access denied.");
        }
    }


    public function showPanelThumbnail(Panel $panel)
    {
        if(Gate::allows('view-panel', $panel)) {

            $image = $panel->image()->first();
            if(!$image) abort(404, "Resource not found.");
            $filepath = config("filesystems.disks.panels.root") . DIRECTORY_SEPARATOR . $panel->id . DIRECTORY_SEPARATOR . 'thumbnails'. DIRECTORY_SEPARATOR . $image->preview_filename;
            if(file_exists($filepath)){
                return response()->file($filepath);
            }else{
                abort(404, "Resource not found.");
            }


        }else{
            abort(401,"Access denied.");
        }
    }
}
