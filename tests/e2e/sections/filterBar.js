var filterBarCommands = {
  toggleFilterBar: function() {
    this.click("@filterBarToggle");
  },
  showMyPanels: function() {
    this.click("@showMyPanelsControl");
  },
  showAllPanels: function() {
    this.click("@showAllPanelsControl");
  },
};

module.exports = {
  selector: '#sd-panel-filters',
  commands: [filterBarCommands],
  elements: {
      // The sidebar itself
      filterBarContent: '#sd-panel-filters-sidebar',
      // The sidebar toggle button
      filterBarToggle: '#sd-panel-filters-toggle',
      // The "Show my own panels radio button"
      showAllPanelsControl: '#sd-panel-privacy-group input[type="radio"][value="all"] + .custom-control-label',
      showMyPanelsControl: '#sd-panel-privacy-group input[type="radio"][value="private"] + .custom-control-label',
  }
};