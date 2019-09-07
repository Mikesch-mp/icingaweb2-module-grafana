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
     * @param   action      {string}    The URL that caused the reload
     * @param   autorefresh {bool}      Whether the rendering is due to an auto-refresh
     *
     * @return  {string|null}           The content to be rendered or null, when nothing should be changed
     */
    Iframe.prototype.renderHook = function(content, $container, action, autorefresh) {
        if (!autorefresh) {
            return content;
        } else {
            var containerId = $container.attr('id');
            if (containerId === 'menu' || containerId === 'application-state') {
                return content;
            }
        }

        if (!$container.find('iframe').length) {
                return content;
        }

        var $children = $container.children();
        var $contentChildren = $container.find('.content').children();
        var $content = $('<div>').html(content);

        $content.children().each(function(idx) {
            var $child = $(this);
            if (!$child.hasClass('content')) {
                $($children[idx]).html($child.html());
            } else {
                $child.children().each(function(contentIdx) {
                    var $contentChild = $(this);
                    // All the iframes we use have this class, overwrite any others
                    if (!$contentChild.hasClass('module-grafana') || $contentChild.hasClass('quick-actions')) {
                        $($contentChildren[contentIdx]).html($contentChild.html());
                    }
                });
            }
        });
        $container.find('.controls .tabs').after('<div class="tabs-spacer">');
        return null;
    };

    Icinga.Behaviors = Icinga.Behaviors || {};

    Icinga.Behaviors.Iframe = Iframe;

}) (Icinga, jQuery);

