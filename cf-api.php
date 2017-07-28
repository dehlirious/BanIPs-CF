<?php
/**
 * @author https://github.com/andrieslouw
 * @copyright 2016
 * https://gist.github.com/andrieslouw/3c833332cbf66f95ca6751f82013acf5
 **/
function cfban($ipaddr,$reason){
	$cfheaders = array(
		'Content-Type: application/json',
		'X-Auth-Email: your@email.com',
		'X-Auth-Key: yourauthkeyhere'
	);
	$data = array(
		'mode' => 'block',
		'configuration' => array('target' => 'ip', 'value' => $ipaddr),
		'notes' => 'Banned on '.date('Y-m-d H:i:s').' by '.$reason
	);
	$json = json_encode($data);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $cfheaders);
	curl_setopt($ch, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/user/firewall/access_rules/rules');
	$return = curl_exec($ch);
	curl_close($ch);
	if ($return === false){
		return false;
	}else{
		$return = json_decode($return,true);
		if(isset($return['success']) && $return['success'] == true){
			return $return['result']['id'];
		}else{
			return false;
		}
	}
}
function cfunban($block_rule_id){
	$cfheaders = array(
		'Content-Type: application/json',
		'X-Auth-Email: your@email.com',
		'X-Auth-Key: yourauthkeyhere'
	);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $cfheaders);
	curl_setopt($ch, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/user/firewall/access_rules/rules/'.$block_rule_id);
	$return = curl_exec($ch);
	curl_close($ch);
	if ($return === false){
		return false;
	}else{
		$return = json_decode($return,true);
		if(isset($return['success']) && $return['success'] == true){
			return $return['result']['id'];
		}else{
			return false;
		}
	}
}
?>
