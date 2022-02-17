const navbar = require('../sections/navbar.js');

module.exports = {
    url: function() {
        return this.api.launchUrl;
    },
    sections: {
        navbar: navbar,
        header: {
            selector: 'header.sd-view-title',
            elements: {
                jumbotron: '#sd-featured-jumbotron',
            }
        }
    },
};