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
use App\Repositories\Interfaces\ImageRepositoryInterface;

class ImageController extends Controller
{

    /**
     * Stream the image file for the given panel ID
     *
     * @param Panel $panel
     * @return Response
     */
    public function showPanelImage(Panel $panel, Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            ['string', 'exists:panel_access_tokens,token']
        );

        if ($validator->fails()) abort(401, "Access Denied");

        $token = $request->get('token', null);

        if (Gate::allows('view-single-panel', [$panel, $token])) {
            return $this->returnImagePath($panel);
        } else {
            abort(401, "Access denied.");
        }
    }

    public function showPublicPanelImage(Panel $panel)
    {
        return $this->returnImagePath($panel);
    }


    public function showPanelThumbnail(Panel $panel)
    {
        if (Gate::allows('view-panel', $panel)) {
            return $this->returnThumbnailPath($panel);
        } else {
            abort(401, "Access denied.");
        }
    }

    protected function returnImagePath(Panel $panel)
    {
        $image = $panel->image()->first();
        if (!$image) abort(404, "Resource not found.");
        $filepath = config("filesystems.disks.panels.root") . DIRECTORY_SEPARATOR . $image->panel_id . DIRECTORY_SEPARATOR . $image->preview_filename;
        if (file_exists($filepath)) {
            return response()->file($filepath);
        } else {
            abort(404, "Resource not found.");
        }
    }

    protected function returnThumbnailPath(Panel $panel)
    {
        $image = $panel->image()->first();
        if (!$image) abort(404, "Resource not found.");
        $filepath = config("filesystems.disks.panels.root") . DIRECTORY_SEPARATOR . $panel->id . DIRECTORY_SEPARATOR . 'thumbnails' . DIRECTORY_SEPARATOR . $image->preview_filename;
        if (file_exists($filepath)) {
            return response()->file($filepath);
        } else {
            abort(404, "Resource not found.");
        }
    }
}
