<?php
/*
Plugin Name: Skip Navigation Links Builder
Plugin URI: https://coodip.com/plugins/skip-navigation-links-builder
Description: build skip navigation links for people with an impairment
Version: 1.0
Author: junichirojp
Author URI: https://github.com/junichirojp
License: GPL2
*/

/*  Copyright 2020 junichirojp (github : https://github.com/junichirojp)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// WordPress 3.5以上
// php5.4
// array -> []

// initialize on activated this plugin
register_activation_hook(__FILE__, 'SkipNavigationLinksBuilder::activation' );
register_uninstall_hook(__FILE__, 'SkipNavigationLinksBuilder::uninstall' );

class SkipNavigationLinksBuilder {

	const TABLE_NAME = 'skip_navigation_links_builder';

	public function __construct() {
		add_action('admin_menu', [ $this, 'set_plugin_sub_menu' ]);
	}

	// add menu on option page
	public function set_plugin_sub_menu()
	{
		// setting page
		add_options_page(__('Skip Navigation Links Setting'), __('Skip Nav Links'), 'manage_options', 'skip-navigation-links', [$this, 'setting_page']);
	}

	// setting page
	public function setting_page() {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLE_NAME;
		$rows = $wpdb->get_results("select * from {$table}");

		// load html template
		require_once plugin_dir_path(__FILE__) . "form.php";
	}

	// save / update rows
	public function update_rows() {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLE_NAME;

		// save
		if($_POST['links']){
			foreach ($_POST['links'] as $id => $link){
				//if validate is false, skip save
				if(!$this->checkValid($link))  continue;

				$wpdb->update($table,
					['label' => $link['label'], 'target_id' => $link['target_id']],
					['id' => $id],
					['label' => '%s', 'target_id' => '%s']
				);
			}
		}

		// update
		if($_POST['newLinks']){
			foreach ($_POST['newLinks'] as $link){
				//if validate is false, skip save
				if(!$this->checkValid($link))  continue;

				$wpdb->insert($table,
					['label' => $link['label'], 'target_id' => $link['target_id']],
					['label' => '%s', 'target_id' => '%s']
				);
			}
		}

		$this->redirect_home();
	}

	// delete row
	public function delete_row() {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLE_NAME;
		$wpdb->delete($table, ['id' => $_GET['id']]);

		$this->redirect_home();
	}

	public function checkValid($row) {
		if(empty($row['label']) || empty($row['target_id'])) {
			return false;
		}
		return true;
	}

	// redirect plugin home
	public function redirect_home() {
		header('Location: ' . admin_url('options-general.php?page=skip-navigation-links'));
		exit;
	}

	// run when plugin is activated
	public static function activation() {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLE_NAME;
		$charset_collate = $wpdb->get_charset_collate();

		// Create table
		$sql = "CREATE TABLE {$table} (
			id int(11) NOT NULL AUTO_INCREMENT,
			label varchar(255) NOT NULL,
			target_id varchar(255) NOT NULL,
			created datetime NULL,
			updated datetime NULL,
			PRIMARY KEY  (id)
		) {$charset_collate};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		// insert data
		$data = [
			[
				'label' => __('main content'),
				'target_id' => 'site-content',
				'created' => current_time('mysql'),
				'updated' => current_time('mysql'),
			],
			[
				'label' => __('main menu'),
				'target_id' => 'menu-main-menu',
				'created' => current_time('mysql'),
				'updated' => current_time('mysql'),
			],
			[
				'label' => __('side bar'),
				'target_id' => 'sidebar-1',
				'created' => current_time('mysql'),
				'updated' => current_time('mysql'),
			],
			[
				'label' => __('footer menu'),
				'target_id' => 'menu-footer-menu',
				'created' => current_time('mysql'),
				'updated' => current_time('mysql'),
			],
		];
		foreach ($data as $row){
			$wpdb->insert($table, $row);
		}
	}

	// run when plugin is uninstall
	public static function uninstall() {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLE_NAME;

		// drop table
		$sql = "DROP TABLE IF EXISTS {$table};";
		$wpdb->query($sql);
	}
}


$snlb = new SkipNavigationLinksBuilder();

// only run on this plugin page
if(is_admin() && isset($_GET['page']) && $_GET['page'] == 'skip-navigation-links'){
	// update rows
	if($_POST){
		$snlb->update_rows();
	}
	// delete row
	if(!empty($_GET['action']) && $_GET['action'] == 'delete-row'){
		$snlb->delete_row();
	}
}


//load_plugin_textdomain('your-unique-name', false, basename( dirname( __FILE__ ) ) . '/languages' );

//wp_enqueue_style()
//wp_enqueue_script()
