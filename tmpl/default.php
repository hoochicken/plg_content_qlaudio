<?php
/**
 * @package       plg_content_qlaudio
 * @copyright     Copyright (C) 2020 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

//no direct access
use Joomla\Registry\Registry;

defined('_JEXEC') or die ('Restricted Access');
/** @var stdClass $data */
/** @var int $counter */
/** @var Registry $params */

if (false !== $data->message) {
    echo '<div class="qlaudio alert alert-notice">' . $data->message . '</div>';
    return;
}

$id = 'qlaudio' . $counter;
//echo '<pre>';print_r($data->states);die;

if (isset($data->states['id']) && '' != $data->states['id']) {
    $id = $data->states['id'];
}
$tag1 = $tag2 = 'div';

$strDefault = '';
$strAutoplay = '';
$strControls = (1 == $data->states['controls']) ? 'controls' : '';
if (1 == $data->states['autostart']) {
    $strDefault = $data->files[0]['path'];
    $strAutoplay = 'autoplay';
}

if ('ul' == $params->get('tag', 'div')) {
    $tag1 = 'ul';
    $tag2 = 'li';
}; ?>
<div id="<?php echo $id; ?>" class="qlaudio <?php echo $data->states['class']; ?>">
    <?php if ('' != $data->states['title']) : ?>
        <h<?php echo $params->get('headline', 4); ?>>
            <?php echo $data->states['title']; ?>
        </h<?php echo $params->get('headline', 4); ?>>
    <?php endif; ?>
    <<?php echo $tag1; ?> class="qlaudioContainer navi">
    <?php foreach ($data->files as $k => $v) : ?>
    <<?php echo $tag2; ?> class="qlaudio-item" data-filename="<?php echo $v['path']; ?>">
    <?php if (1 == $params->get('iconPlay', 1)) echo '<span class="icon-play"> </span>'; ?>
    <?php echo $v['title']; ?>
</<?php echo $tag2; ?>>
<?php endforeach; ?>
</<?php echo $tag1; ?>>
<div class="qlaudioContainer player">
    <audio id="qlaudio_player<?php echo $counter; ?>" <?php echo $strAutoplay; ?> <?php echo $strControls; ?>
           preload="auto">
        <source id="qlaudio_source<?php echo $counter; ?>"
                src="<?php echo $strDefault; ?>" type="audio/mpeg"
        />
        <?php echo JText::_('PLG_CONTENT_QLAUDIO_MSG_BROWSERDOESNTLIKEMEDIATAG'); ?>
    </audio>
</div>
</div>