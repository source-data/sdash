<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
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
    public function index(Tag $tag, Request $request)
    {
        $request->validate([
            "name"  =>  ['max:40']
        ]);

        if ($request->query('name')) {

            $tagList = Tag::where(\DB::raw("content"), 'like', '%' . $request->query('name') . '%')->limit(40)->get();

            return ($tagList->isEmpty()) ? API::response(204, "No matching records found", []) : API::response(200, "A list of tags", $tagList);
        }

        return API::response(200, "A list of all tags.", Tag::all());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function publicIndex(Tag $tag, Request $request)
    {
        $request->validate([
            "name"  =>  ['max:40']
        ]);

        if ($request->query('name')) {

            $tagList = Tag::whereHas('panels', function ($query) {
                    $query->where('is_public', true);
                })
                ->where(\DB::raw("content"), 'like', '%' . $request->query('name') . '%')
                ->limit(40)->get();

            return ($tagList->isEmpty()) ? API::response(204, "No matching records found", []) : API::response(200, "A list of tags", $tagList);
        }
        return API::response(204, "No matching records found", []);
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
     * Store a newly created tag resource in storage.
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

        if (Gate::allows('modify-panel-tags', $panel)) {

            $newTag = Tag::firstOrCreate([
                'content' => strip_tags($request->input('name'))
            ]);

            $panel->tags()->attach($newTag, [
                'origin' => strip_tags($request->input('origin')),
                'type' => strip_tags($request->input('type')),
                'role' => strip_tags($request->input('role')),
                'category' => strip_tags($request->input('category'))
            ]);

            return API::response(200, "Tag added to panel", $panel->tags()->withPivot(['id', 'origin', 'role', 'type', 'category'])->get());
        } else {
            return API::response(401, "Permission denied.", []);
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
     * Connect the supplied array of tag IDs to the given Panel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Panel  $panel
     * @return \Illuminate\Http\Response
     */
    public function addToPanelTags(Request $request, Panel $panel)
    {
        $request->validate([
            'tags.*.id' => ['required', 'numeric', 'exists:tags,id'],
            'tags.*.meta.category' => ['nullable', 'string'],
            'tags.*.meta.role' => ['nullable', 'string'],
            'tags.*.meta.type' => ['nullable', 'string'],
            'tags.*.meta.origin' => ['required', 'string', 'in:user,smarttag'],
        ]);

        if (!Gate::allows('modify-panel-tags', $panel)) return API::response(401, "Permission denied.", []);

        $newTags = $request->get('tags');
        $existingTags = $panel->tags()->withPivot(['category', 'origin', 'role', 'type'])->get()->toArray();
        Log::debug($existingTags);

        // loop through submitted tags and only attach
        // tags that are not already attached
        foreach ($newTags as $newTag) {
            if (!array_filter($existingTags, function ($existingTag) use ($newTag) {
                return ($existingTag['id'] == $newTag['id'] &&
                    $existingTag['meta']['role'] == $newTag['meta']['role'] &&
                    $existingTag['meta']['category'] == $newTag['meta']['category'] &&
                    $existingTag['meta']['origin'] == $newTag['meta']['origin'] &&
                    $existingTag['meta']['type'] == $newTag['meta']['type']);
            })) {
                $panel->tags()->attach($newTag['id'], [
                    'origin' => strip_tags($newTag['meta']['origin']),
                    'type' => strip_tags($newTag['meta']['type']),
                    'role' => strip_tags($newTag['meta']['role']),
                    'category' => strip_tags($newTag['meta']['category'])
                ]);
            }
        }

        return API::response(200, "Tags added to panel", $panel->tags()->withPivot(['id', 'origin', 'role', 'type', 'category'])->get());
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

        if (Gate::allows('modify-panel-tags', $panel)) {

            $pivotId = $request->input("relationship_id");

            $panel->tags()->where('id', $tag->id)->wherePivot('id', $pivotId)->detach($tag->id);

            return API::response(200, "Tag removed from panel", $panel->tags()->withPivot(['id', 'origin', 'role', 'type', 'category'])->get());
        } else {
            return API::response(401, "Permission denied.", []);
        }
    }
}
