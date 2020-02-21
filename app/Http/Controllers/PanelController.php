<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\Panel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class PanelController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Panel  $panel
     * @return \Illuminate\Http\Response
     */
    public function show(Panel $panel, Request $request)
    {
        $validator = Validator::make($request->all(),
            ['string', 'exists:panel_access_tokens,token']
        );

        if($validator->fails()) abort(401, "Access Denied");

        $token = $request->get('token', null);

        if (Gate::allows('view-single-panel', [$panel, $token])) {
            $panel->load(['groups', 'tags'  => function ($query) {
                $query->withPivot('id', 'origin', 'role', 'type', 'category');
            }, 'user', 'files']);
            return View::make('singlepanel', $panel);
        } else {

            abort(401, "Access Denied");
            
        }
    }
}
