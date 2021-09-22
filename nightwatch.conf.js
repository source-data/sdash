const chromedriver = require('chromedriver')
const geckodriver = require('geckodriver')

const output_folder = "tests/js/integration/output"

const chrome_webdriver_config = {
    start_process: true,
    port: 9515,
    server_path: chromedriver.path,
    log_path: output_folder,
}
const firefox_webdriver_config = {
    start_process: true,
    port: 4444,
    server_path: geckodriver.path,
    log_path: output_folder,
}

const firefox_test_settings = {
    desiredCapabilities: {
        browserName: 'firefox'
    },
}
const chrome_test_settings = {
    desiredCapabilities: {
        browserName: 'chrome',
    },
    webdriver: chrome_webdriver_config,
}

module.exports = {
    src_folders: ["tests/js/integration"],
    output_folder: output_folder,
    webdriver: firefox_webdriver_config,
    test_settings: {
        chrome: chrome_test_settings,
        default: firefox_test_settings,
        firefox: firefox_test_settings,
    }
}