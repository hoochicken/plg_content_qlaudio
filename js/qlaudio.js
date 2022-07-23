/**
 * @package     plg_content_qlaudio
 *
 * @copyright   Copyright (C) 2020 Mareike Riegel
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

jQuery(document).ready(function () {
    jQuery('body').on('click', '.qlaudio-item', function(el) {
        // get current filename
        var objItem = jQuery(el.currentTarget);
        var strFilename = objItem.attr('data-filename');

        // add class active on item
        objItem.siblings().removeClass('active');
        objItem.addClass('active');

        // get audio tag
        var objAudioplayer = objItem.closest('.qlaudio').find('audio');

        qlaudioPlay(objAudioplayer, strFilename);
    });

    /* this dows not work the least; for javascript cannot "see"click event on audio tag, wtf? */
    jQuery('body').on('click', '.qlaudioContainer.player', function(el) {
        var objContainer = jQuery(el.currentTarget).children('.qlaudioContainer.player');
        var objAudio = objContainer.siblings('navi').children('.qlaudio-item:first-child');
        var strFilename = objAudio.attr('data-filename');
        var objAudioplayer = objContainer.find('audio');

        qlaudioPlay(objAudioplayer, strFilename);
    });

    /**
     *
     * @param objAudioplayer
     * @param strFilename
     */
    function qlaudioPlay(objAudioplayer, strFilename)
    {
        // set new source, reload audio element and play
        objAudioplayer.find('source').attr('src', strFilename);
        objAudioplayer[0].load();
        objAudioplayer[0].currentTime = 0;
        objAudioplayer[0].play();
    }
});