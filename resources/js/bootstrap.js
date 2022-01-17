window._ = require("lodash");

/**
 * Install Moment library for modifying dates in Javascript
 */

window.moment = require("moment");

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require("popper.js").default;
    window.$ = window.jQuery = require("jquery");

    require("bootstrap");
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require("axios");

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.axios.defaults.headers.common["Accept"] = "application/json";
window.axios.interceptors.response.use(
    response => {
        return response;
    },
    function(error) {
        return Promise.reject(error.response);
    }
);

/*
Laravel stores the current CSRF token in an encrypted XSRF-TOKEN cookie that is included with each response generated by the framework. You can use the cookie value to set the X-XSRF-TOKEN request header.

This cookie is primarily sent as a developer convenience since some JavaScript frameworks and libraries, like Axios, automatically place its value in the X-XSRF-TOKEN header on same-origin requests.
 */
