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
 * language file for coursepayment
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   enrol_coursepayment
 * @copyright 2015 MFreak.nl
 * @author    Luuk Verhoeven
 */

$string['pluginname'] = 'CoursePayment';
$string['pluginname_desc'] = 'This plugin allows you to purchase course with a payment gateway';
$string['mailadmins'] = 'Notify admin';
$string['nocost'] = 'There is no cost associated with enrolling in this course!';
$string['currency'] = 'Currency';
$string['cost'] = 'Enrol cost';
$string['assignrole'] = 'Assign role';
$string['welcomemail'] = 'Welcome mail';
$string['invoicemail'] = 'Invoice mail';
$string['mailstudents'] = 'Notify students';
$string['mailteachers'] = 'Notify teachers';
$string['status'] = 'Allow CoursePayment enrolments';
$string['status_desc'] = 'Allow users to use CoursePayment to enrol into a course by default.';
$string['defaultrole'] = 'Default role assignment';
$string['defaultrole_desc'] = 'Select role which should be assigned to users during CoursePayment enrolments';
$string['enrolenddate'] = 'End date';
$string['enrolenddate_help'] = 'If enabled, users can be enrolled until this date only.';
$string['enrolenddaterror'] = 'Enrolment end date cannot be earlier than start date';
$string['enrolperiod'] = 'Enrolment duration';
$string['enrolperiod_desc'] = 'Default length of time that the enrolment is valid. If set to zero, the enrolment duration will be unlimited by default.';
$string['enrolstartdate'] = 'Start date';
$string['enrolstartdate_help'] = 'If enabled, users can be enrolled from this date onward only.';
$string['enrolperiod_help'] = 'Length of time that the enrolment is valid, starting with the moment the user is enrolled. If disabled, the enrolment duration will be unlimited.';
$string['debug'] = 'Debug';
$string['debug_desc'] = 'Should only be enabled by developers this outputs debug messages';
$string['sandbox'] = 'Sandbox';
$string['sandbox_desc'] = 'Only for testing purposes should not be enabeld. User can enrol in course without paying. For mollie this has no effect';
$string['enabled'] = 'Enabled';
$string['error:failed_getting_plugin_instance'] = 'Failed getting instance details!';
$string['crontask'] = 'CoursePayment - process orders';
$string['title:returnpage'] = 'Payment Status';
$string['success_enrolled'] = 'Thanks for your purchase.<br> We have enrolled you for: {$a->fullname}';
$string['error:unknown_order'] = 'Unknown order we don\'t have a record of it!';
$string['error:gettingorderdetails'] = 'We were unable to query the gateway for order details. We will retry later.';
$string['error:paymentabort'] = 'The payment is aborted!';
$string['error:no_record'] = 'Error: Not exists!';
$string['error:not_within_the_time_period'] = 'Error: Not valid within this time period!';
$string['error:not_for_this_course'] = 'Error: This discountcode is for another course!';
$string['error:waiting_on_payment'] = 'Not paid. We will wait on payment.';
$string['error:mollie_connect_requires'] = 'Error: please link Mollie connect first!';

$string['gateway_not_exists'] = 'Error! Gateway not exists';
$string['enabled_desc'] = 'Status of the gateway if this can be used to create a transaction';
$string['expiredaction'] = 'Enrolment expiration action';
$string['expiredaction_help'] = 'Select action to carry out when user enrolment expires. Please note that some user data
and settings are purged from course during course unenrolment.';
$string['expirymessageenrollersubject'] = 'Enrolment expiry notification';
$string['expirymessageenrollerbody'] = 'Enrolment in the course \'{$a->course}\' will expire within the next {$a->threshold}
for the following users:

{$a->users}

To extend their enrolment, go to {$a->extendurl}';
$string['expirymessageenrolledsubject'] = 'Enrolment expiry notification';
$string['expirymessageenrolledbody'] = 'Dear {$a->user},

This is a notification that your enrolment in the course \'{$a->course}\' is due to expire on {$a->timeend}.

If you need help, please contact {$a->enroller}.';
$string['purchase'] = 'Purchase';
$string['provider'] = 'Provider';
$string['name'] = 'Name';
$string['minimum'] = 'Minimum';
$string['maximum'] = 'Maximum';
$string['instancedesc'] = 'Description';
$string['gateway_mollie_select_method'] = 'Please click the method you like to use.';
$string['gateway_mollie'] = 'Gateway: Mollie';
$string['gateway_mollie_desc'] = 'Offer your customers the payment methods Creditcard, SOFORT Banking, iDEAL, Bancontact/Mister Cash, Bank transfer, Bitcoin, PayPal or paysafecard. Mollie is known for reliability, transparency, nice API’s and ready-to-go modules.';
$string['gateway_mollie_apikey'] = 'API key';
$string['gateway_mollie_link'] = 'If you don\'t have a account please <a href="{$a->link}" target="_blank">register</a>';
$string['gateway_mollie_send_button'] = 'Purchase with mollie';
$string['error:capability_config'] = 'Error: You need the coursepayment/config capability!';
$string['enrol_coursepayment_discount'] = 'Discount';
$string['enrol_coursepayment_discount_desc'] = 'Discount codes to distribute to your customers can be created in the discount manager. <br/><br/><a href="{$a->link}" class="btn btn-small btn-primary">Discount manager</a>';

$string['new:discountcode'] = 'Add a new discount code';
$string['th:code'] = 'Code';
$string['th:courseid'] = 'Course';
$string['th:start_time'] = 'From';
$string['th:end_time'] = 'End';
$string['th:amount'] = 'Discount';
$string['th:action'] = 'Action';

$string['form:allcourses'] = 'Complete website';
$string['form:code'] = 'Code to get the discount<br> (make sure its unique)';
$string['form:discountcode'] = 'Discount code';
$string['form:start_time'] = 'Valid from';
$string['form:end_time'] = 'Valid to';
$string['form:save'] = 'Save';
$string['form:amount'] = 'Amount of discount';
$string['form:percentage'] = 'Percentage of discount';
$string['form:make_selection'] = 'Make a selection';
$string['form:course'] = 'Course';
$string['form:payment_status'] = 'Payment status';
$string['form:search'] = 'Search';

$string['error:number_to_low'] = 'This number is to low';
$string['error:price_wrongformat'] = 'This isn\'t numeric!';
$string['error:code_not_unique'] = 'Discount code needs to be unique';

$string['discount_code_desc'] = 'If you have a discount code enter it below';
$string['discountcode_invalid'] = 'Error: This code isn\'t valid anymore or is incorrect for this course!';
$string['vatpercentages'] = 'VAT Percentage included in the cost';

$string['invoicedetails'] = 'Invoice details';
$string['invoicedetails_desc'] = 'This fields are required! When you leave them blank the generate invoice will be incorrect.';
$string['btw'] = 'VAT';
$string['kvk'] = 'kvk';
$string['place'] = 'Place';
$string['zipcode'] = 'Zipcode';
$string['address'] = 'Address';
$string['companyname'] = 'Companyname';

$string['mail:invoice_subject_manual'] = 'Thank you for ordering: {$a->content_type} - {$a->fullcourse} / {$a->fullname}';
$string['mail:invoice_message_manual'] = 'Dear {$a->fullname},<br><br>
A payment has been made on {$a->date} through {$a->method}.<br>
You will receive the invoice with invoice number {$a->invoice_number}. <br>
The amount is {$a->cost} {$a->currency}.
<br>
The Invoice is attached (see attachment PDF)<br>
<br>
Kind regards,<br>
{$a->sitename}';

$string['mail:invoice_subject_student'] = 'Thank you for ordering: {$a->content_type} - {$a->fullcourse} / {$a->fullname}';
$string['mail:invoice_message_student'] = 'Dear {$a->fullname},<br><br>
A payment has been made on {$a->date} through {$a->method}.<br>
You will receive the invoice with invoice number {$a->invoice_number}. <br>
The amount is {$a->cost} {$a->currency}.
<br>
The Invoice is attached (see attachment PDF)<br>
<br>
Kind regards,<br>
{$a->sitename}';

$string['mail:invoice_subject_admin'] = 'Thank you for ordering: {$a->content_type} - {$a->fullcourse} / {$a->fullname}';
$string['mail:invoice_message_admin'] = 'Dear {$a->fullname},<br><br>
A payment has been made on {$a->date} through {$a->method}.<br>
You will receive the invoice with invoice number {$a->invoice_number}. <br>
The amount is {$a->cost} {$a->currency}.<br>
<br>
The Invoice is attached (see attachment PDF)<br>
<br>
Kind regards,<br>
{$a->sitename}';

$string['mail:invoice_subject_teacher'] = 'Thank you for ordering: {$a->content_type} - {$a->fullcourse} / {$a->fullname}';
$string['mail:invoice_message_teacher'] = 'Dear {$a->fullname},<br><br>
A payment has been made on {$a->date} through {$a->method}.<br>
You will receive the invoice with invoice number {$a->invoice_number}. <br>
The amount is {$a->cost} {$a->currency}.<br>
<br>
The Invoice is attached (see attachment PDF)<br>
<br>
Kind regards,<br>
{$a->sitename}';
$string['coursepayment:report'] = 'Report';
$string['coursepayment:config'] = 'Configuration';
$string['coursepayment:manage'] = 'Manage';
$string['coursepayment:unenrol'] = 'Unenrol';
$string['coursepayment:unenrolself'] = 'Unenrolself';
$string['success_enrolled_activity'] = 'Payment successful, you can now enter the activity.';
$string['gateway_mollie_external_connector'] = 'External API connector';
$string['gateway_mollie_external_connector_desc'] = 'This feature should be disabled! (only enable if you know what you doing)';
$string['gateway_mollie_partner_id'] = 'Partner id';
$string['gateway_mollie_profile_key'] = 'Profile key';
$string['gateway_mollie_app_secret'] = 'App secret';
$string['form:newaccount'] = 'Make a new account on Mollie.nl';
$string['form:username'] = 'Username (should not exists on Mollie)';
$string['form:name'] = 'Your fullname';
$string['form:company_name'] = 'Company name';
$string['form:email'] = 'E-mail';
$string['form:address'] = 'Street and number';
$string['form:zipcode'] = 'Zipcode';
$string['form:city'] = 'City';
$string['form:register'] = 'Register';
$string['enrol_coursepayment_newaccount'] = 'New mollie account';
$string['message:added_account'] = 'Your account is added! Check your e-mail for the details.';
$string['custommails'] = 'Extra email addresses';
$string['custommails_desc'] = 'Add extra addresses that should receive the invoice. (CSV format)';
$string['link_agreement'] = 'Terms and Conditions';
$string['link_agreement_desc'] = 'Users need to approve this link to the terms and conditions.';
$string['agreement_label'] = 'I agree with the <a class="coursepayment-agreement-link" target="_blank" href="{$a->link}">terms and conditions</a>';
$string['js:username'] = 'Username';
$string['js:password'] = 'Password';
$string['js:delay'] = 'Delay';
$string['js:connect'] = 'Connect';
$string['standalone_purchase_page'] = 'Standalone payment page';
$string['standalone_purchase_page_desc'] = 'Use a standalone payment page';
$string['gateway_mollie_ideal_heading'] = 'IDEAL — Select your bank';
$string['gateway_mollie_backlink'] = 'Back to <a href="/">{$a->fullname}</a> ';

// Settings.
$string['settings:tab_invoicedetails'] = 'Invoice details';
$string['settings:tab_gateway'] = 'Gateway';
$string['settings:tab_enrolment'] = 'Enrolment';
$string['settings:tab_advanced'] = 'Advanced';
$string['settings:tab_mail'] = 'E-mail';
$string['settings:tab_multiaccount'] = 'Multi-account';
$string['multi_account_heading'] = 'Multiple Mollie account support';
$string['multi_account'] = 'Multiple accounts';
$string['multi_account_desc'] = 'If this feature is enabled we will support mapping for multiple Mollie accounts
based on a profile field.';
$string['setting:disabled_by_multi_account'] = 'This setting tab is disabled by the Multi-account option.
 See multi-account tab instead for more details.';
$string['message:error_add_profile_field'] = 'Error: make sure you have some extra profile field we need to match on.';
$string['multi_account_profile_field'] = 'Profile field';
$string['multi_account_profile_field_desc'] = 'Select a profile field what we should use for mapping.';
$string['th_name'] = 'Name';
$string['th_action'] = 'Action';
$string['th_profile_value'] = 'Profile value';
$string['btn:new'] = 'Add new';
$string['btn:report'] = 'Report';
$string['btn:filter'] = 'Filter results';
$string['btn:pdf_edit'] = 'Edit invoice';
$string['no_result'] = 'No results';
$string['enrol_coursepayment_multi_account'] = 'Multi-account';
$string['form:name_multiaccount'] = 'Name';
$string['form:profile_value'] = 'Profile field value matches';
$string['form:btw'] = 'Vat';
$string['form:kvk'] = 'KvK';
$string['form:place'] = 'Place';
$string['form:mollie'] = 'Mollie account';
$string['form:company_info'] = 'Company / invoice information';
$string['gateway_mollie_debug'] = 'Debugging';
$string['gateway_mollie_sandbox'] = 'Sandbox';
$string['form:multi_account'] = 'Multi-account settings';
$string['confirm_delete'] = 'Are you sure you want to delete this item?';
$string['transaction_name'] = 'Transaction name';
$string['transaction_name_desc'] = 'Supports the follow shortcodes: <br>
{invoice_number} : Invoice number<br>
{course} : Course<br>
{course_shortname} : Course shortname<br>
{site} : Site name<br>
{site_shortname} : Site shortname<br>
{customtext2} : Extra identifier';
$string['mollieconnect'] = 'IMPORTANT! Account is not connected. To enable this plugin you need to authorise our App. <br> <br> To do this click on the button below.';

// Privacy.
$string['privacy:metadata:enrol_coursepayment'] = 'Stores the user transaction data, this allow to keep track of what a
 user has purchased.';

$string['privacy:metadata:enrol_coursepayment:userid'] = 'The ID of the user with this transaction.';
$string['privacy:metadata:enrol_coursepayment:gateway_transaction_id'] = 'The transaction identifier of the user.';
$string['privacy:metadata:enrol_coursepayment:orderid'] = 'The order identifier of the user.';
$string['privacy:metadata:enrol_coursepayment:instanceid'] = 'The connected instanceid that refers to the purchase.';
$string['privacy:metadata:enrol_coursepayment:addedon'] = 'The start time of the transaction.';
$string['privacy:metadata:enrol_coursepayment:courseid'] = 'The course id connected to the transaction.';

$string['customtext2'] = 'Extra identifier';
$string['report_include_none_payment_users'] = 'Report include all users';

// Headings.
$string['heading:report'] = 'Report';
$string['heading:table_firstname'] = 'Firstname';
$string['heading:table_lastname'] = 'Lastname';
$string['heading:table_course'] = 'Course';
$string['heading:table_email'] = 'E-mail';
$string['heading:table_status'] = 'Payment status';
$string['heading:table_phone1'] = 'Phone';
$string['heading:table_addedon'] = 'Created at';

// Status.
$string['status:success'] = 'Success';
$string['status:waiting'] = 'Waiting';
$string['status:cancel'] = 'Cancel';
$string['status:abort'] = 'Abort';
$string['status:error'] = 'Error';
$string['status:no_payments'] = 'No payments connected';

// Invoice builder.
$string['savechangespreview'] = 'Save changes and preview';
$string['enrol_coursepayment_invoice_edit'] = 'Edit invoice';
$string['page'] = 'Page';
$string['width'] = 'Width';
$string['width_help'] = 'This is the width of the certificate PDF in mm. For reference an A4 piece of paper is 210mm wide and a letter is 216mm wide.';
$string['elementwidth'] = 'Width';
$string['elementwidth_help'] = 'Specify the width of the element - \'0\' means that there is no width constraint.';

$string['height'] = 'Height';
$string['height_help'] = 'This is the height of the certificate PDF in mm. For reference an A4 piece of paper is 297mm high and a letter is 279mm high.';
$string['leftmargin'] = 'Left margin';
$string['leftmargin_help'] = 'This is the left margin of the certificate PDF in mm.';
$string['rightmargin'] = 'Right margin';
$string['rightmargin_help'] = 'This is the right margin of the certificate PDF in mm.';
$string['deletepage'] = 'Delete page';
$string['addelement'] = 'Add element';
$string['addpage'] = 'Add page';

$string['invoice_element_bgimage'] = 'Background image';
$string['invoice_element_image'] = 'Image';
$string['invoice_element_digitalsignature'] = 'Digital signature';
$string['invoice_element_border'] = 'Border';
$string['invoice_element_date'] = 'Date';
$string['invoice_element_text'] = 'Text';
$string['invoice_element_categoryname'] = 'Course category';
$string['invoice_element_userfield'] = 'User profile field';
$string['invoice_element_coursename'] = 'Coursename';
$string['invoice_element_studentname'] = 'Studentname';
$string['invoice_element_orderdata'] = 'Orderdata';
$string['invoice_element_invoiceinfo'] = 'Invoice information';

$string['elementname'] = 'Element name';
$string['image'] = 'Image';
$string['uploadimage'] = 'Upload image';
$string['elementname_help'] = 'This will be the name used to identify this element when editing a PDF.
 Note: this will not displayed on the PDF.';
$string['noimage'] = 'No image';
$string['editelement'] = 'Edit element';
$string['editinvoice'] = 'Edit invoice';
$string['deleteelementconfirm'] = 'Are you sure you want to delete this element?';
$string['deletecertpage'] = 'Delete page';
$string['deleteconfirm'] = 'Delete confirmation';
$string['deleteelement'] = 'Delete element';
$string['rearrangeelements'] = 'Reposition elements';
$string['elements'] = 'Elements';
$string['elements_help'] = 'This is the list of elements that will be displayed on the invoice.';
$string['type'] = 'Type';
$string['rearrangeelements'] = 'Reposition elements';
$string['rearrangeelementsheading'] = 'Drag and drop elements to change where they are positioned on the invoice.';
$string['saveandclose'] = 'Save and close';
$string['saveandcontinue'] = 'Save and continue';
$string['refpoint'] = 'Reference point location';
$string['refpoint_help'] = 'The reference point is the location of an element from which its x and y coordinates are determined. It is indicated by the \'+\' that appears in the centre or corners of the element.';
$string['topcenter'] = 'Center';
$string['topleft'] = 'Top left';
$string['topright'] = 'Top right';
$string['font'] = 'Font';
$string['font_help'] = 'The font used when generating this element.';
$string['fontcolour'] = 'Colour';
$string['fontcolour_help'] = 'The colour of the font.';
$string['fontsize'] = 'Size';
$string['fontsize_help'] = 'The size of the font in points.';
$string['text'] = 'Text';
$string['text_help'] = 'This is the text that will display on the PDF.';
$string['userfield'] = 'Userfield';
$string['userfield_help'] = 'This is the user field that will be displayed on the PDF.';
$string['invalidcode'] = 'Invalid code supplied.';
$string['invalidcolour'] = 'Invalid colour chosen, please enter a valid HTML colour name, or a six-digit, or three-digit hexadecimal colour.';
$string['invalidelementwidth'] = 'Please enter a positive number.';
$string['invalidposition'] = 'Please select a positive number for position {$a}.';
$string['invalidheight'] = 'The height has to be a valid number greater than 0.';
$string['invalidmargin'] = 'The margin has to be a valid number greater than 0.';
$string['invalidwidth'] = 'The width has to be a valid number greater than 0.';
$string['deletepageconfirm'] = 'Are you sure you want to delete this certificate page?';
$string['processexpirationstask'] = 'CoursePayment enrolment send expiry notifications task';

$string['orderdata:coursename'] = 'Name';
$string['orderdata:vat'] = 'Vat';
$string['orderdata:total'] = 'Total';
$string['orderdata:subtotal'] = 'Subtotal';
$string['orderdata:total'] = 'Total';
$string['orderdata:dummy_course'] = 'Dummy name';
$string['invoiceinfo:kvk'] = 'KvK';
$string['invoiceinfo:vat'] = 'VAT';
$string['invoiceinfo:invoice_number'] = 'Invoice number';
$string['invoiceinfo:ref'] = 'Ref.';
$string['invoiceinfo:date'] = 'Date';
$string['invoice_pdf'] = 'Invoice PDF designer';
$string['invoice_desc'] = 'If you want to change the design of the invoice pdf, you can do that here.';
$string['pdf'] = 'PDF';

$string['posx'] = 'X cordinate';
$string['posx_help'] = 'You can use the reposition mode for this';
$string['posy'] = 'Y cordinate';
$string['posy_help'] = 'You can use the reposition mode for this';

// Message providers.
$string['messageprovider:coursepayment_enrolment'] = 'Enrolment message';
$string['messageprovider:expiry_notification'] = 'Expiry message';
$string['messageprovider:coursepayment_invoice'] = 'Invoice message';
