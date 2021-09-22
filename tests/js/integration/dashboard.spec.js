describe('SDash Public Dashboard', function() {
  const url_public_dashboard = 'http://localhost:8080/'
  const public_dashboard = (browser) => browser.url(url_public_dashboard)

  test('check that the basic navigation links & the search bar are present', function(browser) {
    public_dashboard(browser)
      .assert.visible('.search', 'the search bar is visible')
      .assert.visible('a[href="/"]', 'the link to the public dashboard is visible')
      .assert.not.elementPresent('a[href="/dashboard"]', 'the link to the private dashboard is not present')
      .assert.not.elementPresent('a[href="/dashboard/groups"]', 'the link to the groups overview page is not present')
      .end()
  })
})
  