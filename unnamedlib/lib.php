<?php
define('TPL_DIR','./tpl/',true);

class GlobalException extends Exception {
	var $error = '';
	function GlobalException($message) {
		$this->error = $message;
	}
}

function buildFormattedCycledArray($inArray, $template, $from_variable = false) {
	//$template = '';
	$data = '';
	if(!$from_variable) {
	    $template = TPL_DIR.$template;
	    //echo getcwd().$template;
	    if(!file_exists($template)) {
	        //dp('no such file');
	        return null;
	    }
	    $data = file_get_contents($template);
	} else {
		$data = $template;
	}
    //dp($data);
    $keywords = array();
    $columns = array();
    preg_match_all("/([\d\D\n\r]*)#begin#([\d\D\n\r]*)#end#([\d\D\n\r]*)/i", $data, $keywords);
    preg_match_all("/\{(\w+)\}/i", $keywords[2][0], $columns);
    $cell_row_templ = $keywords[2][0];
    $ret_data = '';
    $page_data = '';
    $ret_data .= $keywords[1][0];
    $arr_counter = 0;
	if($inArray!=array()){
	    foreach($inArray as $key => $item)  {
		    $page_data = $cell_row_templ;
	        $counter = 0;
	        foreach ($columns[0] as $col) {            
	            $page_data = str_replace($col, $inArray[$arr_counter][$columns[1][$counter]],$page_data);
	            //echo '$page_data = str_replace('.$col.', $inArray['.$columns[1][$counter].'],'.$page_data.');<BR>';
	            $counter ++;            
	        }                
	        $ret_data .= $page_data;
	        $arr_counter ++;
	    }                
	}
    $ret_data .= $keywords[3][0];
    return $ret_data;
}

function buildFluidFormattedCycledArray($inArray, $template, $from_variable = false, $inHeadRow = null) {
    $data = '';
	if(!$from_variable) {
	    $template = TPL_DIR.$template;
	    if(!file_exists($template) or sizeof($inArray)==0) {
	        return null;
	    }
	    $data = file_get_contents($template);
	} else {
		$data = $template;
	}
        $keywords = array();
        $columns = array();
        preg_match_all("/([\d\D\n\r]*)#begin#([\d\D\n\r]*)#end#([\d\D\n\r]*)/i", $data, $keywords);
        $row_data = array();
        $entry_data = array();
        $head_data = array();
        preg_match_all("/[\d\D\n\r]*#ROW_START#([\d\D\n\r]*)#ROW_END#[\d\D\n\r]*/i", $keywords[2][0], $row_data); //{row}
        preg_match_all("/[\d\D\n\r]*#ENTRY_START#([\d\D\n\r]*)#ENTRY_END#[\d\D\n\r]*/i", $keywords[2][0], $entry_data); //{entry}
        $head_row = '';
        if(is_array($inHeadRow)) {
            preg_match_all("/[\d\D\n\r]*#HEAD_START#([\d\D\n\r]*)#HEAD_END#[\d\D\n\r]*/i", $keywords[2][0], $head_data); //{head_entry}
            foreach($inHeadRow as $N => $head_item) {
                $head_row .= str_replace('{HEAD_ITEM}', $head_item, $head_data[1][0]);
            }
            //pr($head_data);
            unset($head_data);
        }
        //pr($keywords);
        $row_data = $row_data[1][0];
        $entry_data = $entry_data[1][0];
        $out_data = '';
        $out_data .= $keywords[1][0];
        $row_item = '';
        foreach($inArray as $N => $row) {
            foreach ($row as $column => $entry) {
                $row_item .= str_replace('{ENTRY}', $entry, $entry_data);
            }
            if(isset($head_row) && strlen($head_row)>1) {
                $out_data .= $head_row;
                unset($head_row);
            }
            $out_data .= str_replace('{ROW}', $row_item, $row_data);
            $row_item = '';
        }
       $out_data .= $keywords[3][0];
       return $out_data;
        //pr($row_data);
        //pr($entry_data);
        //preg_match_all("/\{(\w+)\}/i", $keywords[2][0], $columns);
        
}

function justTemplate ($template) {
	$template = TPL_DIR.$template;	
    if(!file_exists($template)) {
        dp('no such file');
        return null;
    }    
    return 	file_get_contents($template);
}

function buildFormattedFlatdArray($inArray, $template) {
	$template = TPL_DIR.$template;
	//dp($template);
    if(!file_exists($template)) {
        dp('no such file');
        return null;
    }
    $page_data = file_get_contents($template);
    preg_match_all("/\{(\w+)\}/i", $page_data, $fields);
    $counter = 0;
    foreach ($fields[0] as $key=>$item) {            
	    $page_data = str_replace($item, $inArray[$fields[1][$counter]],$page_data);
	    //echo "$page_data = str_replace($item, \$inArray[".$fields[1][$counter]."],$page_data);<BR>";
        $counter ++;            
    }  
    return $page_data;
}

function buildSingleRowArray($in_query, $assoc = true) {
	$dblink = null;
	if(is_array($in_query)) {
		$dblink = $in_query[0];
		$in_query = $in_query[1];
	}
	$res = null;
	if($dblink) {
	    $res = mysql_query($in_query,$dblink);
	} else {
	    $res = mysql_query($in_query);
	}                   
	if(!$res) return null;		
	if(mysql_num_rows($res) == 0) {
		return null;
	} else {
		if ($assoc) {
			return mysql_fetch_assoc($res);
		} else {
			return mysql_fetch_row($res);
		}
	}
}


function createFetchedArray($query, $assoc = false, &$column_names = null) {
	$dblink = null;
	if(is_array($query)) {
		$dblink = $query[0];
		$query = $query[1];	
	}
	$res = null;
	if($dblink) {
	    $res = mysql_query($query,$dblink);
	} else {
	    $res = mysql_query($query);
	}
	
	if(!$res) return null;		
	if(mysql_num_rows($res) == 0) {
		return null;
	} else {
		$ret_array = array();
		$count = 0;
		if($assoc) {
                        $flag = true;
                        //var_dump($flag && is_array($column_names));
			while($row = mysql_fetch_assoc($res)) {                      
				$ret_array[$count++] = $row;
                                if($flag && is_array($column_names)) {
                                    //echo 'here we set column names<BR>';
                                    $column_names = array_keys($row);
                                    $flag = false;
                                }
			}
                        unset($flag);
		} else {
			while($row = mysql_fetch_array($res)) {
				$ret_array[$count++] = $row;
			}
		}
		return $ret_array;
	}
}

function createFlatArray($query) {
	$dblink = null;
	if(is_array($query)) {
		$dblink = $query[0];
		$query = $query[1];	
	}
	$res = null;
	if($dblink) {
	    $res = mysql_query($query,$dblink);
	} else {
	    $res = mysql_query($query);
	}
	$out = array();
                  
	if(!$res) {
		//echo 'no results: '.mysql_error();
		return null;		
	}
	if(mysql_num_rows($res) == 0) {
		//echo 'empty results: '.mysql_error();
		return null;
	} else {
		$f = mysql_num_fields($res);
		if($f == 1) {
			$count = 0;		
			while($row = mysql_fetch_row($res)) {
				$out[$count] = $row[0];
				$count ++;
			}
			return $out;
		} else if ($f == 2) {
			while($row = mysql_fetch_array($res)) {
				$out[$row[0]] = $row[1];
			}
			return $out;
		} else {
			echo 'fields..';
			return null;
		}
		
	}
}

function getFirstOrNone($query) {
	$dblink = null;
	if(is_array($query)) {
		$dblink = $query[0];
		$query = $query[1];	
	}
	$res = null;
	if($dblink) {
	    $res = mysql_query($query,$dblink);
	} else {
	    $res = mysql_query($query);
	}
    if(!$res) 
        throw new GlobalException('Cannot first elem... '.mysql_error());
    if(mysql_num_rows($res) == 0)
        return null;
    else {
      $ar = mysql_fetch_row($res);
      return $ar[0];
    }
}

function pr($in_array) {
    echo '<BR><pre>';
    print_r($in_array);
    echo '</pre>';
}
function pt($in_string) {
	echo '<textarea rows=5 cols=20>';
	echo $in_string;
	echo '</textarea>';
}
function dp($str) {
    echo '<h2>'.$str.'</h2>';
}

function cut_center_of_string ($input, $cut_lenght = 35) {		
	$input = str_replace('www.','',strtolower($input));
	$len = strlen($input);
	if($len > $cut_lenght) {
		$four = intval($cut_lenght/10);
		if($four == 0) $four = 3;
		//return substr($input,0,$four).'...'.substr($input,$len-$four,$len);
		return substr($input,0,$four*6).'...'.substr($input,$len-($four*3),$len);
	}
	return $input;
}

function clear_string($string) {
    $string = str_replace(
        array(
            '\'', '"', ',' , ';', '<', '>',
            '/', '%', '?', '&', '$', '\'',
            '(', ')', '*', '^', '#', '@',
            '!', '~', '.', '-', '=', '+',
            ' ', "\n", "\t"
        )
    , '', $string);
    return $string;
}

function cut_first_char($str) {
    return substr($str, 1, strlen($str));
}

function get_first_char($str) {
    return substr($str, 0, 1);
}

function cut_some_first_chars($str,$chars) {
    return substr($str, $chars, strlen($str));
}

function cut_last_chars($str, $chars) {
    return substr($str, 0, strlen($str)-$chars);
}

function get_file_extension($file_name) {
	return strtolower(substr($file_name , strrpos($file_name , ".") + 1));
}

function get_file_name($full_file_path) {
	return substr($full_file_path , strrpos($full_file_path , '/')+1);
}

function get_file_directory($file_name){ 
	$filename = explode("/", $file_name);
    $filename2 = '';
	for( $i = 0; $i < (count($filename) - 1); ++$i ) { 
		$filename2 .= $filename[$i].'/'; 
	} 
	return $filename2; 
}

function list_directory($dir_name, $files_ext = ''){
    $rep=opendir($dir_name);
    $files_list = array();
    if(!$rep) {
        return $files_list;
    } else {
        $ext_len = strlen($files_ext);
        $ext = null;
        while ($file = readdir($rep)) {
            if($file == '..' || $file =='.' || $file =='') {
                continue;
            }
            $ext = substr($file, strlen($file)-$ext_len,$ext_len);
            if (is_file($dir_name.'/'.$file) && $ext==$files_ext /*&& substr($file,0,1) != '_'*/) {
                $file_tmp = substr($file, 0, strlen($file)-$ext_len);
                $files_list[$file_tmp] = $file;
            }
        }
    }
    closedir($rep);
    return $files_list;
}

function get_object_by_class_name($className) {
    $r = new ReflectionClass($className);
    $objInstance = $r->newInstance();
    return $objInstance;
}

function genImgTag ($img_path, $img_file) {
    return '<img="'.$img_path.'/'.$img_file.'" />';
}

function form_getInputText($inName, $inValue = null, $inLen = 10, $inClass = null) {
	$ret = '<input type="text" name="'.$inName.'" lenght="'.$inLen.'"';
	if($inValue != null)
		$ret .= ' value="'.$inValue.'"';
	if($inClass != null)
		$ret .= ' class="'.$inClass.'"';
	$ret .= ' />';
	return $ret;
}

function form_getSelectInput($inName, $inOptions, $inClass, $inSelectedKey = null, $inMulti=false) {
	$ret = '';
	if($inMulti) {
		$ret = '<select name="'.$inName.'" class="'.$inClass.'" multiple="multiple">';
	} else {
		$ret = '<select name="'.$inName.'" class="'.$inClass.'">';
	}
	foreach ($inOptions as $key=>$item) {
		if($inSelectedKey!=null && $key==$inSelectedKey) {
			$ret .= '<option value="'.$key.'" selected="selected">'.$item.'</option>';
		} else {
			$ret .= '<option value="'.$key.'">'.$item.'</option>';
		}
	}
	$ret .= '</select>';
	return $ret;
}

function form_getMultiSelectInput($inName, $inOptions, $inSelected, $inClass, $array_keys = false) {
	$ret = '<select name="'.$inName.'[]" class="'.$inClass.'" multiple="multiple">';
	//pr($inOptions);
	//pr($inSelected);
	if ($array_keys) {
		foreach ($inOptions as $key=>$item) {		
			if(array_key_exists($key,$inSelected)){
				$ret .= '<option value="'.$key.'"  selected="selected">'.$item.'</option>';
			} else {
				$ret .= '<option value="'.$key.'">'.$item.'</option>';
			}
		}
	} else {
		foreach ($inOptions as $key=>$item) {		
			if(in_array($key,$inSelected)){
				$ret .= '<option value="'.$key.'"  selected="selected">'.$item.'</option>';
			} else {
				$ret .= '<option value="'.$key.'">'.$item.'</option>';
			}
		}
	}
	$ret .= '</select>';
	return $ret;
}

function createQueryFromRightArray($inArray, $table) {
	$query = 'insert into '.$table.' ';
	$rows = '(';
	$values = 'values (';
	$count = 0;
	foreach($inArray as $arrKey => $arrItem) {
		$rows .= $arrKey;
		$values .= $arrItem;
		if ($count++ < sizeof($inArray)-1) {
			$rows  .= ",\n";
			$values .= ",\n";
		}
	}
	$query .= $rows.")\n ".$values.');';
	//dp($query);
	return $query;
}

/**
 * Builds update query from source data.
 *
 * @param Array array for sets ('column' => 'new_value')
 * @param String table name
 * @param Unknown name of column for Where clause or array for WHERE ('column' => 'value')
 * @param Array If previous param is string. Values for WHERE clause, for setted column.
 * @return String Update SQL query ready to run;
 */
function createUpdateQueryFromRightArray($inArray, $table, $keyArray = null, $justAndValues = null) {
    // $in_key is array [key=>value]
    $query = 'update '.$table.' set';
    $sets = ''; 
    $where = ' where 1';
    $count = 0;
    $arr_size = sizeof($inArray);
    foreach($inArray as $arrKey => $arrItem) {
	    if($arrItem != '\'!_NNULL\'') {
	        $sets .= ' `'.$arrKey.'` = '.$arrItem;                                
	        if ($count++ < $arr_size-1) {
	            $sets .= ',';
	        }
	    } else
	      $arr_size--;
    }
    if  ($keyArray != null && is_array($keyArray)) {
    	if (sizeof($keyArray)>0) {
	        $where .= ' and ';
	        foreach($keyArray as $arrKey => $arrItem) {
                $where .= ' `'.$arrKey.'` = '.$arrItem;
                if ($count++ < sizeof($keyArray)) {
                    $where .= ' and ';
                }
	        }
    	}
    } else if (is_string($keyArray) && (strlen($keyArray) > 0)) {
    	$where .= ' and (';
        foreach($justAndValues as $arrKey => $arrItem) {
            $where .= ' `'.$keyArray.'` = '.$arrItem;
            if ($count++ < sizeof($justAndValues)) {
                $where .= ' or ';
            }
        }
        $where .= ' ) ';
    } else {
    	return null;
    }
    $query .= $sets . $where;
    return $query;
}





function doMQuery ($in_query) {
	if(!mysql_query($in_query))
		throw new GlobalException("\nERR[".mysql_errno().'] :'.mysql_error()." \n on query: ".$in_query." \n");
	else return true;
}

function doMQueryClr ($in_query) {
	if(!mysql_query($in_query))
		return "\nERR[".mysql_errno().'] :'.mysql_error()." \n on query: ".$in_query." \n";
	else return null;
}

function createStringFromArray($inArray, $btw) {
	$out_str = "";
	$count = 0;
	foreach($inArray as $arrKey => $arrItem) {
		$out_str .= $arrItem;
		if ($count++ < sizeof($inArray)-1) {
			$out_str  .= $btw;
		}
	}
	return $out_str;
}



/**
 * Creats a mysql query "delete from <tablename> where..."
 * 
 * @param String $table
 * @param single-dimension array of $columns or column name string
 * @param single-dimension array of $values
 * @return a mysql query;
 */
function create_delete_query($table, $columns, $values) {
	$rq =  'delete from `'.$table.'` where';
	if(is_array($columns)) {
		if(sizeof($columns) != sizeof($values))
			return null;
		$columns = array_values($columns);
		$values  = array_values($values);	
		$dots_count = sizeof($columns);
		foreach ($columns as $key=>$col ) {
			$rq .= ' `'.$col.'` = "'.$values[$key].'" ';
			if ($dots_count-- != 1) {
				$rq .= 'or';
			}	
		}
	} else if (strlen($columns) > 0){		
		$values  = array_values($values);	
		$dots_count = sizeof($values);
		foreach ($values as $key=>$val ) {
			$rq .= ' `'.$columns.'` = "'.$val.'" ';
			if ($dots_count-- != 1) {
				$rq .= 'or';
			}	
		}
	} else 
		return null;
	return $rq;
}

function prepareArray($inArray) {
	$outArray = array();
	foreach($inArray as $key => $item) {
		if (substr($item, 0,4) == '!_NB')
			$outArray[$key] = substr($item,5,strlen($item));
		else {
			if(!get_magic_quotes_gpc()) {
				$outArray[$key] = '\''.mysql_escape_string($item).'\'';
			} else {
				$outArray[$key] = '\''.$item.'\'';
			}
		}
	}
	return $outArray;
}

function prepareArrayFromPOST($inArray) {
	$outArray = array();
	foreach($inArray as $key => $item) {
		if(substr($key,0,5) != 'SBMNT')
			$outArray[$key] = '\''.htmlspecialchars(mysql_escape_string($item)).'\'';
	}
	return $outArray;
}

function insertRightArray($table, $rightArray) {	
	//dp($query); 
	$dblink = null;
	if(is_array($table)) {
		$dblink = $table[0];
		$table = $table[1];	
	}
	$res = null;
	$query = createQueryFromRightArray($rightArray, $table);
	if($dblink) {
	    $res = mysql_query($query,$dblink);
	} else {
	    $res = mysql_query($query);
	}
	if(!$res) {
		return false;
    }
	return true;
}

function getCurrentURL($folder=false) {
    preg_match('#^\/([\/\w\d\._-]+)\??#i', $_SERVER['REQUEST_URI'], $matches);

    return ($_SERVER['HTTPS']=='on'?'https':'http').
            '://'.$_SERVER['HTTP_HOST'].'/'.$matches[1].
            ((sizeof($_GET)>0)?'?'.http_build_query($_GET):'');
}

function getHOSTURL($inString) {
	// Извлекаем имя хоста из URL
	//$test_url = "http://www.php.net/index.html";
	//$pattern    = '/^(?:[;\/?:@&=+$,]|(?:[^\W_]|[-_.!~*\()\[\] ])|(?:%[\da-fA-F]{2}))*$/';
	$pattern    = '/^(https?:\/\/)?([^\/]+)/i';
	preg_match($pattern,$inString, $matches);
	return $matches[2];
}

function getROOTURL($inString) {
	// Извлекаем имя хоста из URL
	$test_url = "http://www.php.net/index.html";
	//$pattern    = '/^(?:[;\/?:@&=+$,]|(?:[^\W_]|[-_.!~*\()\[\] ])|(?:%[\da-fA-F]{2}))*$/';
	$pattern    = '~https?://[\d\w-_/.]+/?~i';
	preg_match($pattern,$inString, $matches);
	return $matches[0];
}

function getJUSTURL($inString) {
	// Извлекаем имя хоста из URL
	$test_url = "http://www.php.net/index.html";
	//$pattern    = '/^(?:[;\/?:@&=+$,]|(?:[^\W_]|[-_.!~*\()\[\] ])|(?:%[\da-fA-F]{2}))*$/';
	$pattern    = '/^(https?:\/\/)?([^\/]+)(.*)$/i';
	preg_match($pattern,$inString, $matches);
	//pr($matches);
	return $matches;
	/*
	$host = $matches[2];

	// извлекаем две последние части имени хоста
	preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
	pr($matches);
	echo "domain name is: {$matches[0]}\n";		
	return $matches[0];
	*/
}

function isValidUrl($str) {
	$urlregex = "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
	if (eregi($urlregex, $str)) {return true;} else {return false;}
	//return preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $str);
}

function isValidEmail($email) {
  // First, we check that there's one @ symbol, 
  // and that the lengths are right.
  if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
    // Email invalid because wrong number of characters 
    // in one section or wrong number of @ symbols.
    return false;
  }
  // Split it into sections to make life easier
  $email_array = explode("@", $email);
  $local_array = explode(".", $email_array[0]);
  for ($i = 0; $i < sizeof($local_array); $i++) {
    if(!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",$local_array[$i])) {
      return false;
    }
  }
  // Check if domain is IP. If not, 
  // it should be valid domain name
  if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) {
    $domain_array = explode(".", $email_array[1]);
    if (sizeof($domain_array) < 2) {
        return false; // Not enough parts to domain
    }
    for ($i = 0; $i < sizeof($domain_array); $i++) {
      if(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$",$domain_array[$i])) {
        return false;
      }
    }
  }
  return true;
}

// ------ SPECIAL

function build_array_for_table($table_name) {
	$R = mysql_query('desc '.mysql_escape_string($table_name));
	if($R) {
		echo '<pre>';
		while ($one = mysql_fetch_array($R)) {
			echo '$TA[\''.$one[0].'\'] = null;'."\n";
		}
		echo '</pre>';
	}
}

function list_php_server_variable() {
    echo "<table border=\"1\">";
    echo "<tr><td>" .$_SERVER['argv'] ."</td><td>argv</td></tr>";
    echo "<tr><td>" .$_SERVER['argc'] ."</td><td>argc</td></tr>";
    echo "<tr><td>" .$_SERVER['GATEWAY_INTERFACE'] ."</td><td>GATEWAY_INTERFACE</td></tr>";
    echo "<tr><td>" .$_SERVER['SERVER_ADDR'] ."</td><td>SERVER_ADDR</td></tr>";
    echo "<tr><td>" .$_SERVER['SERVER_NAME'] ."</td><td>SERVER_NAME</td></tr>";
    echo "<tr><td>" .$_SERVER['SERVER_SOFTWARE'] ."</td><td>SERVER_SOFTWARE</td></tr>";
    echo "<tr><td>" .$_SERVER['SERVER_PROTOCOL'] ."</td><td>SERVER_PROTOCOL</td></tr>";
    echo "<tr><td>" .$_SERVER['REQUEST_METHOD'] ."</td><td>REQUEST_METHOD</td></tr>";
    echo "<tr><td>" .$_SERVER['REQUEST_TIME'] ."</td><td>REQUEST_TIME</td></tr>";
    echo "<tr><td>" .$_SERVER['QUERY_STRING'] ."</td><td>QUERY_STRING</td></tr>";
    echo "<tr><td>" .$_SERVER['DOCUMENT_ROOT'] ."</td><td>DOCUMENT_ROOT</td></tr>";
    echo "<tr><td>" .$_SERVER['HTTP_ACCEPT'] ."</td><td>HTTP_ACCEPT</td></tr>";
    echo "<tr><td>" .$_SERVER['HTTP_ACCEPT_CHARSET'] ."</td><td>HTTP_ACCEPT_CHARSET</td></tr>";
    echo "<tr><td>" .$_SERVER['HTTP_ACCEPT_ENCODING'] ."</td><td>HTTP_ACCEPT_ENCODING</td></tr>";
    echo "<tr><td>" .$_SERVER['HTTP_ACCEPT_LANGUAGE'] ."</td><td>HTTP_ACCEPT_LANGUAGE</td></tr>";
    echo "<tr><td>" .$_SERVER['HTTP_CONNECTION'] ."</td><td>HTTP_CONNECTION</td></tr>";
    echo "<tr><td>" .$_SERVER['HTTP_HOST'] ."</td><td>HTTP_HOST</td></tr>";
    echo "<tr><td>" .$_SERVER['HTTP_REFERER'] ."</td><td>HTTP_REFERER</td></tr>";
    echo "<tr><td>" .$_SERVER['HTTP_USER_AGENT'] ."</td><td>HTTP_USER_AGENT</td></tr>";
    echo "<tr><td>" .$_SERVER['HTTPS'] ."</td><td>HTTPS</td></tr>";
    echo "<tr><td>" .$_SERVER['REMOTE_ADDR'] ."</td><td>REMOTE_ADDR</td></tr>";
    echo "<tr><td>" .$_SERVER['REMOTE_HOST'] ."</td><td>REMOTE_HOST</td></tr>";
    echo "<tr><td>" .$_SERVER['REMOTE_PORT'] ."</td><td>REMOTE_PORT</td></tr>";
    echo "<tr><td>" .$_SERVER['SCRIPT_FILENAME'] ."</td><td>SCRIPT_FILENAME</td></tr>";
    echo "<tr><td>" .$_SERVER['SERVER_ADMIN'] ."</td><td>SERVER_ADMIN</td></tr>";
    echo "<tr><td>" .$_SERVER['SERVER_PORT'] ."</td><td>SERVER_PORT</td></tr>";
    echo "<tr><td>" .$_SERVER['SERVER_SIGNATURE'] ."</td><td>SERVER_SIGNATURE</td></tr>";
    echo "<tr><td>" .$_SERVER['PATH_TRANSLATED'] ."</td><td>PATH_TRANSLATED</td></tr>";
    echo "<tr><td>" .$_SERVER['SCRIPT_NAME'] ."</td><td>SCRIPT_NAME</td></tr>";
    echo "<tr><td>" .$_SERVER['REQUEST_URI'] ."</td><td>REQUEST_URI</td></tr>";
    echo "<tr><td>" .$_SERVER['PHP_AUTH_DIGEST'] ."</td><td>PHP_AUTH_DIGEST</td></tr>";
    echo "<tr><td>" .$_SERVER['PHP_AUTH_USER'] ."</td><td>PHP_AUTH_USER</td></tr>";
    echo "<tr><td>" .$_SERVER['PHP_AUTH_PW'] ."</td><td>PHP_AUTH_PW</td></tr>";
    echo "<tr><td>" .$_SERVER['AUTH_TYPE'] ."</td><td>AUTH_TYPE</td></tr>";
    echo "</table>";
}
?>