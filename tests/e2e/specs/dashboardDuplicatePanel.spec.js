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
    .waitForElementPresent(dashboard.section.panelGrid.elements.firstPanelLink, 2000, 'First panel link is present').click(dashboard.section.panelGrid.elements.firstPanelLink);
    dashboard.section.panelDetailView.assert.elementPresent("@duplicatePanelButton", "The duplicate panel button is correctly shown on a user's own panels");

  });
});