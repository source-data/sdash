const navbar = require('../sections/navbar.js');
const guidedTour = require('../sections/guidedTour.js');
const panelGrid = require('../sections/panelGrid.js');
const panelDetailView = require('../sections/panelDetailView.js');

module.exports = {
    url: function() {
        return this.api.launchUrl;
    },
    sections: {
        navbar: navbar,
        guidedTour: guidedTour,
        panelGrid: panelGrid,
        panelDetailView: panelDetailView,
        header: {
            selector: 'header.sd-view-title',
            elements: {
                jumbotron: '#sd-featured-jumbotron',
            }
        }
    },
};