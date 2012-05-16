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
<center>| <a href="xpath.php">XPath Injection</a> || <a href="xmlinjection.php">XML Injection</a> || <a href="challenges.htm">Challenges</a> | </center>
<hr width="40%">
<hr width="60%">
<hr width="40%">
<br>
<form action='xpath.php' name='inject_form' method='get'>
	<table><tr><td>Injection String:</td></tr>
	<tr><td><textarea name='inject_string'><?php echo (isset($_REQUEST['inject_string']) ? htmlentities($_REQUEST['inject_string']) : '' ); ?></textarea></td></tr>
	<tr><td>Injection Location:</td><td>
		<select name="location">
			<option value="condition_string">String Value in Condition</option>
			<option value="condition_num" <?php echo (isset($_REQUEST['location']) and $_REQUEST['location']=='condition_num') ? 'selected' : ''; ?>>Numeric Value in Condition</option>
			<option value="node_path" <?php echo (isset($_REQUEST['location']) and $_REQUEST['location']=='node_path') ? 'selected' : ''; ?>>Node Path</option>
			<option value="node_name" <?php echo (isset($_REQUEST['location']) and $_REQUEST['location']=='node_name') ? 'selected' : ''; ?>>Node Name</option>
			<option value="condition_var" <?php echo (isset($_REQUEST['location']) and $_REQUEST['location']=='condition_var') ? 'selected' : ''; ?>>Condition Variable</option>
			<option value="sub_node" <?php echo (isset($_REQUEST['location']) and $_REQUEST['location']=='sub_node') ? 'selected' : ''; ?>>Child Node</option>
		</select></td></tr>
		<tr><td>Custom XML (*INJECT* specifies injection point):</td><td><textarea name="custom_inject"><?php echo isset($_REQUEST['custom_inject']) ? htmlentities($_REQUEST['custom_inject']) : ''; ?></textarea></td></tr>
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
			<option value="bool" <?php echo (isset($_REQUEST['query_results']) and $_REQUEST['query_results']=='bool') ? 'selected' : ''; ?>>Boolean (Zero/non-zero result set)</option>
			<option value="none" <?php echo (isset($_REQUEST['query_results']) and $_REQUEST['query_results']=='none') ? 'selected' : ''; ?>>No results</option>
		</select></td></tr>
		<tr><td>Show query?</td><td><input type='checkbox' name='show_query' <?php echo (isset($_REQUEST['show_query']) ? 'checked' : ''); ?>></td></tr>
		<tr><td>Error Verbosity:</td><td><select name="error_level">
			<option value="verbose">Verbose error messages</option>
			<option value="generic" <?php echo (isset($_REQUEST['error_level']) and $_REQUEST['error_level']=='generic') ? 'selected' : ''; ?>>Generic error messages</option>
			<option value="none" <?php echo (isset($_REQUEST['error_level']) and $_REQUEST['error_level']=='none') ? 'selected' : ''; ?>>No error messages</option>
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
	
	include_once('includes/sanitize.inc.php');
	
	if (isset($_REQUEST['custom_inject']) and $_REQUEST['custom_inject']!=''){
		
		$display_query = str_replace('*INJECT*', '<u>' . $_REQUEST['inject_string'] . '</u>', $_REQUEST['custom_inject']);
		$query = str_replace('*INJECT*', $_REQUEST['inject_string'], $_REQUEST['custom_inject']);
		
	}else{
		
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