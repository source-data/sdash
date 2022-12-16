describe('SDash Homepage', function() {
  test('check that the basic navigation links & the search bar are present when not logged in', function(browser) {
    let dashboard = browser.page.dashboard();
    dashboard.navigate();
    dashboard.section.navbar
      .assert.visible('@linkToDashboard', 'the link to the homepage is visible')

      .assert.visible('@linkToAboutPage', 'the link to the "About" page is visible')
      .assert.visible('@linkToTutorial', 'the link to the "Tutorial" page is visible')
      .assert.not.elementPresent('@linkToGroupList', 'the link to the "Groups" page is not present')

      .assert.visible('@linkToLoginPage', 'the link to the login page is visible')
      .assert.visible('@linkToRegistrationPage', 'the link to the registration page is visible')

      .assert.not.visible('@searchBar', 'the search bar is not visible before toggling the search bar')
      .toggleSearchBar()
      .assert.visible('@searchBar', 'the search bar is visible after toggling the search bar')

      .assert.not.elementPresent('@userMenuToggle', 'the dropdown toggle for the user menu is not present')
      .assert.not.elementPresent('@linkToLogoutPage', 'the link to log out is not present')
      .assert.not.elementPresent('@linkToUserProfile', 'the link to the user profile is not present');

    dashboard.section.header
      .assert.visible('@jumbotron', 'the jumbotron with SDash info is visible')

    browser.end()
  })

  test('check that all navigation links & the search bar & the user menu are present when logged in', function(browser) {
    browser.page.login()
      .navigate()
      .login();

    let dashboard = browser.page.dashboard();
    dashboard.navigate();
    dashboard.section.navbar
      .assert.visible('@linkToDashboard', 'the link to the homepage is visible')

      .assert.visible('@linkToAboutPage', 'the link to the "About" page is visible')
      .assert.visible('@linkToTutorial', 'the link to the "Tutorial" page is visible')
      .assert.visible('@linkToGroupList', 'the link to the "Groups" page is not present')

      .assert.not.elementPresent('@linkToLoginPage', 'the link to the login page is not present')
      .assert.not.elementPresent('@linkToRegistrationPage', 'the link to the registration page is not present')

      .assert.not.visible('@searchBar', 'the search bar is not visible before toggling the search bar')
      .toggleSearchBar()
      .assert.visible('@searchBar', 'the search bar is visible after toggling the search bar')

      .assert.not.visible('@linkToLogoutPage', 'the link to log out is not visible before toggling the user menu')
      .assert.not.visible('@linkToUserProfile', 'the link to the user profile is not visible before toggling the user menu')
      .toggleUserMenu()
      .assert.visible('@linkToLogoutPage', 'the link to log out is visible after toggling the user menu')
      .assert.visible('@linkToUserProfile', 'the link to the user profile is visible after toggling the user menu')

    dashboard.section.header
      .assert.not.elementPresent('@jumbotron', 'the jumbotron with SDash info is not present')
    browser.end()
  })

  test('check that the guided tour is displayed on first login', function(browser) {
    browser.page.login()
      .navigate()
      .login();

    let dashboard = browser.page.dashboard();
    dashboard.navigate()
    .waitForElementVisible(dashboard.section.guidedTour, 2000)
    .assert.elementPresent(dashboard.section.guidedTour, 'the guided tour is displayed')
    ;
    dashboard.section.guidedTour
      .assert.visible('@nextButton', 'the guided tour has a next button')
      .assert.visible('@skipButton', 'the guided tour has a skip button')
      .clickNext()
      .waitForElementVisible('@prevButton', 1000)
      .assert.visible('@prevButton', 'the guided tour has skipped to the next step')
      .clickPrev()
      .waitForElementVisible('@nextButton', 1000)
      .assert.visible('@nextButton', 'the guided tour can skip to the previous step')
      .clickNext()
      .waitForElementVisible('@stopButton', 1000)
      .assert.visible('@stopButton', 'the guided tour has arrived at the final step')
      .clickStop()
      .waitForElementNotPresent('.v-step', 1000)
      .assert.not.elementPresent('.v-step', 'the guided tour has been closed');


  })
})
