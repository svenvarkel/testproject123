<?php
/**
 * @package    Mageflow
 * @subpackage Connect
 */

/**
 * MageFlow Media helper indexes WYSIWYG images and
 * calculates diffs of image directory contents
 *
 * PLEASE READ THIS SOFTWARE LICENSE AGREEMENT ("LICENSE") CAREFULLY
 * BEFORE USING THE SOFTWARE. BY USING THE SOFTWARE, YOU ARE AGREEING
 * TO BE BOUND BY THE TERMS OF THIS LICENSE.
 * IF YOU DO NOT AGREE TO THE TERMS OF THIS LICENSE, DO NOT USE THE SOFTWARE.
 *
 * Full text of this license is available @license
 *
 * @license    http://mageflow.com/license/connector/eula.txt MageFlow EULA
 * @version    1.0
 * @author     MageFlow
 * @copyright  2013 MageFlow http://mageflow.com/
 *
 * @package    Mageflow
 * @subpackage Connect
 * @category   MFX
 */

class Mageflow_Connect_Helper_Media extends Mage_Core_Helper_Abstract
{
    private $cli = false;
    private $verbose = false;
    const PROGRESS_MARKER = '=';
    const ADD_MARKER = '+';
    const DEL_MARKER = '-';

    /**
     * @param boolean $cli
     */
    public function setCli($cli)
    {
        $this->cli = $cli;
    }

    /**
     * @return boolean
     */
    public function getCli()
    {
        return $this->cli;
    }

    /**
     * @param boolean $verbose
     */
    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
    }

    /**
     * @return boolean
     */
    public function getVerbose()
    {
        return $this->verbose;
    }

    private function out($out)
    {
        if ($this->getCli() && $this->getVerbose()) {
            echo $out;
        }
    }

    /**
     * Refreshes media index
     *
     * @param bool $forceSave
     *
     * @return int
     */
    public function refreshIndex($forceSave = false)
    {
        $baseDirList = $this->getMediaDirectoryList();

        /**
         * @var Mageflow_Connect_Model_Resource_Media_Index_Collection $mediaIndexModelCollection
         */
        $mediaIndexModelCollection = Mage::getModel('mageflow_connect/media_index')->getCollection();

        $this->out(
            sprintf("%s items in Media Index before reindexing%s", $mediaIndexModelCollection->count(), PHP_EOL)
        );

        foreach ($baseDirList as $baseDir) {
            /**
             * @var Mage_Cms_Model_Wysiwyg_Images_Storage $model
             */
            $model = Mage::getModel('cms/wysiwyg_images_storage');
            $fileCollection = $model->getFilesCollection($baseDir);

            $this->out(sprintf("Re-indexing %s ... Found %s items\n", $baseDir, $fileCollection->count()));

            /**
             * @var Mage_Cms_Model_Wysiwyg_Images_Storage $fileModel
             */
            $i = 0;
            foreach ($fileCollection as $fileModel) {

                if ($i > 0 && $i % 100 == 0) {
                    $this->out(sprintf(" %s %s", $i, PHP_EOL));
                }

                if (!$mediaIndexModelCollection->fileIsCurrent($fileModel)) {
                    $mediaIndexModel = $this->createMediaIndexModel($fileModel);
                    $mediaIndexModel->save();
                    $mediaIndexModelCollection->addItem($mediaIndexModel);
                    $this->out(self::ADD_MARKER);
                } else {
                    $this->out(self::PROGRESS_MARKER);
                }
                $i++;
            }
            $this->out(sprintf(" %s %s", $i, PHP_EOL));
            $this->out(sprintf("Re-indexed %s%s", $baseDir, PHP_EOL));
        }
        if ($forceSave) {
            $mediaIndexModelCollection->save();
        }
        /**
         * @var Mageflow_Connect_Model_Media_Index $mediaIndexModel
         */
        //2nd loop for removing files from index that don't exists on the disk
        $this->out(sprintf("Searching for deleted files ... %s", PHP_EOL));

        $i = 0;
        foreach ($mediaIndexModelCollection as $mediaIndexModel) {

            if ($i > 0 && $i % 100 == 0) {
                $this->out(sprintf(" %s %s", $i, PHP_EOL));
            }
            if (!file_exists($mediaIndexModel->getFilename())) {
                $mediaIndexModel->delete();
                $this->out(self::DEL_MARKER);
            } else {
                $this->out(self::PROGRESS_MARKER);
            }

            $i++;
        }
        $this->out(PHP_EOL);

        $mediaIndexModelCollection->clear();

        $this->out(
            sprintf("%s items in Media Index after reindexing%s", $mediaIndexModelCollection->load()->count(), PHP_EOL)
        );

        $mediaIndexModelCollection->clear();

        return 0;
    }

    /**
     * Returns list of directories to be searched for wysiwyg media files
     *
     * @return array
     */
    public function getMediaDirectoryList()
    {
        $baseDir = Mage::getBaseDir('media') . DS . 'wysiwyg';
        $baseDirList = array($baseDir);
        /**
         * @var Varien_Data_Collection_Filesystem $fileSystemModel
         */
        $fileSystemModel = Mage::getModel('Varien_Data_Collection_Filesystem');
        $fileSystemModel->addTargetDir($baseDir);
        $fileSystemModel->setCollectDirs(true);
        $fileSystemModel->setCollectFiles(false);

        $dirList = $fileSystemModel->loadData();
        foreach ($dirList as $dirObject) {
            $baseDirList[] = $dirObject->getFilename();
        }
        return $baseDirList;
    }

    /**
     * Initializes index. I.e it flushes index, then reads and saves current
     * state of files under wysiwyg folder
     */
    public function initializeIndex()
    {
        $baseDirList = $this->getMediaDirectoryList();

        /**
         * @var Mageflow_Connect_Model_Resource_Media_Index_Collection $mediaIndexModelCollection
         */
        $mediaIndexModelCollection = Mage::getModel('mageflow_connect/media_index')->getCollection();
        foreach ($mediaIndexModelCollection as $mediaIndexItem) {
            $mediaIndexItem->delete();
        }
        $mediaIndexModelCollection->clear();

        foreach ($baseDirList as $baseDir) {
            /**
             * @var Mage_Cms_Model_Wysiwyg_Images_Storage $model
             */
            $model = Mage::getModel('cms/wysiwyg_images_storage');
            $fileCollection = $model->getFilesCollection($baseDir);

            $this->out(sprintf("Re-indexing %s ... Found %s items\n", $baseDir, $fileCollection->count()));

            $i = 0;
            foreach ($fileCollection as $fileModel) {

                if ($i > 0 && $i % 100 == 0) {
                    $this->out(sprintf(" %s %s", $i, PHP_EOL));
                }

                $mediaIndexModel = $this->createMediaIndexModel($fileModel);

                $mediaIndexModel->save();

                $this->out(self::ADD_MARKER);
                $i++;

            }

            $this->out(PHP_EOL);

        }

        $this->out(
            sprintf("%s items in Media Index after reindexing%s", $mediaIndexModelCollection->load()->count(), PHP_EOL)
        );

    }

    /**
     * Creates media index model from filemodel
     *
     * @param $fileModel
     *
     * @return Mageflow_Connect_Model_Media_Index
     */
    private function createMediaIndexModel($fileModel)
    {
        /**
         * @var Mageflow_Connect_Model_Media_Index $mediaIndexModel
         */
        $mediaIndexModel = Mage::getModel('mageflow_connect/media_index');
        $mediaIndexModel->setData($fileModel->getData());
        $mediaIndexModel->setHash($fileModel->getId());

        //FIX Magento file URL bug
        $absolutePath = str_replace(Mage::getBaseDir('base'), '', $mediaIndexModel->getFilename());

        $mediaIndexModel->setPath($absolutePath);

        $absoluteUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . ltrim($absolutePath, '/');
        $mediaIndexModel->setUrl($absoluteUrl);

        $date = new Zend_Date();
        $now = $date->now();
        $mediaIndexModel->setCreatedAt($now);
        $mediaIndexModel->setUpdatedAt($now);

        $imageInfo = @getimagesize($mediaIndexModel->getFilename());
        if (is_array($imageInfo)) {
            $mediaIndexModel->setType($imageInfo['mime']);
        }

        $size = @filesize($mediaIndexModel->getFilename());

        $mediaIndexModel->setSize($size);
        return $mediaIndexModel;
    }

}