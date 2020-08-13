/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// Javascript
const $ = require('jquery')
global.$ = global.jQuery = $
require('popper.js')
require('bootstrap')
global._ = require('underscore')
require('clndr')

// CSS
require('../../node_modules/bootstrap/dist/css/bootstrap.css')
require('../css/admin.scss')

$('[data-toggle="tooltip"]').tooltip();
