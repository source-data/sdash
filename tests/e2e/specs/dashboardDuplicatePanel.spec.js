describe("SDash test the duplicate panel function", function() {

  test("A logged in user can duplicate their own panel", function(browser) {
    browser.page.login()
    .navigate()
    .login();

    let dashboard = browser.page.dashboard();
    let sidebar = dashboard.section.filterBar;
    dashboard.navigate();

    // if the sidebar is not expanded, expand it.
    browser.isVisible(sidebar.elements.filterBarContent, function(result) {
      if(false === result.value) {
        sidebar.toggleFilterBar();
      }
    });

    // Show the logged-in user's panels only
    sidebar.showMyPanels();

    // close the sidebar
    sidebar.toggleFilterBar();
    dashboard.waitForElementPresent(dashboard.section.panelGrid, 1000, 'Panel grid is present')
      .waitForElementPresent(dashboard.section.panelGrid.elements.firstPanelLink, 2000, 'First panel link is present')
      .click(dashboard.section.panelGrid.elements.firstPanelLink);

    // check that the duplicate panel button exists
    dashboard.section.panelDetailView.assert.elementPresent("@duplicatePanelButton", "The duplicate panel button is correctly shown on a user's own panels");

    var originalTitle;
    dashboard.section.panelDetailView.getText('article.panel-detail > header > h1', function(result){
      originalTitle = result.value;
      console.table(result);
    });

    dashboard.section.panelDetailView.clickDuplicatePanelButton();

    browser.pause(1000);

    dashboard.waitForElementPresent(dashboard.section.panelDetailView, 1000, 'Panel detail view has loaded');

    dashboard.section.panelDetailView.getText('article.panel-detail > header > h1', function(result){

      console.table({'new value':result.value, 'test value': 'Copy of: ' + originalTitle, 'original value':originalTitle});
      browser.assert.ok(result.value === 'Copy of: ' + originalTitle, "Panel title has been duplicated into a new panel");

    });


  });
});