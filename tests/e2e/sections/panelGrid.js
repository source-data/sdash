var panelGridCommands = {
  clickFirstPanel: function() {
    return this.verify.visible('@firstPanelLink')
      .moveToElement('@firstPanelLink', 0, 0)
      .click('@firstPanelLink');
  }

};

module.exports = {
  selector: '#sd-panel-listing-grid',
  commands: [panelGridCommands],
  elements: {
    firstPanelLink: 'li.sd-grid-item:first-child .sd-grid-image-container-inner'

  }
}