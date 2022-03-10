const navbar = require('../sections/navbar.js');
const guidedTour = require('../sections/guidedTour.js');

module.exports = {
    url: function() {
        return this.api.launchUrl;
    },
    sections: {
        navbar: navbar,
        guidedTour: guidedTour,
        header: {
            selector: 'header.sd-view-title',
            elements: {
                jumbotron: '#sd-featured-jumbotron',
            }
        }
    },
};