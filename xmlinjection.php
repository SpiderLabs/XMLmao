<?php
/*
XMLmao - A configurable XML/XPath injection testbed
Daniel "unicornFurnace" Crowley
Copyright (C) 2012 Trustwave Holdings, Inc.

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <http://www.gnu.org/licenses/>.
*/
?>
<html>
<head>
<title>XMLmao - XML Injection</title>
</head>
<body>
<center><h1>XMLmao - XML Injection</h1></center><br>
<center>| <a href="xpath.php">XPath Injection</a> || <a href="xmlinjection.php">XML Injection</a> || <a href="challenges.htm">Challenges</a> | </center>
<hr width="40%">
<hr width="60%">
<hr width="40%">
<br>
<form action='xmlinjection.php' name='inject_form' method='get'>
	<table><tr><td>Injection String:</td></tr>
		<tr><td><textarea name='inject_string'><?php echo isset($_REQUEST['inject_string']) ? htmlentities($_REQUEST['inject_string']) : ''; ?></textarea></td></tr>
	<tr><td>Injection Location:</td><td>
		<select name="location">
			<option value="attribute">Attribute</option>
			<option value="value" <?php echo (isset($_REQUEST['location']) and $_REQUEST['location']=='value') ? 'selected' : ''; ?>>Node Value</option>
			<option value="cdatavalue" <?php echo (isset($_REQUEST['location']) and $_REQUEST['location']=='cdatavalue') ? 'selected' : ''; ?>>CDATA-wrapped Value</option>
			<option value="header_value" <?php echo (isset($_REQUEST['location']) and $_REQUEST['location']=='header_value') ? 'selected' : ''; ?>>Header Value</option>
		</select></td></tr>
		<tr><td>Custom XML (*INJECT* specifies injection point):</td><td><textarea name="custom_inject"><?php echo (isset($_REQUEST['custom_inject']) ? htmlentities($_REQUEST['custom_inject']) : '' ); ?></textarea></td></tr>
	<tr><td><b>Input Sanitization:</b></td></tr>
	<tr><td>Blacklist Level:</td><td><select name="blacklist_level">
		<option value="none">No blacklisting</option>
		<option value="reject_low" <?php if(isset($_REQUEST["blacklist_level"]) and $_REQUEST["blacklist_level"]=="reject_low") echo "selected"; ?>>Reject (Low)</option>
		<option value="reject_high" <?php if(isset($_REQUEST["blacklist_level"]) and $_REQUEST["blacklist_level"]=="reject_high") echo "selected"; ?>>Reject (High)</option>
		<option value="escape" <?php if(isset($_REQUEST["blacklist_level"]) and $_REQUEST["blacklist_level"]=="escape") echo "selected"; ?>>Escape</option>
		<option value="low" <?php if(isset($_REQUEST["blacklist_level"]) and $_REQUEST["blacklist_level"]=="low") echo "selected"; ?>>Remove (Low)</option>
		<option value="medium" <?php if(isset($_REQUEST["blacklist_level"]) and $_REQUEST["blacklist_level"]=="medium") echo "selected"; ?>>Remove (Medium)</option>
		<option value="high" <?php if(isset($_REQUEST["blacklist_level"]) and $_REQUEST["blacklist_level"]=="high") echo "selected"; ?>>Remove (High)</option>
	</select></td></tr>
	<tr><td>Blacklist Keywords (comma separated):</td><td><textarea name="blacklist_keywords"><?php if(isset($_REQUEST["blacklist_keywords"])) echo $_REQUEST["blacklist_keywords"]; ?></textarea></td></tr>
	<tr><td><b>Output Level:</b></td></tr>
		<tr><td>Output Query Results:</td><td><select name="query_results">
			<option value="all">All results</option>
			<option value="one" <?php echo (isset($_REQUEST['query_results']) and $_REQUEST['query_results']=='one') ? 'selected' : ''; ?>>One value</option>
			<option value="none" <?php echo (isset($_REQUEST['query_results']) and $_REQUEST['query_results']=='none') ? 'selected' : ''; ?>>No results</option>
		</select></td></tr>
		<tr><td>Show XML?</td><td><input type='checkbox' name='show_xml' <?php echo (isset($_REQUEST['show_xml']) ? 'checked' : ''); ?>></td></tr>
		<tr><td>Error Verbosity:</td><td><select name="error_level">
			<option value="verbose">Verbose error messages</option>
			<option value="generic" <?php echo (isset($_REQUEST['error_level']) and $_REQUEST['error_level']=='generic') ? 'selected' : ''; ?>>Generic error messages</option>
			<option value="none" <?php echo (isset($_REQUEST['error_level']) and $_REQUEST['error_level']=='none') ? 'selected' : ''; ?>>No error messages</option>
		</select></td></tr>
	</table>
	<input type="submit" name="submit" value="Inject!">
</form>

<?php
$xmldata = '<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE xmlfile [  
<!ENTITY author "Inject4" > ]>
<xmlfile>
 <hooray attrib="Inject2">
  <ilovepie>Inject1</ilovepie>
 </hooray>
 <data>
	<![CDATA[Inject3]]>
 </data>
</xmlfile>
';

if(isset($_REQUEST['submit'])){

	include_once('includes/sanitize.inc.php');

	if (isset($_REQUEST['custom_inject']) and $_REQUEST['custom_inject']!=''){
		$displayxml = str_replace('*INJECT*', '<u>' . htmlentities($_REQUEST['inject_string']) . '</u>', htmlentities($_REQUEST['custom_inject']));
		$xmldata = str_replace('*INJECT*', $_REQUEST['inject_string'], $_REQUEST['custom_inject']);
	}else{
		switch($_REQUEST['location']){
			case 'attribute':
				$displayxml = str_replace('Inject2', '<u>'.htmlentities($_REQUEST['inject_string']).'</u>', htmlentities($xmldata));
				$xmldata = str_replace('Inject2', $_REQUEST['inject_string'], $xmldata);
				break;
			case 'value':
				$displayxml = str_replace('Inject1', '<u>'.htmlentities($_REQUEST['inject_string']).'</u>', htmlentities($xmldata));
				$xmldata = str_replace('Inject1', $_REQUEST['inject_string'], $xmldata);
				break;
			case 'cdatavalue':
				$displayxml = str_replace('Inject3', '<u>'.htmlentities($_REQUEST['inject_string']).'</u>', htmlentities($xmldata));
				$xmldata = str_replace('Inject3', $_REQUEST['inject_string'], $xmldata);
				break;
			case 'header_value':
				$displayxml = str_replace('Inject4', '<u>'.htmlentities($_REQUEST['inject_string']).'</u>', htmlentities($xmldata));
				$xmldata = str_replace('Inject4', $_REQUEST['inject_string'], $xmldata);
				break;
			default:
				$displayxml = str_replace('Inject2', '<u>'.htmlentities($_REQUEST['inject_string']).'</u>', htmlentities($xmldata));
				$xmldata = str_replace('Inject2', $_REQUEST['inject_string'], $xmldata);
				break;
		}
		$displayxml = str_replace("\n", '<br>', $displayxml);
	}
	
	if(isset($_REQUEST['show_xml']) and $_REQUEST['show_xml'] == 'on') echo "\nResulting XML:\n" . $displayxml . '<br>';
	
	$xml = '';
	
	if(isset($_REQUEST['error_level'])){
		switch ($_REQUEST['error_level']){
			case 'generic':
				ini_set('display_errors', 0);
				$xml = simplexml_load_string($xmldata);
				if(!$results) echo "An error occurred." . "\n<br>";
				break;
			case 'verbose':
				ini_set('display_errors', 1);
				$xml = simplexml_load_string($xmldata);
				break;
			case 'none':
				ini_set('display_errors', 0);
				$xml = simplexml_load_string($xmldata);
				break;
		}
	}
	
	switch ($_REQUEST['query_results']){
		case 'all':
			foreach ($xml->data as $data){
				echo $data . '<br>';
			}
			break;
		case 'one':
			echo $xml->data[0];
			break;
	}
}
?>
</body>
</html>