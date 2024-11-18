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
 * AMD module used when rearranging a custom certificate.
 *
 * @module     enrol_coursepayment/rearrange-area
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/yui', 'core/fragment', 'enrol_coursepayment/dialogue', 'core/notification',
        'core/str', 'core/templates', 'core/ajax', 'core/log'],
    function ($, Y, fragment, Dialogue, notification, str, template, ajax, Log) {

        Log.debug('Rearrange area: Initialising');

        /**
         * RearrangeArea class.
         *
         * @param {String} selector The rearrange PDF selector
         */
        let RearrangeArea = function (selector) {
            this._node = $(selector);
            this._setEvents();
        };

        RearrangeArea.prototype.COURSEPAYMENT_REF_POINT_TOPLEFT = 0;
        RearrangeArea.prototype.COURSEPAYMENT_REF_POINT_TOPCENTER = 1;
        RearrangeArea.prototype.COURSEPAYMENT_REF_POINT_TOPRIGHT = 2;
        RearrangeArea.prototype.PIXELSINMM = 3.779527559055;

        RearrangeArea.prototype._setEvents = function () {
            this._node.on('click', '.element', this._editElement.bind(this));
        };

        RearrangeArea.prototype._editElement = function (event) {
            let elementid = event.currentTarget.id.substr(8);
            let contextid = this._node.attr('data-contextid');
            let params = {
                'elementid': elementid
            };

            fragment.loadFragment('enrol_coursepayment', 'editelement', contextid, params).done(function (html, js) {
                str.get_string('editelement', 'enrol_coursepayment').done(function (title) {
                    // TODO: moodle-core-formchangechecker is being depricated Moodle 4.0+.
                    // TODO: mod_customcert (where this is copied from) still has this in their 4.3 dev branch though.
                    Y.use('moodle-core-formchangechecker', function () {
                        new Dialogue(
                            title,
                            '<div id=\'elementcontent\'></div>',
                            this._editElementDialogueConfig.bind(this, elementid, html, js),
                            undefined,
                            true
                        );
                    }.bind(this));
                }.bind(this));
            }.bind(this)).fail(notification.exception);
        };

        RearrangeArea.prototype._editElementDialogueConfig = function (elementid, html, js, popup) {
            // Place the content in the dialogue.
            template.replaceNode('#elementcontent', html, js);

            // We may have dragged the element changing it's position.
            // Ensure the form has the current up-to-date location.
            this._setPositionInForm(elementid);

            // Add events for when we save, close and cancel the page.
            let body = $(popup.getContent());
            body.on('click', '#id_submitbutton', function (e) {
                // Do not want to ask the user if they wish to stay on page after saving.
                M.core_formchangechecker.reset_form_dirty_state();
                // Save the data.
                this._saveElement(elementid).then(function () {
                    // Update the DOM to reflect the adjusted value.
                    this._getElementHTML(elementid).done(function (html) {
                        let elementNode = this._node.find('#element-' + elementid);
                        let refpoint = parseInt($('#id_refpoint').val());
                        let refpointClass = '';
                        if (refpoint == this.COURSEPAYMENT_REF_POINT_TOPLEFT) {
                            refpointClass = 'refpoint-left';
                        } else if (refpoint == this.COURSEPAYMENT_REF_POINT_TOPCENTER) {
                            refpointClass = 'refpoint-center';
                        } else if (refpoint == this.COURSEPAYMENT_REF_POINT_TOPRIGHT) {
                            refpointClass = 'refpoint-right';
                        }
                        elementNode.empty().append(html);
                        // Update the ref point.
                        elementNode.removeClass();
                        elementNode.addClass('element ' + refpointClass);
                        elementNode.attr('data-refpoint', refpoint);
                        // Move the element.
                        let posx = $('#editelementform #id_posx').val();
                        let posy = $('#editelementform #id_posy').val();
                        this._setPosition(elementid, refpoint, posx, posy);
                        // All done.
                        popup.close();
                    }.bind(this));
                }.bind(this)).fail(notification.exception);
                e.preventDefault();
            }.bind(this));

            body.on('click', '#id_cancel', function (e) {
                popup.close();
                e.preventDefault();
            });
        };

        RearrangeArea.prototype._setPosition = function (elementid, refpoint, posx, posy) {
            let element = Y.one('#element-' + elementid);

            posx = Y.one('#pdf').getX() + posx * this.PIXELSINMM;
            posy = Y.one('#pdf').getY() + posy * this.PIXELSINMM;
            let nodewidth = parseFloat(element.getComputedStyle('width'));
            let maxwidth = element.width * this.PIXELSINMM;

            if (maxwidth && (nodewidth > maxwidth)) {
                nodewidth = maxwidth;
            }

            switch (refpoint) {
                case this.COURSEPAYMENT_REF_POINT_TOPCENTER:
                    posx -= nodewidth / 2;
                    break;
                case this.COURSEPAYMENT_REF_POINT_TOPRIGHT:
                    posx = posx - nodewidth + 2;
                    break;
            }

            element.setX(posx);
            element.setY(posy);
        };

        RearrangeArea.prototype._setPositionInForm = function (elementid) {
            let posxelement = $('#editelementform #id_posx');
            let posyelement = $('#editelementform #id_posy');

            if (posxelement.length && posyelement.length) {
                let element = Y.one('#element-' + elementid);
                let posx = element.getX() - Y.one('#pdf').getX();
                let posy = element.getY() - Y.one('#pdf').getY();
                let refpoint = parseInt(element.getData('refpoint'));
                let nodewidth = parseFloat(element.getComputedStyle('width'));

                switch (refpoint) {
                    case this.COURSEPAYMENT_REF_POINT_TOPCENTER:
                        posx += nodewidth / 2;
                        break;
                    case this.COURSEPAYMENT_REF_POINT_TOPRIGHT:
                        posx += nodewidth;
                        break;
                }

                posx = Math.round(parseFloat(posx / this.PIXELSINMM));
                posy = Math.round(parseFloat(posy / this.PIXELSINMM));

                posxelement.val(posx);
                posyelement.val(posy);
            }
        };

        RearrangeArea.prototype._getElementHTML = function (elementid) {
            // Get the variables we need.
            let templateid = this._node.attr('data-templateid');

            // Call the web service to get the updated element.
            let promises = ajax.call([{
                methodname: 'enrol_coursepayment_get_element_html',
                args: {
                    templateid: templateid,
                    elementid: elementid
                }
            }]);

            // Return the promise.
            return promises[0];
        };

        RearrangeArea.prototype._saveElement = function (elementid) {
            // Get the variables we need.
            let templateid = this._node.attr('data-templateid');
            let inputs = $('#editelementform').serializeArray();

            // Call the web service to save the element.
            let promises = ajax.call([{
                methodname: 'enrol_coursepayment_save_element',
                args: {
                    templateid: templateid,
                    elementid: elementid,
                    values: inputs
                }
            }]);

            // Return the promise.
            return promises[0];
        };

        return {
            init: function (selector) {
                new RearrangeArea(selector);
            }
        };
    }
);
