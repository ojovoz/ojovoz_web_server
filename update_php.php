<?php
function strpos_recursive($haystack, $needle, $offset = 0, &$results = array()){               
    $offset = strpos($haystack, $needle, $offset);
    if($offset === false) {
        return $results;           
    } else {
        $results[] = $offset;
        return strpos_recursive($haystack, $needle, ($offset + 1), $results);
    }
}

function switch_params($params){
	$params=substr($params,1,strlen($params)-2);
	$parts=explode(',',$params);
	return "(".trim($parts[1]).",".trim($parts[0]).")";
}

$functions=array("mysql_fetch_array","mysql_num_rows","mysql_fetch_row","mysql_free_result");
$new_functions=array("mysqli_fetch_array","mysqli_num_rows","mysqli_fetch_row","mysqli_free_result");
$dirs=glob('./*',GLOB_ONLYDIR);
array_push($dirs,'.');

foreach($dirs as $dir){
	$files=glob($dir."/*.php");
	foreach($files as $file){
		if(strpos($file,"update_php")===false){
			$n=0;
		
			$code=file_get_contents($file);
		
			for($i=0;$i<sizeof($functions);$i++){
				$code=str_replace($functions[$i],$new_functions[$i],$code);
			}

			while(strpos($code,"mysql_query")!==false){
				$start=strpos($code,"mysql_query");
				$end=strpos($code,')',$start);
				$length=$end-$start+1;
				$old=substr($code,$start,$length);
				$new="mysqli_query".switch_params(substr($old,strpos($old,'(')));
				$code=substr($code,0,$start).$new.substr($code,$end+1);
				$n++;
			}
		
			while(strpos($code,"mysql_insert_id")!==false){
				$start=strpos($code,"mysqli_insert_id");
				$end=strpos($code,')',$start);
				$new="mysqli_insert_id($dbh)";
				$code=substr($code,0,$start).$new.substr($code,$end+1);
				$n++;
			}
		
			while(strpos($code,"mysql_affected_rows")!==false){
				$start=strpos($code,"mysqli_affected_rows");
				$end=strpos($code,')',$start);
				$new="mysqli_affected_rows($dbh)";
				$code=substr($code,0,$start).$new.substr($code,$end+1);
				$n++;
			}
		
			if($n>0){
				$path_parts=pathinfo($file);
				$old_file=$path_parts['dirname'].'/'.$path_parts['filename']."_o.php";
				rename($file,$old_file);
				file_put_contents($file,$code);
				echo($file."<br>");
			}
		}
		
	}
}
?>