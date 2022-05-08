window._ = require('lodash');

try {
    window.Popper = require('@popperjs/core');
    window.bootstrap = require('bootstrap');
} catch (exception) {
    console.error(exception);
}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
