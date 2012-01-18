<?php
/***************************************************************************

OpenNab - An open PHP-based proxy framework for the Nabaztag™ (http://www.nabaztag.com/) electronic pet.
Copyright c 2007 OpenNab team - http://opennab.sourceforge.net/team/

This file is part of OpenNab

OpenNab is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

OpenNab is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public
License along with this script; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

****************************************************************************/

require_once('../includes/http.inc');
require_once('../includes/misc.php');

// wrapper for api calls
class LocalApiCall
{
  var $httpClient, $url;
  
  function LocalApiCall() {
    global $_SERVER;
		$this->httpClient = new http( HTTP_V10, false);
		$this->httpClient->host = $_SERVER['SERVER_NAME'];
		$this->httpClient->port = $_SERVER['SERVER_PORT'];
    $this->httpClient->set_request_header('User-Agent', 'MTL');
  }
  
  function FullParametersArray(&$parameters) {
    global $_REQUEST;
    if( @$_REQUEST['sn'] )
      return array_merge( array('sn'=>$_REQUEST['sn']), $parameters );
    else if( @$_REQUEST['dst'] )
      return array_merge( array('dst'=>$_REQUEST['dst']), $parameters );
    else
      return $parameters;
  }
  
  function ComputeParametersString($parameters) {
    array_walk($parameters, create_function('&$v,$k', '$v = $k."=".$v;') );
    return implode('&',$parameters);
  }
  
  function ComputeUrl($parameters) {
    $url = '/vl/api.php';
    if( !$parameters )
      return $url;
    return $url.'?'.LocalApiCall::ComputeParametersString(LocalApiCall::FullParametersArray($parameters));
  }
  
  function Reply($code) {
    $calledUrl = 'http://'.$this->httpClient->host.':'.$this->httpClient->port.$this->url;
		if( $code != HTTP_STATUS_OK )
      return array($calledUrl,'ERROR '.$code);
		return array($calledUrl,$this->httpClient->get_response_body());
  }
  
  function Get($parameters) {
    $this->url = LocalApiCall::ComputeUrl($parameters);
    return $this->Reply( @$this->httpClient->get($this->url) );
  }
  
  function Post($parameters) {
    $this->url = LocalApiCall::ComputeUrl(false);
    return $this->Reply( @$this->httpClient->post($this->url,LocalApiCall::FullParametersArray($parameters)) );
  }
  
  function Put($parameters,&$data) {
    $this->url = LocalApiCall::ComputeUrl($parameters);
    return $this->Reply( @$this->httpClient->put($this->url,$data) );
  }
}


// base class for all demos
class Demo
{
  function Name() {
    return substr(get_class($this),5);
  }
  
  function UseSerialNumber() {
    return true;
  }
}

// load '_demo.php' files
loadPluginFiles('../plugins','_demo.php');

// create demo classes
$demos = array();
$currentDemo = false;
foreach( get_declared_classes() as $classname ) {
  if( strtolower(substr($classname,0,5)) == 'demo_' ) {
    $demo = new $classname();
    $demos[$demo->Name()] = $demo;
    if( @$_REQUEST['demo'] == $demo->Name() )
      $currentDemo = $demo;
  }
}
ksort($demos);

// execute demo if needed
if( isset($_REQUEST['action']) )
  list($calledUrl,$reply) = $currentDemo->Execute();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>OpenNab API demo</title>
<style type="text/css">
#all {
  position: absolute;
  width: 100%;
}
#top {
  text-align: center;
  height: 50px;
}
#main {
  min-height: 300px;
}
#menu {
  position: absolute;
  left:0;
  width: 200px;
}
#demo {
  margin-left: 210px;
}
#bottom {
  text-align: center;
  margin-top: 80px;
  height: 80px;
}
</style>
</head>
<body>
<div id='all'>
<div id='top'>
OpenNab API Demonstration
</div>
<div id='main'>
<div id='menu'>
Demos:
<ul>
<?php
  if( @$_REQUEST['sn'] )
    $param = '&sn='.$_REQUEST['sn'];
  else if( @$_REQUEST['dst'] )
    $param = '&dst='.$_REQUEST['dst'];
  else
    $param = '';
  foreach( $demos as $demo ) {
    print '<li><a href="?demo='.$demo->Name().$param.'">'.$demo->Name().'</a></li>';
  }
?>
</ul>
</div>
<div id='demo'>
<?php
if( $currentDemo !== false ) {
?>
<form method='POST' action='index.php' enctype="multipart/form-data">
<input type='hidden' name='demo' value='<?php print $currentDemo->Name(); ?>' />
<input type='hidden' name='action' value='1' />
<?php
  if( $currentDemo->UseSerialNumber() ) {
?>
Serial Number: <input type='text' name='sn' value='<?php print $_REQUEST['sn']?>' /> or 
Destination user name: <input type='text' name='dst' value='<?php print $_REQUEST['dst']?>' /><br/><br/>
<?php
  }
  $currentDemo->Display();
?>
</form><br/><br/>
<?php
	if( isset($reply) ) {
		print 'We called: '.htmlentities($calledUrl).'<br/>';
		print 'OpenNab API replied :<br/>';
		print nl2br(str_replace(' ','&nbsp;',str_replace('>','&gt;',str_replace('<','&lt;',$reply))));
	}
}
?>
</div>
</div>
<div id='bottom'>
OpenNab - An open PHP-based proxy framework for the Nabaztag™ (<a target='_blank' href='http://www.nabaztag.com/'>http://www.nabaztag.com/</a>) electronic pet.<br/>
<a target='_blank' href='http://sourceforge.net/projects/opennab/'>http://sourceforge.net/projects/opennab/</a><br/>
Copyright c 2007 OpenNab team - http://opennab.sourceforge.net/team/ <br/>
</div>
</div>
</body>