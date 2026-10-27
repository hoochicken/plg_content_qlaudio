/**
 * @package     plg_content_qlaudio
 *
 * @copyright   Copyright (C) 2022 Mareike Riegel
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

document.addEventListener('DOMContentLoaded', function () {

    /**
     * Get the list of audio filenames.
     *
     * @returns {string[]}
     */
    function initiateFileList() {
        return Array.from(document.querySelectorAll('.qlaudio-item'))
            .map(item => item.dataset.filename);
    }

    const fileList = initiateFileList();

    /**
     * Load and play the next audio file.
     */
    const loadNextFileAndPlay = () => {
        const audioPlayer = document.querySelector('.qlaudio audio');

        if (!audioPlayer || fileList.length === 0) {
            return;
        }

        const source = audioPlayer.querySelector('source');
        const currentAudio = source ? source.getAttribute('src') : audioPlayer.src;

        const indexCurrent = fileList.indexOf(currentAudio);
        const indexNew =
            indexCurrent !== -1 && indexCurrent < fileList.length - 1
                ? indexCurrent + 1
                : 0;

        qlaudioSetIntoPlayer(audioPlayer, fileList[indexNew]);
    };

    /**
     * Initialize the audio player listener.
     */
    function initiateAudioListener() {
        const audioPlayer = document.querySelector('.qlaudio audio');

        if (!audioPlayer) {
            return;
        }

        audioPlayer.addEventListener('ended', loadNextFileAndPlay);
    }

    initiateAudioListener();

    /**
     * Clicking an item in navigation selects an audio file.
     */
    document.body.addEventListener('click', function (event) {
        const selectedItem = event.target.closest('.qlaudio-item');

        if (!selectedItem) {
            return;
        }

        const filename = selectedItem.dataset.filename;
        const audioPlayer = selectedItem.closest('.qlaudio')
            ?.querySelector('audio');

        if (!audioPlayer) {
            return;
        }

        qlaudioSetItemActive(selectedItem);
        qlaudioSetIntoPlayer(audioPlayer, filename);
    });

    /**
     * Mark the selected item as active.
     *
     * @param {HTMLElement} selectedItem
     */
    function qlaudioSetItemActive(selectedItem) {
        selectedItem.parentElement
            .querySelectorAll('.qlaudio-item.active')
            .forEach(item => item.classList.remove('active'));

        selectedItem.classList.add('active');
    }

    /**
     * Load an audio file into the player and play it.
     *
     * @param {HTMLAudioElement} audioPlayer
     * @param {string} filename
     */
    function qlaudioSetIntoPlayer(audioPlayer, filename) {
        const source = audioPlayer.querySelector('source');

        if (source) {
            source.setAttribute('src', filename);
        } else {
            audioPlayer.setAttribute('src', filename);
        }

        audioPlayer.load();
        audioPlayer.currentTime = 0;
        audioPlayer.play().catch(error => {
            console.warn('Audio playback failed:', error);
        });
    }

});