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
| <a href="xpath.php">XPath Injection</a> || <a href="xmlinjection.php">XML Injection</a> || <a href="challenges.htm">Challenges</a> | 
<hr width="40%">
<hr width="60%">
<hr width="40%">
<br>
<form action='xmlinjection.php' name='inject_form' method='get'>
	<table><tr><td>Injection String:</td></tr>
		<tr><td><textarea name='inject_string'></textarea></td></tr>
	<tr><td>Injection Location:</td><td>
		<select name="location">
			<option value="attribute">Attribute</option>
			<option value="value" <?php echo (isset($_REQUEST['location']) and $_REQUEST['location']=='value' ? 'selected' : ''); ?>>Node Value</option>
			<option value="cdatavalue" <?php echo (isset($_REQUEST['location']) and $_REQUEST['location']=='cdatavalue' ? 'selected' : ''); ?>>CDATA-wrapped Value</option>
		</select></td></tr>
	<tr><td><b>Input Sanitization:</b></td></tr>
		<tr><td>Remove Quotes?</td><td><input type='checkbox' name="quotes_remove" <?php echo (isset($_REQUEST['quotes_remove']) ? 'checked' : ''); ?>></td></tr>
		<tr><td>Remove Spaces?</td><td><input type="checkbox" name="spaces_remove" <?php echo (isset($_REQUEST['spaces_remove']) ? 'checked' : ''); ?>></td></tr>
		<tr><td>Remove Angle Brackets &lt; &gt;?</td><td><input type="checkbox" name="angle_remove" <?php echo (isset($_REQUEST['angle_remove']) ? 'checked' : ''); ?>></td></tr>
		<tr><td>Remove Square Brackets [ ]?</td><td><input type="checkbox" name="brackets_remove" <?php echo (isset($_REQUEST['brackets_remove']) ? 'checked' : ''); ?>></td></tr>
	<tr><td><b>Output Level:</b></td></tr>
		<tr><td>Output Query Results:</td><td><select name="query_results">
			<option value="all">All results</option>
			<option value="one" <?php echo (isset($_REQUEST['query_results']) and $_REQUEST['query_results']=='one' ? 'selected' : ''); ?>>One value</option>
			<option value="none" <?php echo (isset($_REQUEST['query_results']) and $_REQUEST['query_results']=='none' ? 'selected' : ''); ?>>No results</option>
		</select></td></tr>
		<tr><td>Show XML?</td><td><input type='checkbox' name='show_xml' <?php echo (isset($_REQUEST['show_xml']) ? 'checked' : ''); ?>></td></tr>
		<tr><td>Error Verbosity:</td><td><select name="error_level">
			<option value="verbose">Verbose error messages</option>
			<option value="generic" <?php echo (isset($_REQUEST['error_level']) and $_REQUEST['error_level']=='generic' ? 'selected' : ''); ?>>Generic error messages</option>
			<option value="none" <?php echo (isset($_REQUEST['error_level']) and $_REQUEST['error_level']=='none' ? 'selected' : ''); ?>>No error messages</option>
		</select></td></tr>
	</table>
	<input type="submit" name="submit" value="Inject!">
</form>

<?php
$xmldata = '
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
	
	//sanitization section
	if(isset($_REQUEST['quotes_remove']) and $_REQUEST['quotes_remove'] == 'on') $_REQUEST['inject_string'] = str_replace("'", "", $_REQUEST['inject_string']);
	if(isset($_REQUEST['spaces_remove']) and $_REQUEST['spaces_remove'] == 'on') $_REQUEST['inject_string'] = str_replace(' ', '', $_REQUEST['inject_string']);
	if(isset($_REQUEST['brackets_remove']) and $_REQUEST['brackets_remove'] == 'on'){
		$_REQUEST['inject_string'] = str_replace('[', '', $_REQUEST['inject_string']);
		$_REQUEST['inject_string'] = str_replace(']', '', $_REQUEST['inject_string']);
	}
	if(isset($_REQUEST['angle_remove']) and $_REQUEST['angle_remove'] == 'on'){
		$_REQUEST['inject_string'] = str_replace('<', '', $_REQUEST['inject_string']);
		$_REQUEST['inject_string'] = str_replace('>', '', $_REQUEST['inject_string']);
	}
	
	switch($_REQUEST['location']){
		case 'attribute':
			$displayxml = str_replace('Inject2', '<u>'.$_REQUEST['inject_string'].'</u>', $xmldata);
			$xmldata = str_replace('Inject2', $_REQUEST['inject_string'], $xmldata);
			break;
		case 'value':
			$displayxml = str_replace('Inject1', '<u>'.$_REQUEST['inject_string'].'</u>', $xmldata);
			$xmldata = str_replace('Inject1', $_REQUEST['inject_string'], $xmldata);
			break;
		case 'cdatavalue':
			$displayxml = str_replace('Inject3', '<u>'.$_REQUEST['inject_string'].'</u>', $xmldata);
			$xmldata = str_replace('Inject3', $_REQUEST['inject_string'], $xmldata);
			break;
	}
	
	if(isset($_REQUEST['show_xml']) and $_REQUEST['show_xml'] == 'on') echo 'Resulting XML: ' . htmlentities($xmldata) . '<br>';
	
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