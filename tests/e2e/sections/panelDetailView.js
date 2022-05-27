var panelDetailViewCommands = {
  clickDuplicatePanelButton: function() {
    return this.click('@duplicatePanelButton');
  }
};

module.exports = {
  selector: '#sd-panel-detail-modal',
  commands: [panelDetailViewCommands],
  elements: {
    reportContentButton: 'a.sd-report-content-button',
    duplicatePanelButton: 'button.sd-duplicate-panel-button',
    panelTitle: 'article.panel-detail > header > h1',
    mainImage: 'img.sd-panel-main-image',
    authorList: 'ul.sd-panel-author-list',
  }

}