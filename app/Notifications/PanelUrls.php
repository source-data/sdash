<?php

namespace App\Notifications;

use App\Models\Panel;

trait PanelUrls {

    protected $panel;

    protected function panelDetailUrl()
    {
        return url('/?panel=' . $this->panel->id);
    }

    protected function panelUrl()
    {
        return url('/panel/' . $this->panel->id);
    }
}
