var loginCommands = {
    login: function() {
        let userName = 'user@example.org',
            password = 'password';
        this.verify.visible('@userName')
            .verify.visible('@password')
            .verify.visible('@submit')
            .setValue('@userName', userName)
            .setValue('@password', password);
        this.api.saveScreenshot(`tests/e2e/output/login/${Date.now()}_after-values-set.png`)
        this.assert.enabled('@submit')
        this.api.saveScreenshot(`tests/e2e/output/login/${Date.now()}_after-submit-enabled.png`)
        this.click('@submit')
        this.api.saveScreenshot(`tests/e2e/output/login/${Date.now()}_after-submit-clicked.png`)
        this.waitForElementVisible('@notification', 10000)
        this.api.saveScreenshot(`tests/e2e/output/login/${Date.now()}_notification.png`)
        return this.waitForElementVisible('#sd-panel-listing-grid', 10000)
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