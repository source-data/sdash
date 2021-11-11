describe('SDash Homepage', function() {
  const homepage = (browser) => browser.url(browser.launchUrl)

  test('check that the basic navigation links & the search bar are present', function(browser) {
    homepage(browser)
      .waitForElementVisible('nav')
      .assert.visible('a[href="/"]', 'the link to the homepage is visible')
      .assert.visible('a[href="/about"]', 'the link to the "About" page is visible')
      .assert.visible('a[href="/login"]', 'the link to the login page is visible')
      .assert.visible('a[href="/register"]', 'the link to the registration page is visible')
      .assert.not.elementPresent('a[href="/groups"]', 'the link to the "Groups" page is not present')
      .assert.not.visible('input[type="search"]', 'the search bar is not visible before clicking the search button')
      .click('#navbarSearchMenuLink')
      .assert.visible('input[type="search"]', 'the search bar is visible after clicking the search button')
      .end()
  })
})
