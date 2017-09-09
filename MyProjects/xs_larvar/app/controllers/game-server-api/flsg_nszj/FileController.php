<?php
class FileController extends \BaseController {
    //页游game_message表添加
    public function gameMessageload(){
        $game_id = Session::get('game_id');
        $game = Game::find($game_id);
    	$data = array(
	            'content' => View::make('serverapi.gamemessage', array(
                    'game_code' => $game->game_code,
                    ))
	    );
	    return View::make('main', $data);    
    }

    public function gameMessage(){
    	$msg = array(
    	    'code' => Config::get('errorcode.unknow'),
    	    'error' => Lang::get('error.basic_input_error')
    	);
    	$game_id = Session::get('game_id');
    	$game = Game::find($game_id);
    	if(in_array($game->game_code, array('flsg', 'nszj', 'dld', 'poker'))){
    		$file_name = 'game_message';
    	}elseif(in_array($game->game_code, array('mnsg', 'yysg'))){
            $file_name = 'game_message';
        }else{
    		return Response::json(array('error'=>'该游戏暂时不支持自己添加，可以找技术进行添加'),403);
    	}
    	$table = Table::initArray(public_path() . '/table/' . $game->game_code .'/'.$file_name. '.txt');
    	$table = $table->getData();
    	foreach ($table as $k => $v) {
    		$mids[$v['id']] = $k;
    	}
    	
    	
    	$type = Input::get('type');
    	if(1 == $type){
    		$mid = Input::get('mid');
    		$desc = Input::get('desc');
    		$name = Input::get('name');
    		$is_filter = Input::get('is_filter');
            if('' == $is_filter){
                $is_filter = '2';
            }else{
                $is_filter = (1 == $is_filter) ? '1' : '0';
            }
    		if(!$mid || !is_numeric($mid)){
                $table = Table::initArray(public_path() . '/table/' . $game->game_code .'/'.$file_name. '.txt');
                $table = $table->getData();
    			return Response::json(array('error'=>'Did you enter right mid?','table' => $table),403);
    		}
    		if(in_array($game->game_code, array('flsg', 'nszj', 'dld', 'poker'))){
    			$table[] = array(
    				'id' => $mid,
    				'desc' => isset($desc) ? $desc : (isset($mids[$mid]) ? $table[$mids[$mid]]['desc'] : ''),
    				'name' => isset($name) ? $name : (isset($mids[$mid]) ? $table[$mids[$mid]]['name'] : ''),
    				'is_filter' => ('2' != $is_filter) ? $is_filter : (isset($mids[$mid]) ? $table[$mids[$mid]]['is_filter'] : '0'),
    			);
    		}elseif(in_array($game->game_code, array('mnsg', 'yysg'))){
    			$table[] = array(
    				'id' => $mid,
    				'desc' => isset($desc) ? $desc : (isset($mids[$mid]) ? $table[$mids[$mid]]['desc'] : ''),
    			);
    		} 
            if(isset($mids[$mid])){
                unset($table[$mids[$mid]]);
            }
    		
    	}elseif(2 == $type){
    		$text_datas = Input::get('text_data');
    		$text_datas = explode("\n", $text_datas);
    		if(!$text_datas){
    			return Response::json($msg, 403);
    		}
    		foreach ($text_datas as &$v) {
    		    $v = trim($v);
    		}
    		unset($v);
    		$text_datas = array_unique($text_datas);
    		foreach ($text_datas as $text_data) {
    			$text_data = explode("\t", $text_data);
    			if(in_array($game->game_code, array('flsg', 'nszj', 'dld', 'poker'))){
    				if(count($text_data) != 3 || !is_numeric($text_data[0])){
    					return Response::json($msg, 403);
    				}
    				if(isset($mids[$text_data[0]])){
    					$is_filter = $table[$mids[$text_data[0]]]['is_filter'];
    					unset($table[$mids[$text_data[0]]]);
    				}else{
    					$is_filter = '0';
    				}

    				$temp_table = array(
    					'id' => $text_data[0],
    					'desc' => $text_data[1],
    					'name' => $text_data[2],
    					'is_filter' => $is_filter,
    				);
    				$table[] = $temp_table;
    				unset($temp_table);
    			}elseif(in_array($game->game_code, array('mnsg', 'yysg'))){
    				if(count($text_data) != 2 || !is_numeric($text_data[0])){
    					return Response::json($msg, 403);
    				}
    				if(isset($mids[$text_data[0]])){
    					unset($table[$mids[$text_data[0]]]);
    				}

    				$temp_table = array(
    					'id' => $text_data[0],
    					'desc' => $text_data[1],
    				);
    				$table[] = $temp_table;
    				unset($temp_table);
    			}
    		}
    	}else{
	    	return Response::json($msg, 403);
	    }
        usort($table, function($a,$b){
            $a_id = $a['id'];
            $b_id = $b['id'];
            if($a_id == $b_id) return 0;
            return ($a_id > $b_id) ? 1 : -1;
        });
        unlink(public_path() . '/table/' . $game->game_code .'/'.$file_name. '.txt');
        if(in_array($game->game_code, array('flsg', 'nszj', 'dld', 'poker'))){
        	$titletowrite = "mid\t描述\t名字\t是否筛选\nid\tdesc\tname\tis_filter\n";
        }elseif(in_array($game->game_code, array('mnsg', 'yysg'))){
        	$titletowrite = "mid\t描述\nid\tdesc\n";
        }
        file_put_contents(public_path() . '/table/' . $game->game_code .'/'.$file_name. '.txt', $titletowrite, FILE_APPEND);

        $row = 0;
        foreach ($table as $value) {
        	if('' != $value['id']){
        		if(in_array($game->game_code, array('flsg', 'nszj', 'dld', 'poker'))){
        			$towrite = $value['id']."\t". $value['desc']."\t". $value['name']."\t".$value['is_filter']."\n";
        		}elseif(in_array($game->game_code, array('mnsg', 'yysg'))){
        			$towrite = $value['id']."\t". $value['desc']."\n";
        		}
        		
        		$charnum = file_put_contents(public_path() . '/table/' . $game->game_code .'/'.$file_name. '.txt', $towrite, FILE_APPEND);
        		if($charnum>0){
        			$row++;
        		}
        	}
        }

        $table = Table::initArray(public_path() . '/table/' . $game->game_code .'/'.$file_name. '.txt');
        $table = $table->getData();

    	if($row > 0){
    		return Response::json(array('result' => 'OK','table'=>$table));
    	}else{
    		return Response::json(array('error'=>'error' ,'table'=>$table), 403);
    	}
    }


}