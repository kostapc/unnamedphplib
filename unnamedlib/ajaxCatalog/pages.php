<?php
/**
 * Date: 10.06.13
 */

$scopeName = clear_string($_GET['page']);
$linkBase = '?page='.$scopeName;
$scope = new ScopeReports($scopeName);

if(isset($_GET['rkey']) || isset($_GET['ajax'])) {

    $rkey = clear_string($_GET['rkey']);
    $report = $scope->getReport($rkey);
    $ajax = $_GET['ajax'];
    unset($_GET['ajax']);
    $report->drawBody($calendar->getSelectedDate(), $ajax);
    exit();
}
?>

<style type="text/css">
    .box-hidden {
        /*make invisible*/
        display:none;
        z-index:0;

    }
    .box-visible {
         /*make visible*/
        display:block;

        /*position it 200px down the screen*/
        position:absolute;
        top:50px;
        left:20%;
        width:60%;
        /*height:80%;*/
        min-height: 80%;
        text-align:center;

        /*in supporting browsers, make it
        a little transparent*/
        background:lightgoldenrodyellow;
        filter: alpha(opacity=90); /* internet explorer */
        -khtml-opacity: 0.90;      /* khtml, old safari */
        -moz-opacity: 0.90;        /* mozilla, netscape */
        opacity: 0.90;             /* fx, safari, opera */
        border:1px solid black;

        z-index:20;
    }

    .reportPreview {
        background-color: transparent;
        text-align:left;
    }

</style>

<script type="text/javascript">
    function showBox(){
        document.getElementById("InformationBox").className = "box-visible";
    }

    function hideBox(){
        document.getElementById("InformationBox").className = "box-hidden";
    }

    function loadDataToBox (rkey) {
        fillDataBox('loading, please don`t refresh page');
        showBox();
        var home = '<? echo $linkBase; ?>&rkey='+rkey+'&ajax=1';
        httpGet(home, fillDataBox);
    }

    function httpGet(strURL, readyFunc) {
        var self = this;
        self.xmlHttpReq = new XMLHttpRequest();
        self.xmlHttpReq.open('GET', strURL, true);
        self.xmlHttpReq.onreadystatechange = function() {
                if (self.xmlHttpReq.readyState == 4) {
                    readyFunc(self.xmlHttpReq.responseText);
                }
            };
        self.xmlHttpReq.send();
        //self.xmlHttpReq.send(getquerystring());
    }

    function fillDataBox(str){
        document.getElementById("InformationBoxContent").innerHTML = str;
    }

</script>

<div id="InformationBox" class='boxHidden'>
    <a href="#" onclick="javascript:hideBox();">close</a>
    <div id="InformationBoxContent">
        &nbsp;
    </div>
</div>

<div id="mainTable">

    <table>
    <?
        $reports = $scope->getAllReports();
        foreach($reports as $i => $report) {
            $rkey = $report->getReportName();

            echo "<tr><td>";
            echo '<a href="#" onclick="javascript:loadDataToBox(\''.$rkey.'\');">'.$report->getTitle().'</a>';
            echo "</td></tr>\n";

            echo "<tr>\n";
                    echo '</td><td><span class="reportPreview">'."\n";
                    $report->drawPreviewCell();
                    echo '</span>'."\n";
                    echo '</td>'."\n";

            echo '</tr>';
        }
    ?>
    </table>

</div>