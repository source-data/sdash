const navbar = require('../sections/navbar.js');

var userProfileCommands = {
    deleteAvatar: function() {
        this.verify.visible('@deleteAvatarButton')
            .click('@deleteAvatarButton')
            .waitForElementVisible('@deleteAvatarConfirmButton', 1000)
            .click('@deleteAvatarConfirmButton');
        return this.waitForElementVisible('@defaultAvatar', 10000);
    },
    changeAvatar: function() {
        let newAvatar = require('path').resolve(__dirname + '/../resources/test-image.jpg');
        browser.execute(() => {document.querySelector(".vue-image-crop-upload input[type='file']").style="display:block;"});
        this.verify.visible('@changeAvatarButton')
            .click('@changeAvatarButton')
            .waitForElementVisible('@changeAvatarModal', 1000)
            .setValue('@changeAvatarInput', newAvatar)
            .waitForElementVisible('@changeAvatarConfirmButton', 1000)
            .click('@changeAvatarConfirmButton');
        return this.waitForElementVisible('@customAvatar', 10000);
    },
};

module.exports = {
    commands: [userProfileCommands],
    url: function() {
        // User 3fd2dcde-5743-4497-bc7a-b88d0c253c60 is user@example.org, see UsersTableSeeder.php. We're logging in as that user as well in login.js.
        return this.api.launchUrl + '/user/3fd2dcde-5743-4497-bc7a-b88d0c253c60';
    },
    sections: {
        navbar: navbar,
    },
    elements: {
        customAvatar: '.avatar img[src^="/storage/avatars/"]', // src attribute starts with "/storage/avatars/"
        defaultAvatar: '.avatar img[src$="default_avatar.jpg"]',

        deleteAvatarButton: '#sd-delete-avatar-button',
        deleteAvatarConfirmButton: '#sd-delete-avatar-confirm-button',

        changeAvatarButton: '.avatar button.edit-avatar',
        changeAvatarModal: '.vue-image-crop-upload',
        changeAvatarInput: '.vue-image-crop-upload input[type="file"]',
        changeAvatarConfirmButton: 'a.vicp-operate-btn',
    }
};