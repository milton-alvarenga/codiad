<?php

/*
*  Copyright (c) Codiad & Andr3as, distributed
*  as-is and without warranty under the MIT License. See
*  [root]/license.txt for more. This information must remain intact.
*/

class Settings {
	
	const DEFAULT_OPTIONS = array(
		array(
			"name" => "codiad.editor.autocomplete",
			"value" => "false",
		),
		array(
			"name" => "codiad.editor.autosave",
			"value" => "true",
		),
		array(
			"name" => "codiad.editor.fileManagerTrigger",
			"value" => "false",
		),
		array(
			"name" => "codiad.editor.fontSize",
			"value" => "14px",
		),
		array(
			"name" => "codiad.editor.highlightLine",
			"value" => "true",
		),
		array(
			"name" => "codiad.editor.indentGuides",
			"value" => "true",
		),
		array(
			"name" => "codiad.editor.overScroll",
			"value" => "0.5",
		),
		array(
			"name" => "codiad.editor.persistentModal",
			"value" => "true",
		),
		array(
			"name" => "codiad.editor.printMargin",
			"value" => "true",
		),
		array(
			"name" => "codiad.editor.printMarginColumn",
			"value" => "80",
		),
		array(
			"name" => "codiad.editor.rightSidebarTrigger",
			"value" => "false",
		),
		array(
			"name" => "codiad.editor.softTabs",
			"value" => "false",
		),
		array(
			"name" => "codiad.editor.tabSize",
			"value" => "4",
		),
		array(
			"name" => "codiad.editor.theme",
			"value" => "twilight",
		),
		array(
			"name" => "codiad.editor.wrapMode",
			"value" => "false",
		),
		array(
			"name" => "codiad.settings.autosave",
			"value" => "true",
		),
		array(
			"name" => "codiad.settings.plugin.sync",
			"value" => "true",
		),
		array(
			"name" => "codiad.settings.plugin.sync",
			"value" => "true",
		),
	);
	
	//////////////////////////////////////////////////////////////////
	// PROPERTIES
	//////////////////////////////////////////////////////////////////
	
	public $connection    = '';
	public $username    = '';
	public $settings    = '';
	
	//////////////////////////////////////////////////////////////////
	// METHODS
	//////////////////////////////////////////////////////////////////
	
	// -----------------------------||----------------------------- //
	
	//////////////////////////////////////////////////////////////////
	// Construct
	//////////////////////////////////////////////////////////////////
	
	public function __construct() {
	}
	
	public function delete_option( $option, $username = null ) {
		
		if( $username == null ) {
			
			$query = "DELETE FROM options WHERE `name`=?";
			$bind = "s";
			$bind_variables = array(
				$option,
			);
			
			sql::sql( $query, $bind, $bind_variables, formatJSEND( "error", "Could not delete setting: $option" ) );
		} else {
			
			$query = "DELETE FROM options WHERE `name`=? AND `username`=?";
			$bind = "ss";
			$bind_variables = array(
				$option,
				$this->username,
			);
			
			sql::sql( $query, $bind, $bind_variables, formatJSEND( "error", "Could not delete setting: $option" ) );
		}
	}
	
	public function get_option( $option, $user_setting = null, $action = "return" ) {
		
		if( $user_setting == null ) {
			
			$sql = "SELECT `value` FROM `options` WHERE `name`=?;";
			$bind = "s";
			$bind_variables = array( $option );
			$return = sql::sql( $sql, $bind, $bind_variables, formatJSEND( "error", "Error fetching option: $option" ) );
			
			if( mysqli_num_rows( $return ) > 0 ) {
				
				$return = mysqli_fetch_assoc( $return )["value"];
			} else {
				
				$return = null;
			}
		} else {
			
			$sql = "SELECT `value` FROM `user_options` WHERE `name`=? AND `username`=?;";
			$bind = "ss";
			$bind_variables = array( $option, $this->username );
			$return = sql::sql( $sql, $bind, $bind_variables, formatJSEND( "error", "Error fetching option: $option" ) );
			
			if( mysqli_num_rows( $return ) > 0 ) {
				
				$return = mysqli_fetch_assoc( $return )["value"];
			} else {
				
				$return = null;
			}
		}
		
		switch( $action ) {
			
			case( "exit" ):
				
				exit( $return );
			break;
			
			case( "return" ):
				
				return( $return );
			break;
		}
	}
	
	//////////////////////////////////////////////////////////////////
	// Save User Settings
	//////////////////////////////////////////////////////////////////
	
	public function Save() {
		
		foreach( $this->settings as $option => $value ) {
			
			$this->update_option( $option, $value, $this->username );
		}
		echo formatJSEND( "success", null );
	}
	
	//////////////////////////////////////////////////////////////////
	// Load User Settings
	//////////////////////////////////////////////////////////////////
	
	public function Load() {
		
		$query = "SELECT DISTINCT * FROM user_options WHERE `username`=?;";
		$bind = "s";
		$bind_variables = array(
			$this->username
		);
		
		$options = sql::sql( $query, $bind, $bind_variables, formatJSEND( "error", "Error, Could not load user's settings." ) );
		echo formatJSEND( "success", $options );
	}
	
	public function update_option( $option, $value, $user_setting = null ) {
		
		$query = "INSERT INTO user_options ( `name`, `username`, `value` ) VALUES ( ?, ?, ? );";
		$bind = "sss";
		$bind_variables = array(
			$option,
			$this->username,
			$value,
		);
		$result = sql::sql( $query, $bind, $bind_variables, formatJSEND( "error", "Error, Could not load user's settings." ) );
		
		if( $result !== true ) {
			
			$query = "UPDATE user_options SET `value`=? WHERE `name`=? AND `username`=?;";
			$bind = "sss";
			$bind_variables = array(
				$value,
				$option,
				$this->username,
			);
			$result = sql::sql( $query, $bind, $bind_variables, formatJSEND( "error", "Error, Could not load user's settings." ) );
		}
	}
}
