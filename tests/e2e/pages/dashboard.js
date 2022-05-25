const navbar = require('../sections/navbar.js');
const guidedTour = require('../sections/guidedTour.js');
const panelGrid = require('../sections/panelGrid.js');
const panelDetailView = require('../sections/panelDetailView.js');
const filterBar = require('../sections/filterBar.js');

module.exports = {
    url: function() {
        return this.api.launchUrl;
    },
    sections: {
        navbar: navbar,
        filterBar: filterBar,
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