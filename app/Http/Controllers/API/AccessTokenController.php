<?php

namespace App\Http\Controllers\API;

use API;
use App\User;
use App\Models\Panel;
use Illuminate\Http\Request;
use App\Models\PanelAccessToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use App\Services\PanelAccessTokenService as TokenMaker;
use Laravel\Passport\Bridge\AccessToken;

class AccessTokenController extends Controller
{

    public function create(Panel $panel)
    {

        if (Gate::allows('modify-panel', $panel)) {

            $tokenMaker = new TokenMaker($panel);

            $token = $tokenMaker->generateToken();

            return API::response(200, "Public access token created.", $token);
        } else {
            return API::response(401, "Permission denied.", []);
        }
    }


    public function destroy(Panel $panel)
    {

        if (Gate::allows('modify-panel', $panel)) {

            $tokenMaker = new TokenMaker($panel);

            $token = $tokenMaker->destroyToken();

            return API::response(200, "Public access token removed.", $token);
        } else {
            return API::response(401, "Permission denied.", []);
        }
    }


    public function qrCode(Panel $panel)
    {
        if (Gate::allows('view-panel', $panel)) {

            $accessToken = $panel->accessToken;
            Log::info($accessToken);

            if (!$accessToken) abort(404, "Panel has no public access token");

            $qr = $accessToken->qr_image_name;
            if (!$qr) abort(404, "Resource not found.");
            $filepath = config("filesystems.disks.panels.root") . DIRECTORY_SEPARATOR . $panel->id . DIRECTORY_SEPARATOR . $qr;
            if (file_exists($filepath)) {
                return response()->file($filepath);
            } else {
                abort(404, "Resource not found.");
            }
        } else {

            abort(401, "Access Denied");
        }
    }
}
