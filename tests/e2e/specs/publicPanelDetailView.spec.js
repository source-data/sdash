describe('Panel detail view on public dashboard', function () {

  test('check that the panel detail view will display on clicking the first panel', function (browser) {

    let dashboard = browser.page.dashboard();
    dashboard.navigate()
      .waitForElementPresent(dashboard.section.panelGrid, 1000, 'Panel grid is present')
      .waitForElementPresent(dashboard.section.panelGrid.elements.firstPanelLink, 2000, 'First panel link is present');

      dashboard.section.panelGrid.clickFirstPanel()
        .waitForElementPresent(dashboard.section.panelDetailView, 1000, 'Panel detail view can open');

  });

  test('check that the panel detail view has the expected elements', function (browser) {

    let dashboard = browser.page.dashboard();
    dashboard.navigate()
      .waitForElementPresent(dashboard.section.panelGrid, 1000, 'Panel grid is present')
      .waitForElementPresent(dashboard.section.panelGrid.elements.firstPanelLink, 2000, 'First panel link is present');

      dashboard.section.panelGrid.clickFirstPanel()
        .waitForElementPresent(dashboard.section.panelDetailView, 2000, 'Panel detail view can open');
      dashboard.section.panelDetailView
        .assert.elementPresent('@panelTitle', 'The panel title is present')
        .assert.elementPresent('@reportContentButton', 'The report content button is present')
        .assert.elementPresent("@mainImage", "The main panel image is present")
        .assert.elementPresent("@authorList", "The list of authors is present")
        .assert.not.elementPresent("@duplicatePanelButton", "The duplicate panel button is correctly not shown");

  });

});