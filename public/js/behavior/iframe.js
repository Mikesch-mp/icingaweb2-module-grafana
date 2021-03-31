/* Garrett Seward | @spectralsun | GPLv2 */

/**
 * Icinga.Behavior.Iframe
 *
 * Merges content for containers when iframes are present to prevent reloading.
 */
(function(Icinga, $) {
    'use strict';

    function Iframe(icinga) {
        Icinga.EventListener.call(this, icinga);
    }

    Iframe.prototype = new Icinga.EventListener();

    /**
     * Mutates the HTML before it is placed in the DOM after a reload
     *
     * @param   content     {string}    The content to be rendered
     * @param   $container  {jQuery}    The target container
     * @param   action      {string}    How the content should be inserted (`replace` or `append`)
     * @param   autorefresh {bool}      Whether the rendering is due to an auto-refresh
     * @param   autoSubmit  {bool}      Whether the rendering is due to an auto-submit
     *
     * @return  {string|null}           The content to be rendered or null, when nothing should be changed
     */
    Iframe.prototype.renderHook = function(content, $container, action, autorefresh, autoSubmit) {
        if (! autorefresh) {
            return content;
        }

        var containerId = $container.attr('id');
        if (containerId === 'menu' || containerId === 'application-state') {
            // Exit early if there's no chance to find a grafana container
            return content;
        }

        var $grafanaContainer = $container.find('.module-grafana');
        if (! $grafanaContainer.length || ! $grafanaContainer.find('iframe').length) {
            // Exit early if there's still no grafana container or no iframe in it
            return content;
        }

        var $updates = $('<div>').html(content);

        // Update controls and such
        var $existingContent = $container.children('.content');
        var newRootChildren = Array.prototype.slice.call($updates[0].children);
        this.replaceSiblings(newRootChildren, '.content', $existingContent[0]);

        var newChildren;
        if ($container.is('.module-icingadb')) {
            newChildren = Array.prototype.slice.call(
                $updates.children('.content').children('.host-detail')[0].children
            );
        } else {
            newChildren = Array.prototype.slice.call($updates.children('.content')[0].children);
        }

        // Update main content
        this.replaceSiblings(newChildren, '.module-grafana', $grafanaContainer[0]);

        return null;
    };

    /**
     * Replace all siblings of the given refChild but leave the refChild itself alone
     *
     * @param {Array} newSiblings The new siblings, including a node representing the location of the refChild
     * @param {string} refIdentifier A CSS selector to identify the node in newSiblings representing refChild
     * @param {Node} refChild
     */
    Iframe.prototype.replaceSiblings = function(newSiblings, refIdentifier, refChild) {
        // Remove the existing siblings first
        while (refChild.previousSibling) {
            refChild.previousSibling.remove();
        }
        while (refChild.nextSibling) {
            refChild.nextSibling.remove();
        }

        // Then insert the updated siblings, but leave the reference node alone
        var refPassed = false;
        $.each(newSiblings, function (_, newSibling) {
            if (newSibling.matches(refIdentifier)) {
                refPassed = true;
            } else if (! refPassed) {
                refChild.parentNode.insertBefore(newSibling, refChild);
            } else {
                refChild.parentNode.appendChild(newSibling);
            }
        });
    };

    Icinga.Behaviors = Icinga.Behaviors || {};

    Icinga.Behaviors.Iframe = Iframe;

}) (Icinga, jQuery);

