<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Panel;
use App\Repositories\Interfaces\PanelRepositoryInterface;

class DashboardController extends Controller
{

    protected $panelRepository;

    public function __construct(PanelRepositoryInterface $panelRepository)
    {
        $this->panelRepository = $panelRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        /* The ID of a panel can be passed in the `panel` query parameter to indicate that the detail view for that
         * panel should be opened. This detail view is a modal above the panel grid; thus, for UI consistency, the
         * panel whose details are on display should also be present in the panel grid. This, for example, allows the
         * user to close and then re-open the detail view for that panel without having to find the right page first.
         * 
         * The panel grid is paginated, therefore the page that the panel is in must be opened. This page can and does
         * change due to newly added, updated, or removed SmartFigures. The block below finds the correct page to open
         * and adds it as an additional (or updates the existing) `page` query parameter.
         */
        $idPanel = $request->query('panel');

        if ($idPanel) {
            $panel = Panel::find($idPanel);
            if (is_null($panel)) {
                // If there is no panel with the given ID, redirect to the dashboard without the query parameter
                return redirect()->route('home');
            }

            $user = auth()->user();
            $actualPageOfPanel = $this->panelRepository->pageOfPanel($panel, $user);

            if (is_null($actualPageOfPanel)) {
                // The user is not allowed to view the panel, so redirect to the dashboard without the query parameter
                return redirect()->route('home');
            }

            $pageOfPanel = $request->query('page');
            if ($pageOfPanel != $actualPageOfPanel) {
                // redirect to the dashboard with the panel & and the correct page as query parameters
                return redirect()->route('home', ['panel' => $idPanel, 'page' => $actualPageOfPanel]);
            }
        }

        return view('dashboard');
    }
}
