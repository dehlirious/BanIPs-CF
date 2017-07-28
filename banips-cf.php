<?php
require_once(cf-api.php");
$error = 0;
$ips = "";

function loadlogs($echos){
	global $ips,$error;
	$exploded = explode("\r\n", $_POST['logs']);
	if($echos){
		echo "\n<br/>List of IPs: \n<br/>";
	}
	
	foreach($exploded as &$explode)
	{
		$explodes = explode(' - ', $explode);
		if(filter_var(trim($explodes[0]), FILTER_VALIDATE_IP) !== false){
			if($echos){
				echo "$explodes[0], ";
			}
			$ips .= $explodes[0].", ";
		}
		else{
			$error+1;
			if($echos){
				if(!$explodes[0] == "" | !$explodes[0] == " "){
					echo "\n<br/>Error! - ";
					echo "\"".htmlentities($explodes[0])."\"";
				}
			}
		}
		//If there isn't an error, it is added.
		//If there is an error, it is'nt added.
	}
	
	if($echos){
		echo "\n<br/>There are $error errors";
	}
	
	$ips = substr($ips, 0, -2);
}

if(isset($_GET['ajax']) && $_GET['ajax'] == "true"){
	//loadlogs(false);
	
	if(isset($_POST['confirmation']) && $_POST['confirmation'] == "true"){	
		echo "\n<br/><br/>The Result IDs are: ";
		$ips = $_POST['ips'];
		$exploded2 = explode(', ', $ips);
		foreach($exploded2 as $explosion){
			$cfban = cfban($explosion,"BanIPs-CF (Manual) for Very suspicious behavior");
			echo $_require->saveToFile(S_DIR."cfresultids.txt","$explosion: ".$cfban."\r\n","a");
			echo $cfban;
			echo ", ";
			sleep(0.1);
		}
	}
	else{
		echo "Cannot continue.";
	}
	exit(); die();
}
?>
<html>
<head>
<script>
var ajax = {};
ajax.x = function () {
    if (typeof XMLHttpRequest !== 'undefined') {
        return new XMLHttpRequest();
    }
    var versions = [
        "MSXML2.XmlHttp.6.0",
        "MSXML2.XmlHttp.5.0",
        "MSXML2.XmlHttp.4.0",
        "MSXML2.XmlHttp.3.0",
        "MSXML2.XmlHttp.2.0",
        "Microsoft.XmlHttp"
    ];

    var xhr;
    for (var i = 0; i < versions.length; i++) {
        try {
            xhr = new ActiveXObject(versions[i]);
            break;
        } catch (e) {
        }
    }
    return xhr;
};

ajax.send = function (url, callback, method, data, async) {
    if (async === undefined) {
        async = true;
    }
    var x = ajax.x();
    x.open(method, url, async);
    x.onreadystatechange = function () {
        if (x.readyState == 4) {
			var serverResponse = x.responseText;
            callback(x.responseText) 
        }
		/*x.onload = function (e) {
    results.innerHTML = e.target.response.message;
};*/
    };
    if (method == 'POST') {
        x.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    }
    x.send(data)
};

ajax.get = function (url, data, callback, async) {
    var query = [];
    for (var key in data) {
        query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
    }
    ajax.send(url + (query.length ? '?' + query.join('&') : ''), callback, 'GET', null, async)
};

ajax.post = function (url, data, callback, async) {
    var query = [];
    for (var key in data) {
        query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
    }
    ajax.send(url, callback, 'POST', query.join('&'), async)
};
</script>
<style>
.loader {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid blue;
  border-bottom: 16px solid blue;
  width: 120px;
  height: 120px;
  -webkit-animation: spin 2s linear infinite;
  animation: spin 2s linear infinite;
  display: none;
}

@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
/* */
#container {
    width:100%;
    text-align:center;
}

#left {
    float:left;
    width:30%;
    height:50%;
}

#center {
    display: inline-block;
    margin:0 auto;
    width:%40;
    height: 50%;
}

#right {
    float:right;
    width:30%;
    height: 50%;
}

</style>
</head>
<body>
	<div id="left">
		<form action="https://null.org.uk/banips-cf.php" method="POST">
		Unban IP: <input type="text" name="uip" />
		<button type="submit">GO!</button>
		</form>
		<br/>
	</div>
	
	<div id="right">
		<form action="https://null.org.uk/banips-cf.php" method="POST">
		IP: <input type="text" name="ip" /> <br/>
		Reason: <input type="text" name="reason" />
		<button type="submit">GO!</button>
		</form>
		<br/>
	</div>
	
	<div id="center">
	<form action="https://null.org.uk/banips-cf.php" method="POST" id="usrform">
	NGINX logs: <textarea name="logs" form="usrform">Enter text here...</textarea> 
	<button type="submit">GO!</button>
	</form>
	<br/>
	<center>
	<?php
	if(isset($_POST['ip']) && $_POST['ip'] != "" && isset($_POST['reason']) && $_POST['reason'] != ""){
		//echo cfban(htmlentities($_POST['ip']));
		$cfban = cfban($explosion,"Ban-CF-Script (Manually) for ".htmlentities($_POST['reason']));
		echo $_require->saveToFile(S_DIR."cfresultids.txt","$explosion: ".$cfban."\r\n","a");	
		echo $cfban;
	}
	else{
	//	echo "\n<br/>No IP set";
	}
	
	if(isset($_POST['uip']) && $_POST['uip'] != ""){
		echo "aye captin. <br/>\n";
		$result=preg_split('/'.preg_quote($_POST['uip'].": ").'/',$_require->getFile(S_DIR."cfresultids.txt"));
		//$result = explode("\r\n", $result);
		if(count($result)>1){
			$result_split=explode(' ',$result[1]);
			$result_split2=explode("\r\n",$result_split[1]);
			echo(cfunban($result_split2[0]));
		}
	}
	
	if(isset($_POST['logs']) && $_POST['logs'] != ""){
		echo "<br/><br/>";
		loadlogs(true);
		
		echo "\n<br/>Do you wish to continue?<br/>";
		?>
		<button id="retrieve" data-url="/">Confirm!</button>
		<br/><br/>
		<div class="loader" id="loader"></div>
		<p id="results"></p>
		<script nonce="bpquxr9yrckd9sg">
		function pushresult(e){
			results.innerHTML = e;
			document.getElementById('loader').setAttribute("style", "display:none;");
		}
		function yourFunction(){
			retrieve.addEventListener('click', function (e) {
				document.getElementById('loader').setAttribute("style", "display:block;");
				ajax.post('/banips-cf.php?ajax=true', {confirmation: 'true'<?php
				/*if(isset($_POST)){
					foreach($_POST as $k => $v){
						echo ",$k: \"".htmlentities($v)."\"";
					}
				}*/ echo ", \nips: '".$ips."'";
				?>}, pushresult);
			});
		}

		yourFunction();
		</script>
		<?php
	}?>
	</center>
	</div>
</body>
</html>
