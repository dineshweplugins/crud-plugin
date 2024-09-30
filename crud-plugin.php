<?php
/**
 * 
 * Plugin Name: CRUD Plugin
 * Description: A Simple CRUD Plugin for WodPress
 * Version: 1.0
 * Author: Dinesh Suthar
 */

 if(!defined('ABSPATH')) exit;

 register_activation_hook(__FILE__, 'crud_plugin_create_table');

 function crud_plugin_create_table()
 {
    global $wpdb;
    $table_name = $wpdb->prefix . 'crud_items';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        title varchar(255) NOT NULL,
        description text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
 }
 register_deactivation_hook(__FILE__, 'crud_plugin_drop_table');

 function crud_plugin_drop_table(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'crud_items';
    $sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
 }

 add_action('admin_menu', 'crud_plugin_admin_menu');

 function crud_plugin_admin_menu(){
    add_menu_page(
        'CRUD Items', 
        'CRUD Items',
        'manage_options',
        'crud-items',
        'crud_plugin_admin_page',
        'dashicons-list-view',
        6
    );
 }

 function crud_plugin_admin_page(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'crud_items';

    if(isset($_POST['submit'])){
        $title = sanitize_text_field($_POST['title']);
        $description = sanitize_textarea_field($_POST['description']);

        if(!empty($title) && !empty($description)){
            $wpdb->insert(
                $table_name,
                array(
                    'title' => $title,
                    'description' => $description,
                )
            );
            echo '<div class="updated"><p> Item added successfully!</p></div>';            
        }
    }
?>
<div class="wrap">
    <h1>Crud Items</h1>
    <form method="POST">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="title">Title</label></th>
                    <td><input type="text" name="title" id="title" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="description">Description</label></th>
                    <td><textarea name="description" id="description" rows="5" class="large-text"></textarea></td>
                </tr>
            </table>
            <p><input type="submit" name="submit" id="submit" class="button button-primary" value="Add Item"></p>
    </form>

    <hr>

    <h2>All Items</h2>
    <table class="widefat fixed" cellspacing="0">
        <thead>
            <tr>
                <th id="columnname" class="manage-column column-columnname" scope="col">Title</th>
                <th id="columnname" class="manage-column column-columnname" scope="col">Description</th>
                <th id="columnname" class="manage-column column-columnname" scope="col">Actions</th>
            </tr>
        <thead>
            <tbody>
                <?php 
                $results = $wpdb->get_results("SELECT * FROM $table_name");
                foreach($results as $row){
                    echo "<tr>";
                    echo "<td>{$row->title}</td>";
                    echo "<td>{$row->description}</td>";
                    echo "<td><a href='?page=crud-items&delete={$row->id}' class='button button-danger'>Delete</a></td>";
                    echo "<tr>";                
                }
                ?>
            </tbody>
        </theading>
    </table>
</div>
<?php
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $wpdb->delete($table_name, ['id' => $id]);
    echo '<div class="updated"><p>Item deleted successfully!</p></div>';
    echo "<script>location.replace('?page=crud-items');</script>";
    }
 }
 ?>