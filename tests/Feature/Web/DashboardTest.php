<?php

namespace Tests\Feature\Web;

use App\User;
use App\Models\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $users;
    protected $panels;

    public function setUp(): void
    {
        parent::setUp();

        /* Users and panels are stored in arrays because data provider functions are executed before the first call to
         * setUp(), thus the data providers cannot access any variables here. Instead, they return indexes for the user
         * and panel arrays.
         */

        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $this->users = [$user1, $user2];

        $this->panels = array_merge(
            /* These panels with the IDs 1-30 are only visible to user 1. 1-20 reside on page 1, 21-30 on page 2 of the
             * dashboard for that user.
             */
            factory(Panel::class, 30)->create(['user_id' => $user1->id])->toArray(),
            /* These panels with the IDs  are visible to both users & publicly.
             * For user 1, 61-70 are on page 2 and 71-90 on page 3. For user 3 & anonymous visitors, 61-80 are on
             * page 1 and 81-90 on page 2.
             */
            factory(Panel::class, 30)->create(['user_id' => $user2->id, 'is_public' => true])->toArray(),
        );
    }

    /**
     * On which page does the user see the panel?
     * 
     * Returns an array with the dataset description as the key. Values are arrays that indicate which user views the
     * dashboard, which panel they want to view, and on which page that panel is for that user.
     * 
     * User and panel are given as indexes into the `users` and `panels` variables of this class because these
     * variables can't be accessed in this method yet (see the documentation for setUp()).
     * 
     * Passing `null` as user indicates an anonymous user.
     */
    public function onWhichPageDoesTheUserSeeThePanel()
    {
        return [
            // 'dataset description' => [idxUser || null, idxPanel, pageNumber]
            'anonymous user & public panel on page 1' => [null, 49, 1],
            'anonymous user & public panel on page 2' => [null, 50, 2],
            'user & private panel on page 1'          => [0,    19, 1],
            'user & private panel on page 2'          => [0,    20, 2],
            'user & public panel on page 2'           => [0,    39, 2],
            'user & public panel on page 3'           => [0,    40, 3],
        ];
    }

   /**
     * Verifies that requests with an unknown or invalid panel id are redirected to the plain dashboard URI.
     *
     * @return void
     *
     * @dataProvider invalidPanelIds
     */
    public function testInvalidPanelIdsAreIgnored($idPanel)
    {
        $this->assertDashboardRedirects($this->users[1], "/?panel=$idPanel", '/');
    }
    public function invalidPanelIds()
    {
        return [
            'Nonsense string that doesn\'t parse into a number' => ['asdf'],
            'String that doesn\'t parse into a number but is part of panel titles & descriptions' => ['lorem'],
            'ID of panel that doesn\'t exist' => [1234],
        ];
    }

    public function testPrivatePanelIsIgnored()
    {
        $idPanel = $this->panels[0]['id'];
        $this->assertDashboardRedirects(null, "/?panel=$idPanel", '/');
        $this->assertDashboardRedirects($this->users[1], "/?panel=$idPanel", '/');
    }

   /**
     * Verifies that requests with a valid panel id are redirected to right page.
     *
     * @return void
     *
     * @dataProvider onWhichPageDoesTheUserSeeThePanel
     */
    public function testRequestsWithValidPanelIdAreRedirectedToRightPage($idxUser, $panelIdx, $expectedPage)
    {
        $user = is_null($idxUser) ? null : $this->users[$idxUser];
        $idPanel = $this->panels[$panelIdx]['id'];
        $uri = "/?panel=$idPanel";
        $expectedRedirect = $uri . "&page=$expectedPage";
        $this->assertDashboardRedirects($user, $uri, $expectedRedirect);
    }

    /**
      * Verifies that requests with a valid panel id & wrong page are redirected to right page.
      *
      * @return void
      *
      * @dataProvider onWhichPageDoesTheUserSeeThePanel
      */
    public function testRequestsWithValidPanelIdAndWrongPageAreRedirectedToRightPage($idxUser, $panelIdx, $expectedPage)
    {
        $user = is_null($idxUser) ? null : $this->users[$idxUser];
        $idPanel = $this->panels[$panelIdx]['id'];
        $wrongPage = $expectedPage + 1;
        $uri = "/?panel=$idPanel&page=$wrongPage";
        $expectedRedirect = "/?panel=$idPanel&page=$expectedPage";
        $this->assertDashboardRedirects($user, $uri, $expectedRedirect);
    }

    protected function assertDashboardRedirects($actor, $uri, $expectedRedirect)
    {
        if (! is_null($actor))
        {
            $request = $this->actingAs($actor, 'sanctum');
        } else {
            $request = $this;
        }
        $response = $request->get($uri);
        $response->assertRedirect($expectedRedirect);
    }
}
