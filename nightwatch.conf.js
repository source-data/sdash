const dotenv = require("dotenv")
dotenv.config()

const baseFolder = "tests/e2e"
const outputFolder = `${baseFolder}/output`

module.exports = {
    src_folders: `${baseFolder}/specs`,
    page_objects_path: `${baseFolder}/pages`,
    output_folder: outputFolder,

    screenshots: {
        enabled: true,
        path: outputFolder,
    },

    // Common configuration options for the webdriver
    webdriver: {
        log_path: outputFolder,
        start_process: true,
    },

    test_settings: {
        default: {
            launch_url: process.env.APP_URL // read from the .env file
        },
        chrome: {
            desiredCapabilities: {
                browserName: "chrome",
            },
            webdriver: {
                port: 9515,
                server_path: require("chromedriver").path,
            },
        },
        firefox: {
            desiredCapabilities: {
                browserName: "firefox"
            },
            webdriver: {
                port: 4444,
                server_path: require("geckodriver").path,
            }
        },
    }
}