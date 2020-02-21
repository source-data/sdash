<?php

namespace App\Services;

use App\User;
use App\Models\Panel;
use BaconQrCode\Writer;
use Illuminate\Support\Str;
use App\Models\PanelAccessToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

class PanelAccessTokenService
{
    protected $panel;
    protected $baseUrl;

    public function __construct(Panel $panel)
    {
        $this->panel = $panel;
        $this->baseUrl = url('panel/' . $panel->id);
    }

    public function generateToken(): PanelAccessToken
    {
        $this->destroyToken();

        $token = Str::random(48);
        $qrFileName = $this->generateQr($this->baseUrl . '?token=' . $token, Str::random(16) . ".png");

        $panelAccessToken = new PanelAccessToken([
            'token'     => $token,
            'panel_id'  => $this->panel->id,
            'qr_image_name'   => $qrFileName
        ]);

        $panelAccessToken->save();

        return $panelAccessToken;
    }

    public function destroyToken(): Bool
    {
        $existingToken = $this->panel->accessToken;

        if ($existingToken) {
            $this->deleteQrImage();
            $existingToken->delete();
        }

        return TRUE;
    }


    protected function generateQr($url, $filename)
    {
        $QRrender = new ImageRenderer(
            new RendererStyle(350, 4),
            new ImagickImageBackEnd()
        );

        $QRwriter = new Writer($QRrender);

        $QRwriter->writeFile($url, storage_path('app/panels/' . $this->panel->id . DIRECTORY_SEPARATOR . $filename));

        return $filename;
    }

    public function deleteQrImage()
    {
        $qr = $this->panel->accessToken;

        Storage::delete($this->panel->id . DIRECTORY_SEPARATOR . $qr->qr_image_name);
    }
}
