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
$string['pluginname_desc'] = 'Deze plugin maakt het mogelijk om een cursus te verkopen met een betaal provider';
$string['mailadmins'] = 'E-mail admin';
$string['nocost'] = 'Er zitten geen kosten aan deze cursus';
$string['currency'] = 'Valuta';
$string['cost'] = 'Inschrijfkosten';
$string['assignrole'] = 'Toekennen rol';
$string['welcomemail'] = 'Welkom cursus e-mail';
$string['invoicemail'] = 'Facturatie e-mail';
$string['mailstudents'] = 'E-mail studenten';
$string['mailteachers'] = 'E-mail leraren';
$string['status'] = 'sta CoursePayment inschrijvingen toe';
$string['status_desc'] = 'Sta gebruikers toe om CoursePayment gebruiken om in te schrijven in een cursus standaard.';
$string['defaultrole'] = 'Standaard roltoewijzing';
$string['defaultrole_desc'] = 'Selecteer rol die de gebruikers moeten worden toegekend tijdens CoursePayment inschrijvingen';
$string['enrolenddate'] = 'Einddatum';
$string['enrolenddate_help'] = 'Indien ingeschakeld, kunnen gebruikers worden ingeschreven tot deze datum.';
$string['enrolenddaterror'] = 'Inschrijving einddatum kan niet eerder dan startdatum';
$string['enrolperiod'] = 'Inschrijvingsduur';
$string['enrolperiod_desc'] = 'Standaard duur dat een inschrijving geldig is. Indien ingesteld op nul, zal de inschrijving voor onbeperkte tijd zijn';
$string['enrolstartdate'] = 'Startdatum';
$string['enrolstartdate_help'] = 'Indien ingeschakeld, kunnen gebruikers worden ingeschreven vanaf deze datum.';
$string['enrolperiod_help'] = 'duur dat de inschrijving geldig is, gestart vanaf het moment dat de gebruiker is aangemeld in de cursus. Wanneer deze optie is uitgeschakeld, is de duratie oneindig.';
$string['debug'] = 'Debug';
$string['debug_desc'] = 'Mag alleen worden ingeschakeld voor ontwikkel doel einde';
$string['sandbox'] = 'Sandbox';
$string['sandbox_desc'] = 'Sandbox mode voor de provider, is niet altijd van kracht. Soms ligt de instelling bij de provider';
$string['enabled'] = 'Ingeschakeld';
$string['error:failed_getting_plugin_instance'] = 'instance gegevens ophalen mislukt';
$string['crontask'] = 'CoursePayment - order behandeling';
$string['title:returnpage'] = 'Betaal Status';
$string['success_enrolled'] = 'Bedankt voor de aanschaf.<br> We hebben u ingeschreven voor: {$a->fullname}';
$string['error:unknown_order'] = 'Er is geen order bij ons bekend met deze gegevens.';
$string['error:gettingorderdetails'] = 'We waren niet in staat om de order gegevens op te halen.';
$string['error:paymentabort'] = 'De betaling is afgebroken.';
$string['error:mollie_connect_requires'] = 'Fout: Mollie connect is nog niet geconfigureerd';
$string['gateway_not_exists'] = 'Fout! Gateway is onbekend.';
$string['enabled_desc'] = 'Wanneer ingeschakeld kan deze enrolment plugin ingesteld worden bij een cursus.';
$string['expiredaction'] = 'Inschrijving verloop actie';
$string['expiredaction_help'] = 'Selecteer actie die uitvoerd moet wordenwanneer de inschrijving van de gebruiker verloopt. Houdt u er rekening mee dat sommige gebruikersgegevens en instellingen kunnen worden verwijderd.';
$string['expirymessageenrollersubject'] = 'Melding voor het vervallen van de aanmelding';
$string['expirymessageenrollerbody'] = 'De aanmelding in cursus \'{$a->course}\' zal binnen {$a->threshold} vervallen voor volgende gebruikers:

{$a->users}

Ga naar {$a->extendurl} om hun aanmelding te verlengen.';
$string['expirymessageenrolledsubject'] = 'Melding voor het vervallen van de aanmelding';
$string['expirymessageenrolledbody'] = 'Beste {$a->user},

Je aanmelding in cursus \'{$a->course}\' gaat vervallen op {$a->timeend}.

ls je hier een probleem mee hebt, neem dan contact op met {$a->enroller}.';
$string['purchase'] = 'Betalen';
$string['provider'] = 'Provider';
$string['name'] = 'Naam';
$string['minimum'] = 'Minimaal';
$string['maximum'] = 'Maximaal';
$string['instancedesc'] = 'Omschrijving';
$string['gateway_mollie_select_method'] = 'Klik op een van de betaalmethodes hieronder.';
$string['gateway_mollie'] = 'Provider: Mollie';
$string['gateway_mollie_desc'] = 'Offer your customers the payment methods Creditcard, SOFORT Banking, iDEAL, Bancontact/Mister Cash, Bank transfer, Bitcoin, PayPal or paysafecard. Mollie is known for reliability, transparency, nice API’s and ready-to-go modules.';
$string['gateway_mollie_apikey'] = 'API sleutel';
$string['gateway_mollie_link'] = 'Als u nog geen account heeft kunt u via <a href="{$a->link}">Registeren</a> een account aanmaken';
$string['gateway_mollie_send_button'] = 'Koop deze cursus';
$string['error:capability_config'] = 'Fout: Je heb de volgende toegang nodig, coursepayment/config';
$string['enrol_coursepayment_discount'] = 'Kortingscodebeheer';
$string['enrol_coursepayment_discount_desc'] = 'Korting codes kunnen aangemaakt worden via de onderstaande knop. <br/><br/><a href="{$a->link}" class="btn btn-small btn-primary">Kortingscodebeheer</a>';
$string['new:discountcode'] = 'Nieuwe kortingscode toevoegen';
// Table.
$string['th:code'] = 'Kortingscode';
$string['th:courseid'] = 'Cursus';
$string['th:start_time'] = 'Geldig vanaf';
$string['th:end_time'] = 'Geldig to';
$string['th:amount'] = 'Waarde';
$string['th:action'] = 'Actie';
// Form.
$string['form:allcourses'] = 'Volledige website';
$string['form:code'] = 'Kortignscode<br> (moet uniek zijn)';
$string['form:discountcode'] = 'Kortingscode';
$string['form:start_time'] = 'Geldig vanaf';
$string['form:end_time'] = 'Geldig to';
$string['form:save'] = 'Bewaar wijzigingen';
$string['form:amount'] = 'Bedrag van kortingscode';
$string['form:percentage'] = 'Percentage van kortingscode';
// Error.
$string['error:number_to_low'] = 'Dit nummer is te laag';
$string['error:price_wrongformat'] = 'Dit is geen nummer';
$string['error:code_not_unique'] = 'Kortingscode moet uniek zijn';
$string['error:no_record'] = 'Fout: Komt niet voor in onze database!';
$string['error:not_within_the_time_period'] = 'Fout: Niet geldig binnen deze tijd periode!';
$string['error:not_for_this_course'] = 'Fout: Deze kortingscode is bedoelt voor een andere cursus!';
// Settings.
$string['discount_code_desc'] = 'Als u een kortingscode heeft kunt u die hieronder invullen';
$string['discountcode_invalid'] = 'Fout: Deze kortingscode is onjuist, niet geldig meer of hoort niet bij deze cursus!';
$string['vatpercentages'] = 'BTW percentage inbegrepen in de kosten';
$string['invoicedetails'] = 'Invoice details';
$string['invoicedetails_desc'] = 'Deze velden zijn verplicht! Wanneer u dit leeg laat zijn uw facturen onjuist.';
$string['btw'] = 'BTW nummer';
$string['kvk'] = 'KvK nummer';
$string['place'] = 'Vestigingplaats';
$string['zipcode'] = 'Postcode';
$string['address'] = 'Adres';
$string['companyname'] = 'Naam organisatie';

// Mail.
$string['mail:invoice_subject_manual'] = 'Bedankt voor het kopen van: {$a->content_type} - {$a->fullcourse} / {$a->fullname}';
$string['mail:invoice_message_manual'] = 'Beste {$a->fullname},<br><br>
Er is een betaling gedaan op {$a->date} via {$a->method}.<br>
Bijgaand ontvangt u de factuur met factuurnummer {$a->invoice_number}.<br>
Het bedrag is {$a->cost} {$a->currency}.<br>
<br>
Met hartelijke groet,<br>
{$a->sitename}';

$string['mail:invoice_subject_student'] = 'Bedankt voor het kopen van: {$a->content_type} - {$a->fullcourse} / {$a->fullname}';
$string['mail:invoice_message_student'] = 'Beste {$a->fullname},<br><br>
Er is een betaling gedaan op {$a->date} via {$a->method}.<br>
Bijgaand ontvangt u de factuur met factuurnummer {$a->invoice_number}.<br>
Het bedrag is {$a->cost} {$a->currency}.<br>
<br>
Met hartelijke groet,<br>
{$a->sitename}';

$string['mail:invoice_subject_admin'] = 'Bedankt voor het kopen van: {$a->content_type} - {$a->fullcourse} / {$a->fullname}';
$string['mail:invoice_message_admin'] = 'Beste {$a->fullname},<br><br>
Er is een betaling gedaan op {$a->date} via {$a->method}.<br>
Bijgaand ontvangt u de factuur met factuurnummer {$a->invoice_number}.<br>
Het bedrag is {$a->cost} {$a->currency}.<br>
<br>
Met hartelijke groet,<br>
{$a->sitename}';

$string['mail:invoice_subject_teacher'] = 'Bedankt voor het kopen van: {$a->content_type} - {$a->fullcourse} / {Ja$a->fullname}';
$string['mail:invoice_message_teacher'] = 'Beste {$a->fullname},<br><br>
Er is een betaling gedaan op {$a->date} via {$a->method}.<br>
Bijgaand ontvangt u de factuur met factuurnummer {$a->invoice_number}.<br>
Het bedrag is {$a->cost} {$a->currency}.<br>
<br>
Met hartelijke groet,<br>
{$a->sitename}';
$string['coursepayment:report'] = 'Rapportage';
$string['coursepayment:config'] = 'Instellen';
$string['coursepayment:manage'] = 'Beheren';
$string['coursepayment:unenrol'] = 'Uitschrijven';
$string['coursepayment:unenrolself'] = 'Zichzelf uitschrijven';
$string['success_enrolled_activity'] = 'Betaling gelukt, u kunt nu de activiteit starten.';
$string['gateway_mollie_external_connector'] = 'External API connector';
$string['gateway_mollie_external_connector_desc'] = 'Deze functie moet worden uitgeschakeld! (Alleen in te schakelen als je weet wat je doet)';
$string['message:added_account'] = 'Je account is aangemaakt! Controleer je e-mail voor de details.';

$string['gateway_mollie_partner_id'] = 'Partner id';
$string['gateway_mollie_profile_key'] = 'Profile key';
$string['gateway_mollie_app_secret'] = 'App secret';
$string['form:newaccount'] = 'Een nieuw account maken op Mollie.nl';
$string['form:username'] = 'Gebruikersnaam (mag nog niet bestaan op Mollie)';
$string['form:name'] = 'Volledige naam';
$string['form:company_name'] = 'Bedrijfsnaam';
$string['form:email'] = 'E-mail';
$string['form:address'] = 'Straat en huisnummer';
$string['form:zipcode'] = 'Postcode';
$string['form:city'] = 'Stad';
$string['form:register'] = 'Registreren';
$string['form:search'] = 'Zoeken';

$string['form:make_selection'] = 'Maak een keuze';
$string['form:course'] = 'Cursus';
$string['form:payment_status'] = 'Betaalstatus';

$string['enrol_coursepayment_newaccount'] = 'Nieuw account aanmaken';
$string['custommails'] = 'Extra email adressen';
$string['custommails_desc'] = 'Voeg extra adressen toe die de factuur mogen ontvangen. (CSV format)';
$string['link_agreement'] = 'Algemene voorwaarden';
$string['link_agreement_desc'] = 'Link naar de algemene voorwaarde die gebruikers voor de aankoop moeten accorderen.
Wanneer u deze leeg laat, zal er geen algemene voorwaarde vertoond worden tijdens het aankoopprocedure.';
$string['agreement_label'] = 'Ik ga akkoord met de <a class="coursepayment-agreement-link" target="_blank" href="{$a->link}">
Algemene Voorwaarden</a>';
$string['js:claim_title'] = 'Betaalprovider Mollie koppelen aan Avetica';
$string['js:claim_desc'] = 'Uw account is nog niet aan Avetica gekoppeld u kunt dit oplossen door hieronder uw
Mollie gebruikersnaam en wachtwoord in te vullen.';
$string['js:username'] = 'Gebruikersnaam';
$string['js:password'] = 'Wachtwoord';
$string['js:delay'] = 'Uitstellen';
$string['js:connect'] = 'Koppelen';
$string['standalone_purchase_page'] = 'Losse betalingspagina';
$string['standalone_purchase_page_desc'] = 'Keuzescherm op een losse betalingspagina vertonen.';
$string['gateway_mollie_ideal_heading'] = 'IDEAL — KIES UW BANK';
$string['gateway_mollie_backlink'] = 'Terug naar <a href="/">{$a->fullname}</a>';

// Settings.
$string['settings:tab_invoicedetails'] = 'Factuurgegevens';
$string['settings:tab_gateway'] = 'Gateway';
$string['settings:tab_enrolment'] = 'Inschrijving';
$string['settings:tab_advanced'] = 'Geavanceerd';
$string['settings:tab_mail'] = 'E-mail';
$string['settings:tab_multiaccount'] = 'Multi-account';
$string['multi_account_heading'] = 'Meerdere Mollie accounts ondersteuning';
$string['multi_account'] = 'Meerdere accounts';
$string['multi_account_desc'] = 'Als deze functie is ingeschakeld, ondersteunen we meerdere Mollie accounts.
Dit is gebaseerd op een overeenkomende waarde in het profielveld.';
$string['setting:disabled_by_multi_account'] = 'Dit tabblad is uitgeschakeld, omdat Multi-account is ingeschakeld.';
$string['message:error_add_profile_field'] = 'Fout: maak een extra profielveld aan.';
$string['multi_account_profile_field'] = 'Profielveld';
$string['multi_account_profile_field_desc'] = 'Selecteer een profielveld dat we kunnen gebruiken.';
$string['th_name'] = 'Naam';
$string['th_action'] = 'Actie';
$string['th_profile_value'] = 'Profielveld waarde';
$string['btn:new'] = 'Nieuwe toevoegen';
$string['btn:report'] = 'Rapportage';
$string['btn:filter'] = 'Resultaten filteren';
$string['no_result'] = 'Geen resultaat';
$string['enrol_coursepayment_multi_account'] = 'Multi-account';
$string['form:name_multiaccount'] = 'Naam';
$string['form:profile_value'] = 'Profielveld waarde';
$string['form:btw'] = 'BTW';
$string['form:kvk'] = 'KvK';
$string['form:place'] = 'Plaats';
$string['form:mollie'] = 'Mollie account';
$string['form:company_info'] = 'Bedrijfs / factuur informatie';
$string['gateway_mollie_debug'] = 'Debugging';
$string['gateway_mollie_sandbox'] = 'Sandbox';
$string['form:multi_account'] = 'Multi-account instellingen';
$string['confirm_delete'] = 'Weet u zeker dat u dit item wilt verwijderen?';
$string['transaction_name'] = 'Transactie naam';
$string['transaction_name_desc'] = 'Ondersteund de volgende shortcodes: <br>
{invoice_number} : Factuurnummer<br>
{course} : Cursus<br>
{course_shortname} : Cursus korte naam<br>
{site} : Site naam<br>
{site_shortname} : Site korte naam<br>
{customtext2} : Extra betaal identifier';

$string['mollieconnect'] = 'IMPORTANT! Account is not connected. To enable this plugin you need to authorise our App. <br> <br> To do this click on the button below.';

// Privacy.
$string['privacy:metadata:enrol_coursepayment'] = 'Slaat de gebruikerstransactiegegevens op, dit staat toe om bij te
houden wat een gebruiker heeft gekocht.';

$string['privacy:metadata:enrol_coursepayment:userid'] = 'De ID van de gebruiker bij deze transactie.';
$string['privacy:metadata:enrol_coursepayment:gateway_transaction_id'] = 'De transactie-ID van de gebruiker.';
$string['privacy:metadata:enrol_coursepayment:orderid'] = 'De order-ID van de gebruiker.';
$string['privacy:metadata:enrol_coursepayment:instanceid'] = 'De instanceid die verwijst naar de aankoop.';
$string['privacy:metadata:enrol_coursepayment:addedon'] = 'De starttijd van de transactie.';
$string['privacy:metadata:enrol_coursepayment:courseid'] = 'De cursus id van de verbonden cursus.';

$string['customtext2'] = 'Extra identifier';
$string['report_include_none_payment_users'] = 'Rapportage alle gebruikers inladen';

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
$string['status:no_payments'] = 'Geen betalingen';

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
$string['fontcolour'] = 'Kleur';
$string['fontcolour_help'] = 'The colour of the font.';
$string['fontsize'] = 'Size';
$string['fontsize_help'] = 'The size of the font in points.';
$string['text'] = 'Tekst';
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

$string['orderdata:coursename'] = 'Naam';
$string['orderdata:vat'] = 'BTW';
$string['orderdata:total'] = 'Totaal';
$string['orderdata:subtotal'] = 'Subtotaal';
$string['orderdata:total'] = 'Totaal';
$string['orderdata:dummy_course'] = 'Voorbeeld naam';
$string['invoiceinfo:kvk'] = 'KvK';
$string['invoiceinfo:vat'] = 'BTW';
$string['invoiceinfo:invoice_number'] = 'Factuurnummer';
$string['invoiceinfo:ref'] = 'Ref.';
$string['invoiceinfo:date'] = 'Datum';
$string['invoice_pdf'] = 'Invoice PDF designer';
$string['invoice_desc'] = 'If you want to change the design of the invoice pdf, you can do that here.';
$string['pdf'] = 'PDF';

// Message providers.
$string['messageprovider:coursepayment_enrolment'] = 'Enrolment bericht';
$string['messageprovider:expiry_notification'] = 'Verval bericht';
$string['messageprovider:coursepayment_invoice'] = 'Factuur bericht';
