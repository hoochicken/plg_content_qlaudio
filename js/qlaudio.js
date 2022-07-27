/**
 * @package     plg_content_qlaudio
 *
 * @copyright   Copyright (C) 2022 Mareike Riegel
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

jQuery(document).ready(function () {

    /**
     *
     * @param selectedItem
     */
    function initiateFileList(selectedItem)
    {
        let fileListTemp = new Array;
        jQuery('.qlaudio-item').each(function () {
            // console.log($(this))
            fileListTemp.push($(this).attr('data-filename'));
        });
        return fileListTemp;
    }

    const fileList = initiateFileList();

    const loadNextFileAndPlay = () => {
        let objAudioplayer = jQuery('.qlaudio').find('audio');
        let currentAudio = objAudioplayer.find('source').attr('src');
        let indexCurrent = fileList.indexOf(currentAudio) ;
        let indexNew = indexCurrent < fileList.length && indexCurrent !== -1 ? indexCurrent + 1 : 0;

        objAudioplayer.src = fileList[indexNew];
        objAudioplayer.find('source').attr('src', fileList[indexNew]);
        objAudioplayer[0].play();
    };

    function initiateAudioListener() {
        let objAudioplayer = jQuery('.qlaudio').find('audio');
        objAudioplayer[0].addEventListener('ended', loadNextFileAndPlay);
    }

    initiateAudioListener();

    /**
     * Clicking an item in navigation = selecting an audio file
     */
    jQuery('body').on('click', '.qlaudio-item', function(el) {
        // get current filename
        let objItem = jQuery(el.currentTarget);
        let strFilename = objItem.attr('data-filename');
        let objAudioplayer = objItem.closest('.qlaudio').find('audio');

        // get audio tag
        qlaudioSetItemActive(jQuery(el.currentTarget))
        qlaudioSetIntoPlayer(objAudioplayer, strFilename);
    });

    /**
     *
     * @param selectedItem
     */
    function qlaudioSetItemActive(selectedItem)
    {
        // add class active on item
        selectedItem.siblings().removeClass('active');
        selectedItem.addClass('active');
    }

    /**
     * load audio files into player
     * e. g. when clicking on item
     *
     * @param objAudioplayer
     * @param strFilename
     */
    function qlaudioSetIntoPlayer(objAudioplayer, strFilename)
    {
        // set new source, reload audio element and play
        objAudioplayer.find('source').attr('src', strFilename);
        objAudioplayer[0].load();
        objAudioplayer[0].currentTime = 0;
        objAudioplayer[0].play();
    }

});