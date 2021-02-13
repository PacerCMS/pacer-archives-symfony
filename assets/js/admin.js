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
require('clndr')
require('../../node_modules/datatables.net/js/jquery.dataTables.js')
require('../../node_modules/datatables.net-bs4/js/dataTables.bootstrap4.js')

// CSS
require('../../node_modules/bootstrap/dist/css/bootstrap.css')
require('../../node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css')
require('../css/admin.scss')

$('[data-toggle="tooltip"]').tooltip();
