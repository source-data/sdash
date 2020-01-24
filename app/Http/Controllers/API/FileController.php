<?php

namespace App\Http\Controllers\API;

use API;
use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Panel;
use App\User;
use App\Repositories\FileRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{

    protected $fileRepository;

    public function __construct()
    {
           $this->fileRepository = new FileRepository;
    }

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
    public function store(Panel $panel, Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'file'  => ['required_without:url', 'max:4096'],
            'url'   => ['required_without:file', 'url']
        ]);

        if(Gate::allows('access-panel', $panel)) {

            if($request->input('url')){
                $fileCreated = File::create([
                    'panel_id'  => $panel->id,
                    'url'       => $request->input('url'),
                    'type'      => 'url'
                ]);

                return API::response(200, "External URL stored.", File::find($fileCreated->id));
            }

            if($file = $request->file('file')){

                return API::response(200, "File uploaded successfully", $this->fileRepository->storePanelFile($panel, $file));

            }

            return API::response(400, "Malformed upload attempt - did you submit a file or url?", []);

        } else {
            abort(401,"Access denied.");
        }



    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Files  $files
     * @return \Illuminate\Http\Response
     */
    public function show(Files $files)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Files  $files
     * @return \Illuminate\Http\Response
     */
    public function edit(Files $files)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Files  $files
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, File $file)
{
        $user = auth()->user();
        $panel = $file->panel;
        $request->validate([
            'description'  => ['required', 'max:255']
        ]);

        if( !Gate::allows('access-panel', $panel)) return API::response(401, "Access denied.", []);

        $file->update([
            'description' => strip_tags($request->input("description"))
        ]);

        return API::response(200, "File updated", $file);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Files  $files
     * @return \Illuminate\Http\Response
     */
    public function destroy(File $file)
    {

        $user = auth()->user();
        $panel = $file->panel;

        if( !Gate::allows('access-panel', $panel)) return API::response(401, "Access denied.", []);

        if($this->fileRepository->archiveAndRemove($file)) {
            return API::response(200, "File removed.", []);
        }

        return API::response(500, "Could not remove requested file", []);
    }



    public function download(File $file)
    {

        $user = auth()->user();
        $panel = $file->panel;

        if( !Gate::allows('access-panel', $panel)) throw new AuthorizationException("Access Denied");

        $fs = Storage::getDriver();

        $path = $panel->save_path . 'attachments' . DIRECTORY_SEPARATOR . $file->filename;
        $fileStream = $fs->readStream($path);

        return response()->stream(
            function() use($fileStream){
                while(ob_get_level() > 0) ob_end_flush();
                fpassthru($fileStream);
            },
            200,
            [
                'Content-Type'  =>  $file->mime_type,
                'Content-disposition' => 'attachment; filename="' . $file->original_filename .'"',
            ]
        );




    }
}
