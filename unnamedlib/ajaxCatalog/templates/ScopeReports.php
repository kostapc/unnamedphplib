<?php
/**
 * Date: 11.06.13
 */

class ScopeReports {
    const TEMPLATES_DIR = 'pages/templates/';
    const TEMPLATES_FILES_EXT = '.php';

    var $scope;
    var $reports = array();
    var $reportsObjects = array();
    var $link;

    function __construct($inScopeName) {
        $this->scope = $inScopeName;
        $this->reports = list_directory(self::TEMPLATES_DIR.$this->scope, self::TEMPLATES_FILES_EXT);
        $this->link = new DBShell();
    }

    private function getObjectByClassName($scope, $className) {
        $fileName = $this->reports[$className];
        includeClassFile(self::TEMPLATES_DIR.$scope.'/'.$fileName);
        return get_object_by_class_name($className);
    }

    private function loadCachedObject($class) {
        $obj = $this->reportsObjects[$class];
        if($obj!=null) {
            return $obj;
        }
        $obj = self::getObjectByClassName($this->scope, $class);
        $this->reportsObjects[$class] = $obj;
        $obj->init($this->link);
        return $obj;
    }

    function getReport($reportName) {
        if(is_array($reportName)) {
            $retArray = array();
            foreach($reportName as $i => $class) {
                $retArray[$i] = $this->loadCachedObject($class);
            }
            return $retArray;
        }
        return $this->loadCachedObject($reportName);
    }

    function getAllReports() {
        return $this->getReport(array_keys($this->reports));
    }

}

function includeClassFile($classFile) {
    include($classFile);
}

?>