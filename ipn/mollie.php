<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Webhook for Mollie.
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MFreak.nl
 * @author    Luuk Verhoeven
 */

require("../../../config.php");
defined('MOODLE_INTERNAL') || die();

require_once("../lib.php");

set_exception_handler('enrol_coursepayment_ipn_exception_handler');

$orderid = required_param('orderid', PARAM_ALPHANUMEXT);
$instanceid = required_param('instanceid', PARAM_INT);

// Validate the orderid.
$return = enrol_get_plugin('coursepayment')
    ->order_valid($orderid, 'mollie');

if ($return['status'] == true) {
    die('success');

} else {
    // Send a status message to the webhook.
    throw new Exception($return['message']);
}

/**
 * Exception handler.
 * Response the error to Mollie
 *
 * @param Exception $ex
 */
function enrol_coursepayment_ipn_exception_handler($ex) {
    $info = get_exception_info($ex);
    echo "IPN exception handler: " . $info->message;
}
