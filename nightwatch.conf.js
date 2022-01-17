const dotenv = require("dotenv")
dotenv.config()

const output_folder = "tests/e2e/output"

module.exports = {
    src_folders: ["tests/e2e"],
    output_folder: output_folder,

    // Common configuration options for the webdriver
    webdriver: {
        log_path: output_folder,
        start_process: true
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