const dotenv = require("dotenv")
dotenv.config()

const baseFolder = "tests/e2e"
const outputFolder = `${baseFolder}/output`


if (process.env.CHROMEWEBDRIVER) {
    chromedriver_path = process.env.CHROMEWEBDRIVER;
} else {
    chromedriver_path = require("chromedriver").path;
}
console.log("Using chromedriver from", chromedriver_path)

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
                server_path: chromedriver_path,
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