var loginCommands = {
    login: function() {
        let userName = 'user@example.org',
            password = 'password';
        this.verify.visible('@userName')
            .verify.visible('@password')
            .verify.visible('@submit')
            .setValue('@userName', userName)
            .setValue('@password', password)
            .assert.enabled('@submit')
            .click('@submit')
            .waitForElementVisible(
                '@notification',
                2000,
                false, /* don't fail if the notification doesn't become visible, the login might still have worked */
            );
        this.api.saveScreenshot(`tests/e2e/output/login/${Date.now()}_login-notification.png`);
        return this.waitForElementVisible('#sd-panel-listing-grid', 10000);
    }
};

module.exports = {
    commands: [loginCommands],
    url: function() {
        return this.api.launchUrl + '/login';
    },
    elements: {
        userName: 'input#login-email',
        password: 'input#login-password',
        submit: 'button[type="submit"]',
        notification: '.snotifyToast',
        successNotification: '.snotifyToast.snotify-success',
        errorNotification: '.snotifyToast.snotify-error',
    }
};