describe('User profile', function() {
    beforeEach(function(browser) {
        browser.page.login()
        .navigate()
        .login();
    })

    test('check that a user without an avatar can change and delete it', function(browser) {
      let userProfile = browser.page.userProfile();

      userProfile.navigate();
      userProfile
        .assert.not.elementPresent('@customAvatar', 'before uploading an avatar, the user has no custom avatar')
        .assert.visible('@defaultAvatar', 'before uploading an avatar, the default avatar is visible')
        .assert.visible('@changeAvatarButton', 'before uploading an avatar, the button to edit the user\'s avatar is visible')
        .assert.not.elementPresent('@deleteAvatarButton', 'before uploading an avatar, the button to delete the user\'s avatar is not present');
      userProfile.section.navbar
        .assert.not.elementPresent('@customUserProfileImage', 'before uploading an avatar, the user has no custom avatar in the navbar')
        .assert.visible('@defaultUserProfileImage', 'before uploading an avatar, the default avatar is visible in the navbar');

      userProfile.changeAvatar();
      userProfile
        .assert.visible('@customAvatar', 'after uploading an avatar, the new avatar is visible')
        .assert.not.elementPresent('@defaultAvatar', 'after uploading an avatar, the default avatar is no longer present')
        .assert.visible('@changeAvatarButton', 'after uploading an avatar, the button to edit the user\'s avatar is still visible')
        .assert.visible('@deleteAvatarButton', 'after uploading an avatar, the button to delete the user\'s avatar is visible');
        userProfile.section.navbar
          .assert.visible('@customUserProfileImage', 'after uploading an avatar, the custom avatar is visible in the navbar')
          .assert.not.elementPresent('@defaultUserProfileImage', 'after uploading an avatar, the default avatar is no longer present in the navbar');

      userProfile.deleteAvatar();
      userProfile
        .assert.not.elementPresent('@customAvatar', 'after deleting an avatar, the user has no custom avatar')
        .assert.visible('@defaultAvatar', 'after deleting an avatar, the default avatar is visible')
        .assert.visible('@changeAvatarButton', 'after deleting an avatar, the button to edit the user\'s avatar is visible')
        .assert.not.elementPresent('@deleteAvatarButton', 'after deleting an avatar, the button to delete the user\'s avatar is not present');
        userProfile.section.navbar
          .assert.not.elementPresent('@customUserProfileImage', 'after deleting an avatar, the user has no custom avatar in the navbar')
          .assert.visible('@defaultUserProfileImage', 'after deleting an avatar, the default avatar is visible in the navbar')

      browser.end()
    })
  })
  