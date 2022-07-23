<?php
/**
 * @package       plg_content_qlaudio
 * @copyright     Copyright (C) 2020 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

//no direct access
use Joomla\CMS\Factory;

defined('_JEXEC') or die ('Restricted Access');

jimport('joomla.plugin.plugin');

class plgContentQlaudio extends JPlugin
{

    /*TODO
    text.txt (wenn empty, dass Dateinamen; ggf. im Ordner wird angelegt, generiere Liste (Button?) Über den EDITOR-Button; Sicherheitsoption, dass es nicht einfach überschrieben wird array_merge?)
    */
    /*{qlaudio folder="images/audio" limit="20" limit="2" layout="default" id="defaultId" class="defaultClass" autostart="false" title="some title" } */
    protected string $str_call_start = 'qlaudio';
    protected array $arr_attributes = [
        'folder',
        'folderRoot',
        'limit',
        'layout',
        'id',
        'class',
        'autostart',
        'controls',
        'filesAllowed',
        'alterFileName',
        'filenameStrip',
        'ordering',
        'title',
    ];
    protected array $default = [
        'folder' => '',
        'folderRoot' => '',
        'limit' => 20,
        'layout' => 'default',
        'id' => '',
        'class' => 'defaultClass',
        'autostart' => 0,
        'controls' => 0,
        'filesAllowed' => [],
        'alterFileName' => [],
        'ordering' => 'title',
        'filenameStrip' => 0,
        'title' => '',
    ];
    protected array $states = [];
    public $params;
    public $data;
    protected $matches;

    /**
     * plgContentQlaudio constructor. setting language
     * @param $subject
     * @param $config
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    /**
     * onContentPrepare :: some kind of controller of plugin
     * @param $context
     * @param $article
     * @param $params
     * @param ?int $page
     * @return bool
     * @throws Exception
     */
    public function onContentPrepare($context, &$article, &$params, ?int $page = 0)
    {
        if ($context == 'com_finder.indexer') {
            return true;
        }

        // if no tags found => return
        if (!$this->checkTags($article->text)) {
            return true;
        }

        // if no matches are found => return
        if (!isset($this->matches[0]) && 0 >= $this->matches) {
            return true;
        }

        // javascript
        $article->text = $this->clearTags($article->text);
        if (1 == $this->params->get('jquery', 0)) {
            JHtml::_('jquery.framework');
        }

        $app = Factory::getApplication();
        $wa = $app->getDocument()->getWebAssetManager();
        $wa->registerAndUseScript('qlaudio', 'media/plugins/plg_content_qlaudio/qlaudio.js', [], ['defer' => true], ['qlaudio']);


        // stylesheets
        JHtml::_('stylesheet', 'plg_content_qlaudio/qlaudio.css', ['relative' => true]);
        if (1 == $this->params->get('addStyles', 0)) {
            JFactory::getDocument()->addStyleDeclaration($this->setStyles());
        }

        $this->setStates();
        $this->setData();
        foreach ($this->data as $k => $v) {
            $v->message = false;
            if (!isset($v->files) || 0 >= count($v->files)) {
                $v->message = JText::sprintf('PLG_CONTENT_QLAUDIO_MSG_NOFILESINFOLDERFOUND', $v->states['folderRoot'] . '/' . $v->states['folder']);
                $strFilesAllowed = $v->states['filesAllowed'];
                if (0 == count($strFilesAllowed)) {
                    $strFilesAllowed = JText::_('PLG_CONTENT_QLAUDIO_NONE');
                }
                $v->message .= '<br />' . JText::sprintf('PLG_CONTENT_QLAUDIO_MSG_FILEENDINGSALLOWED', implode(',', $strFilesAllowed));
                $article->text = str_replace($v->string, $this->getHtml($k, $v), $article->text);
                continue;
            }
            $article->text = str_replace($v->string, $this->getHtml($k, $v), $article->text);
        }
    }

    /**
     * @param int $counter
     * @param $data
     * @return false|string
     * @throws Exception
     */
    private function getHtml(int $counter, $data): bool|string
    {
        ob_start();
        $params = $this->params;
        $path = JPluginHelper::getLayoutPath('content', $this->_name, $data->states['layout']);
        require $path;
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * method to get attributes
     */
    private function setStates()
    {
        foreach ($this->arr_attributes as $k => $v) {
            $this->states[$v] = $this->params->get($v, $this->default[$v]);
        }
    }

    /**
     *
     */
    private function setData()
    {
        $this->data = new stdClass();
        foreach ($this->matches[0] as $k => $v) {
            $this->data->$k = new stdClass();
            $this->data->$k->string = $this->matches[0][$k];
            $this->data->$k->states = $this->states;
            $regex = '/([a-z]*)="([^"]*)"/i';
            preg_match_all($regex, $this->matches[1][$k], $attributes);
            //$this->printer($attributes);
            //$this->printer($this->data->$k);
            foreach ($attributes[0] as $k2 => $v2) {
                $key = $attributes[1][$k2];
                $value = $attributes[2][$k2];
                if ('filesAllowed' == $key) {
                    $value = explode('|', $value);
                }
                if ('alterFileName' == $key) {
                    $value = explode('|', $value);
                }
                $this->data->$k->states[$key] = $value;
            }
            $folder = str_replace('//', '/', $this->data->$k->states['folderRoot'] . '/' . $this->data->$k->states['folder']);
            $this->data->$k->files = $this->getFilesFromFolder($folder, $this->data->$k->states);
            //$this->printer($this->data);die;
            //$this->data->$k->files=$this->getFilesFromFolder($folder);
            if (!is_countable($this->data->$k->files) || 0 === count($this->data->$k->files)) {
                continue;
            }
            $this->data->$k->files = $this->sortFiles($this->data->$k->files, $this->data->$k->states['ordering']);
            $this->data->$k->files = array_slice($this->data->$k->files, 0, $this->data->$k->states['limit']);
        }
    }

    /**
     * @param $var
     * @param bool $die
     */
    public function printer($var, bool $die = true)
    {
        echo '<pre>';
        print_r($var);
        if ($die) exit;
    }

    /**
     * method to get images from folder
     * @param string $folder path to folder
     * @param $states
     * @return array|null array with pic info like path, title and description
     */
    public function getFilesFromFolder(string $folder, $states): ?array
    {
        $arrFilesAllowed = $states['filesAllowed'];
        $arrAlterFileName = $states['alterFileName'];
        if (!$dir = @opendir($folder)) {
            return null;
        }

        // get stripp from params
        $numStripFilenameFront = (int)$this->params->get('filenameStrip', 0);
        // overwrite params, if attribute in tag is given
        $numStripFilenameFront = $states['filenameStrip'] ?? $numStripFilenameFront;

        $files = [];
        $i = 0;
        while (false !== ($file = readdir($dir))) {
            $regex = implode('|', $arrFilesAllowed);
            $regex = '/.+\.(' . $regex . ')$/i';
            if (preg_match($regex, $file) && '.' != $file && '..' != $file) {
                if (false !== filesize(JPATH_ROOT . '/' . $folder . '/' . $file)) {
                    $files[$i]['path'] = '/' . $folder . '/' . $file;
                    $fileAltered = $this->alterFilename($file, $arrAlterFileName, $numStripFilenameFront);
                    $files[$i]['title'] = $fileAltered;
                    $files[$i]['filename'] = $file;
                }
            }
            $i++;
        }
        closedir($dir);
        return $files;
    }

    /**
     * @param string $filename
     * @param array $alterFileName
     * @param int $numStripFilenameFront
     * @return string
     */
    private function alterFilename(string $filename, array $alterFileName, int $numStripFilenameFront = 0): string
    {
        if (in_array('ucwords', $alterFileName)) {
            $filename = ucwords($filename);
        }
        if (in_array('nodash', $alterFileName)) {
            $filename = str_replace('-', ' ', $filename);
        }
        if (in_array('stripfileending', $alterFileName)) {
            $filename = substr($filename, 0, strpos($filename, '.'));
        }
        if (0 < $numStripFilenameFront) {
            $filename = substr($filename, $numStripFilenameFront);
        }

        return $filename;
    }

    /**
     * @param array $files
     * @param string $ordering
     * @return mixed
     */
    private function sortFiles(array $files, string $ordering)
    {
        if ('natural' == $ordering) {
            natcasesort($files);
        } elseif ('title' == $ordering) {
            sort($files);
        } else {
            shuffle($files);
        }
        return $files;
    }

    /**
     * @param string $str
     * @return mixed
     */
    private function clearTags(string $str): string
    {
        $str = str_replace('<p>{' . $this->str_call_start . '', '{' . $this->str_call_start . '', $str);
        $regex = '!{' . $this->str_call_start . '\s(.*?)}</p>!';
        preg_match_all($regex, $str, $matches, PREG_SET_ORDER);
        if (0 < count($matches)) {
            foreach ($matches as $k => $v) $str = str_replace($v[0] . '</p>', $v[0], $str);
        }
        return $str;
    }

    /**
     * @param $str
     * @return bool
     */
    private function checkTags($str): bool
    {
        $return = false;
        $regex = '!{' . $this->str_call_start . '\s(.*?)}!';
        if (false !== preg_match_all($regex, $str, $this->matches)) {
            $return = true;
        }
        return $return;
    }

    /**
     * @return string
     */
    private function setStyles(): string
    {
        $styles = [];
        $styles[] = '.qlaudio ';
        $styles[] = '{';
        $styles[] = 'float:' . $this->params->get('float', 'left') . ';';
        $styles[] = 'background:' . $this->params->get('background', '#fef0f0') . ';';
        $styles[] = 'padding:' . $this->params->get('padding', 20) . 'px;';
        $styles[] = 'border-radius:' . $this->params->get('borderRadius', 20) . 'px;';
        $styles[] = 'border:' . $this->params->get('borderWidth', 2) . 'px ' . $this->params->get('borderStyle', 'solid') . ' ' . $this->params->get('borderColor', '#fee0e0') . ';';
        $styles[] = '}';
        return implode("\n", $styles);
    }
}