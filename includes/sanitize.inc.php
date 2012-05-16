<?php
/*
XMLmao - A configurable XML/XPath injection testbed
Daniel "unicornFurnace" Crowley
Copyright (C) 2012 Trustwave Holdings, Inc.

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

if(isset($_REQUEST['blacklist_keywords'])){
	$blacklist = explode(',' , $_REQUEST['blacklist_keywords']);
}

if(isset($_REQUEST['blacklist_level'])){
	switch($_REQUEST['blacklist_level']){
		//We process blacklists differently at each level. At the lowest, each keyword is removed case-sensitively.
		//At medium blacklisting, checks are done case-insensitively.
		//At the highest level, checks are done case-insensitively and repeatedly.
		
		case 'reject_low':
			foreach($blacklist as $keyword){
				if(strstr($_REQUEST['inject_string'], $keyword)!='') {
					die("\nBlacklist was triggered!");
				}
			}
			break;
		case 'reject_high':
			foreach($blacklist as $keyword){
				if(strstr(strtolower($_REQUEST['inject_string']), strtolower($keyword))!='') {
					die("\nBlacklist was triggered!");
				}
			}
			break;
		case 'escape':
			foreach($blacklist as $keyword){
				$_REQUEST['inject_string'] = str_replace($keyword, '\\'.$keyword, $_REQUEST['inject_string']);
			}
			break;
		case 'low':
			foreach($blacklist as $keyword){
				$_REQUEST['inject_string'] = str_replace($keyword, '', $_REQUEST['inject_string']);
			}
			break;
		case 'medium':
			foreach($blacklist as $keyword){
				$_REQUEST['inject_string'] = str_replace(strtolower($keyword), '', strtolower($_REQUEST['inject_string']));
			}
			break;
		case 'high':
			do{
				$keyword_found = 0;
				foreach($blacklist as $keyword){
					$_REQUEST['inject_string'] = str_replace(strtolower($keyword), '', strtolower($_REQUEST['inject_string']), $count);
					$keyword_found += $count;
				}	
			}while ($keyword_found);
			break;
		
	}
}

?>