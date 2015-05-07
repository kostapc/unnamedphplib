<?

class p1tpl extends TemplateClassImpl {

    private function getData($presentDay) {
        $data = $this->getCached();
        if($data!=null) {
            return $data;
        }
        $data = array();
	// ..............
        $this->putToCache($data);
        return $data;
    }

    function getTitle() {
        return 'Название блока';
    }

    function drawPreviewCell() {
        ?>
        	<b>предпросмотр блока</b>
        <?
    }

    function drawBody($inData, $ajax=1) {
        header('Content-Type: text/html; charset=utf-8');
        $data = $this->getData($inData);
        ?>
                        
            <table class="reportTable">
                <tr>
                    <td>Тело блока (показывается в окне)</td>
                    <td><? echo $data['k1']; ?></td>
                </tr>
            </table>
        <?
    }

}

?>