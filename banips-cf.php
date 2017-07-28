<?php
require_once(__dir__ . "/_required/cf-api.php");

class banip{
	public $err,$ip,$ips,$save,$file;
	//public function cfban($ipaddr,$reason);
	
	public function __construct(){
		//
		$this->save = false;
		$this->file = __dir__ . "/cfresultids.txt";
	}
	
	public function saveToFile($file,$content,$mode){
		if (!isset($file)) {
			return "Filename not defined.";
			exit;
		}
		if(!file_exists($file) && $mode == "a"){
			$mode = "w";
		}
		if (!is_writable($file)) {
			return "Could not write to {$file}.";
			exit;
		}
		$fp = fopen($file, $mode);
		if (!is_resource($fp)) {
			return "Could not open {$file} for writting.";
			exit;
		}
		fwrite($fp, $content);
		fclose($fp);
	}
	
	public function processlogs($inputlog,$echo=false){ //$echo is unnecessary now. I'll remove it in the future
		$exploded = explode("\r\n", $inputlog); // May need to be changed to \n depending on the system.
		if($echo){
			echo "List of IPs: \n<br/>";
		}
		
		foreach($exploded as &$explode){
			$explodes = explode(' - ', $explode);
			if(filter_var(trim($explodes[0]), FILTER_VALIDATE_IP) !== false){
				if($echo){
					echo "$explodes[0], ";
				}
				
				$ips .= $explodes[0].", ";
			}
			else{
				$error+1;
				if($echo){
					if(trim($explodes[0]) == "" || trim($explodes[0]) == " "){ //There's probably a better way to detect if its an empty line
					}
					else{
						echo "\n<br/>Error! (\"".htmlentities($explodes[0])."\")";
					}
				}
			}
			
			if($echo && $error != 0){
				echo "\n<br/>There are $error errors";
			}
			
			$ips = substr($ips, 0, -2); //Remove the last ", " from the string
		}
	}
	
	public function ajaxprocessing(){
		if(isset($_GET['ajax']) && $_GET['ajax'] == "true"){
			if(isset($_POST['confirmation']) && $_POST['confirmation'] == "true"){
				echo "<br/>The result IDs are:";
				$ips = $_POST['ips'];
				$exploded = explode(', ', $ips);
				
				foreach($exploded as $explosion){
					$cfban = cfban($explosion, "BanIPs-CF (Manual) for very suspicious behavior");
					if($this->save){
						echo $this->saveToFile($this->file,"$explosion: ".$cfban."\r\n","a");
					}
					if($cfban == false){
						echo "Returned False (Failed to ban)";
					}
					else{
						echo $cfban;
					}
					echo ", ";
					sleep(0.1);
				}
				exit;
				die; // Have to do this so it only returns this data, and nothing else.
			}
		}
	}
	
	public function processban(){
		$ip = htmlentities($_POST['ip']);
		$cfban = cfban($ip,"Ban-CF-Script (Manually) for ".htmlentities($_POST['reason']));
		if($this->save){
			echo $this->saveToFile($this->file,"$ip: ".$cfban."\r\n","a");	
		}
		echo "Result: ".$cfban;
		if($cfban == false){
			echo "Returned False (Failed to ban)";
		}
	}
	
	public function processunban(){
		if($this->save == false){
			echo "You disabled saving to file; Cannot continue!";
			exit;
		}
		
		$result=preg_split('/'.preg_quote(htmlentities($_POST['uip']).": ").'/',$this->file);
		if(count($result)>1){
			$result_split=explode(' ',$result[1]);
			$result_split2=explode("\r\n",$result_split[1]);
			echo "<br/>ID: ".$result_split2[0];
			$cfunban = cfunban($result_split2[0]);
			echo "<br/> Result: ".$cfunban;
			if($cfunban == false){
				echo " Returned False (Failed to Unban, have you already unbanned this ID?)";
			}
		}
	}
}

$banip = new banip();
$banip->ajaxprocessing();
?>
<html>
<head>
<title>The BanHammer</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://bootswatch.com/darkly/bootstrap.min.css">
<style>
footer {
    padding: 1em;
    color: white;
    /*background-color: #222222;*/
	text-align: center;
	width: 100%;
	bottom: 0;
    position: fixed;
}
footer a{
	color: white;
}
.footerdiv{
	background: rgb(70, 69, 69, 0.5); /*#464545*/
	border: 1px solid #222222;
	text-shadow: 2px 2px #222222;
}
/**/
.loader {
  border: 16px solid #464545;
  border-top: 16px solid #375a7f;
  border-radius: 50%;
  width: 120px;
  height: 120px;
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
<body> <br/><br/><br/>
	<div id="left">
		<form action="https://null.org.uk/banips-cf.php" method="POST">
			<div class="form-group">
				<label class="control-label" for="focusedInput">Unban IP:</label>
				<input class="form-control" id="focusedInput" name="uip" value="Enter IP here" type="text" style="width:70%;">
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit">Go!</button>
				</span>
			</div>
		</form>
		<br/>
	</div>
	
	<div id="right">
		<form action="https://null.org.uk/banips-cf.php" method="POST">
			<div class="form-group">
				<label class="control-label" for="inputSmall">Ban IP:</label>
				<input class="form-control input-sm" id="inputSmall" name="ip" value="Enter IP here" type="text" style="width:70%;"> <br/>
				<input class="form-control input-sm" id="inputSmall" name="reason" value="Enter Reason here" type="text" style="width:70%;">
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit">Go!</button>
				</span>
			</div>
		</form>
		<br/>
	</div>
	
	<div id="center">
	<form action="https://null.org.uk/banips-cf.php" method="POST" id="usrform">
		<div class="form-group">
			<div class="input-group">
				<label class="control-label">Input NGINX Logs</label>
				<textarea class="form-control" name="logs" form="usrform">Enter text here...</textarea>
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit">Go!</button>
				</span>
			</div>
		</div>
	</form>
	
	<br/>
	<center>
	<?php
	if(isset($_POST['ip']) && $_POST['ip'] != "" && isset($_POST['reason']) && $_POST['reason'] != ""){
		$banip->processban();
	}
	
	if(isset($_POST['uip']) && $_POST['uip'] != ""){
		$banip->processunban();
	}
	
	if(isset($_POST['logs']) && $_POST['logs'] != ""){
		$banip->processlogs($_POST['logs'], true);
		
		echo "\n<br/>Do you wish to continue?<br/>";
		?>
		<button id="retrieve" class="btn btn-default" data-url="/">Confirm!</button>
		<br/><br/>
		<div class="loader" id="loader"></div>
		<p id="results"></p>
		<script nonce="bpquxr9yrckd9sg">
		function pushresult(e){
			results.innerHTML = e;
			//document.getElementById('loader').setAttribute("style", "display:none;");
		}
		
		$(document).ready(function(){
			$(document).ajaxStart(function(){
				$("#loader").css("display", "block");
			});
			$(document).ajaxComplete(function(){
				$("#loader").css("display", "none");
			});
			$("#retrieve").click(function(){
				//document.getElementById('loader').setAttribute("style", "display:block;");
				$.ajax({
					type: "POST",
					url: "/banips-cf.php?ajax=true",
					data:{confirmation: 'true'<?php echo ", \nips: '".$ips."'";?>},
					success: pushresult
				});
			});
		});
		</script>
		<?php
	}?>
	</center>
	<br/><br/><br/>
	</div>
	
	<br/><p style="visibility: hidden;">Hidden-Content</p><br/> <!-- The footer could cover up content if I don't do this -->
	<footer><div class="footerdiv">Created by <a href="https://github.com/andrewsalmon">Andrew Salmon</a><br/></div></footer>
</body>
</html>
