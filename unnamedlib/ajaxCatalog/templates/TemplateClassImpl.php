<?php
/**
 * Date: 13.06.13
 */

class TemplateClassImpl {
    const HTML_REQUEST = 1;
    const JSON_REQUEST = 2;

    const DEFAULT_GROUP_ID = 'reports_group';
    const KEY_APPENDIX = '_df';

    var $link;
    var $cache = null;
    var $name;

    function getReportName() {
        return $this->name;
    }

    function init(&$inLink) {
        $this->link = $inLink;
        if (ConfigContainer::CACHE_TIME > 0) {
            $this->cache = new LocalCaching();
        }
        $this->name = get_called_class();
    }

    function getCached($key = self::KEY_APPENDIX) {
        if ($this->cache==null) {
            return null;
        }
        return $this->cache->get($this->name.$key,self::DEFAULT_GROUP_ID);
    }

    function putToCache($data, $key = self::KEY_APPENDIX) {
        if ($this->cache==null) {
            return null;
        }
        $this->cache->save($this->name.$key, $data, self::DEFAULT_GROUP_ID);
    }

    function drawBody() {

    }

    function getTitle() {

    }

    function drawPreviewCell() {
        echo '<b>This is preview for "'.$this->getReportName().'"</b>';
    }

}
?>