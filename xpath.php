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
<title>XMLmao - XPath Injection</title>
</head>
<body>
<center><h1>XMLmao - XPath Injection</h1></center><br>
<hr width="40%">
<hr width="60%">
<hr width="40%">
<br>
<form action='xpath.php' name='inject_form' method='get'>
	<table><tr><td>Injection String:</td><td><input type='text' name='inject_string'></td></tr>
	<tr><td>Injection Location:</td><td>
		<select name="location">
			<option value="node_path">Node Path</option>
			<option value="node_name">Node Name</option>
			<option value="condition_var">Condition Variable</option>
			<option value="condition_string">String Value in Condition</option>
			<option value="condition_num">Numeric Value in Condition</option>
			<option value="sub_node">Child Node</option>
			<option value="entire_query">Entire Query</option>
		</select></td></tr>
	<tr><td><b>Input Sanitization:</b></td></tr>
		<tr><td>Remove Quotes?</td><td><input type='checkbox' name="quotes_remove"></td></tr>
		<tr><td>Remove Spaces?</td><td><input type="checkbox" name="spaces_remove"></td></tr>
		<tr><td>Remove Square Brackets?</td><td><input type="checkbox" name="brackets_remove"></td></tr>
		<tr><td>Remove Slashes?</td><td><input type="checkbox" name="slashes_remove"></td></tr>
		<tr><td>Remove Pipes?</td><td><input type="checkbox" name="pipes_remove"></td></tr>
	<tr><td><b>Output Level:</b></td></tr>
		<tr><td>Output Query Results:</td><td><select name="query_results">
			<option value="all">All results</option>
			<option value="one">One value</option>
			<option value="bool">Boolean (Zero/non-zero result set)</option>
			<option value="none">No results</option>
		</select></td></tr>
		<tr><td>Show query?</td><td><input type='checkbox' name='show_query'></td></tr>
		<tr><td>Error Verbosity:</td><td><select name="error_level">
			<option value="verbose">Verbose error messages</option>
			<option value="generic">Generic error messages</option>
			<option value="none">No error messages</option>
		</select></td></tr>
	</table>
	<input type="submit" name="submit" value="Inject!">
</form>

<?php
if(isset($_REQUEST['submit'])){
	$node_path = $display_node_path = '/xmlfile/users';
	$node_name = $display_node_name = '/user';
	$condition = $display_condition = "username='jsmiley'";
	$sub_node = $display_sub_node = '/username';
	
	//sanitization section
	if(isset($_REQUEST['quotes_remove']) and $_REQUEST['quotes_remove'] == 'on') $_REQUEST['inject_string'] = str_replace("'", "\'", $_REQUEST['inject_string']);
	if(isset($_REQUEST['spaces_remove']) and $_REQUEST['spaces_remove'] == 'on') $_REQUEST['inject_string'] = str_replace(' ', '', $_REQUEST['inject_string']);
	if(isset($_REQUEST['pipes_remove']) and $_REQUEST['pipes_remove'] == 'on') $_REQUEST['inject_string'] = str_replace('|', '', $_REQUEST['inject_string']);
	if(isset($_REQUEST['slashes_remove']) and $_REQUEST['slashes_remove'] == 'on') $_REQUEST['inject_string'] = str_replace('/', '', $_REQUEST['inject_string']);
	if(isset($_REQUEST['brackets_remove']) and $_REQUEST['brackets_remove'] == 'on'){
		$_REQUEST['inject_string'] = str_replace('[', '', $_REQUEST['inject_string']);
		$_REQUEST['inject_string'] = str_replace(']', '', $_REQUEST['inject_string']);
	}
	
	switch ($_REQUEST['location']){
		case 'node_path':
			$node_path = $_REQUEST['inject_string'];
			$display_node_path = '<u>' . $_REQUEST['inject_string'] . '</u>';
			break;
		case 'node_name':
			$node_name = '/' . $_REQUEST['inject_string'];
			$display_node_name = '/<u>' . $_REQUEST['inject_string'] . '</u>';
			break;
		case 'condition_var':
			$condition = $_REQUEST['inject_string'] . "='jsmiley'";
			$display_condition = '<u>' . $_REQUEST['inject_string'] . "</u>='jsmiley'";
			break;
		case 'condition_string':
			$condition = "username='" . $_REQUEST['inject_string'] . "'";
			$display_condition = "username='<u>" . $_REQUEST['inject_string'] . "</u>'";
			break;
		case 'condition_num':
			$condition = 'id=' . $_REQUEST['inject_string'];
			$display_condition = 'id=<u>' . $_REQUEST['inject_string'] . '</u>';
			break;
		case 'sub_node':
			$sub_node = '/' . $_REQUEST['inject_string'];
			$display_sub_node = '/<u>' . $_REQUEST['inject_string'] . '</u>';
			break;
	}
	
	$display_query = $display_node_path . $display_node_name . "[". $display_condition . "]" . $display_sub_node;
	$query = $node_path . $node_name . "[". $condition . "]" . $sub_node;
	if($_REQUEST['location'] == 'entire_query'){
		$query = $_REQUEST['inject_string'];	
		$display_query = '<u>' . $query . '</u>';
	}

	$xml = simplexml_load_file('data.xml');
	
	$results = '';
	
	if(isset($_REQUEST['error_level'])){
		switch ($_REQUEST['error_level']){
			case 'generic':
				ini_set('display_errors', 0);
				$results = $xml->xpath($query);
				if(!$results) echo "An error occurred." . "\n<br>";
				break;
			case 'verbose':
				ini_set('display_errors', 1);
				$results = $xml->xpath($query);
				break;
			case 'none':
				ini_set('display_errors', 0);
				$results = $xml->xpath($query);
				break;
		}
	}
	
	if(isset($_REQUEST['show_query']) and $_REQUEST['show_query'] == 'on') echo 'Executed query: ' . $display_query . '<br>';

	if($results){
		switch($_REQUEST['query_results']){
			case 'all':
				print_r($results);
				break;
			case 'one':
				print($results[0]);
				break;
			case 'bool':
				if($results[0]) echo "Got results!<br>";
				break;
		}
	}
	
}

?>
</body>
</html>