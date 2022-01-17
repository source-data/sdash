var navbarCommands = {
    toggleSearchBar: function() {
        return this
            .verify.visible('@searchBarToggle')
            .click('@searchBarToggle')
            .waitForElementVisible('@searchBar', 1000)
    },
    toggleUserMenu: function() {
        return this
            .verify.visible('@userMenuToggle')
            .click('@userMenuToggle')
            .waitForElementVisible('@linkToUserProfile', 1000)
    },
};
module.exports = {
    selector: '#sd-navbar',
    commands: [navbarCommands],
    elements: {
        // the SDash icon on the left
        linkToDashboard: 'a[href="/"]',
        // the main navigation links in the middle
        linkToAboutPage: 'a[href="/about"]',
        linkToTutorial: 'a[href="/tutorial"]',
        linkToGroupList: 'a[href="/groups"]',
        // the secondary navigation links on the right if not logged in
        linkToLoginPage: 'a[href="/login"]',
        linkToRegistrationPage: 'a[href="/register"]',
        // the search bar
        searchBarToggle: '#navbarSearchMenuLink',
        searchBar: 'input[type="search"]',
        // the secondary navigation links on the right when logged in
        userMenuToggle: '#navbarUserProfileMenuLink',
        linkToUserProfile: 'a[href^="/user/"]',
        linkToLogoutPage: 'a[href="/logout"]',
    }
};