<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Models\Panel;
use App\Models\Tag;
use API;

class TagController extends Controller
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
    public function store(Request $request, Panel $panel)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:2'],
            'category' => ['nullable', 'string'],
            'role' => ['nullable', 'string'],
            'type' => ['nullable', 'string'],
            'origin' => ['required', 'string', 'in:user,smarttag'],
        ]);

        if(Gate::allows('modify-panel', $panel)) {

            $newTag = Tag::firstOrCreate([
                'content' => strip_tags($request->input('name'))
            ]);

            $panel->tags()->attach($newTag,[
                'origin' => strip_tags($request->input('origin')),
                'type' => strip_tags($request->input('type')),
                'role' => strip_tags($request->input('role')),
                'category' => strip_tags($request->input('category'))
                ]);

            return API::response(200, "Tag removed from panel", $panel->tags()->withPivot(['id', 'origin','role','type','category'])->get());

        }
        else {
            return API::response(401, "Permission denied.",[]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function edit(Tag $tag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tag $tag)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        //
    }


    public function detachPanel(Request $request, Panel $panel, Tag $tag)
    {
        $request->validate([
            'relationship_id' => ['required', 'integer']
        ]);

        if(Gate::allows('modify-panel', $panel)) {

            $pivotId = $request->input("relationship_id");

            $panel->tags()->where('id', $tag->id)->wherePivot('id', $pivotId)->detach($tag->id);

            return API::response(200, "Tag removed from panel", $panel->tags()->withPivot(['id', 'origin','role','type','category'])->get());

        }
        else {
            return API::response(401, "Permission denied.",[]);
        }


    }
}
