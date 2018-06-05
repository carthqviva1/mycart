<?php
/*
Plugin Name: IImagine Questionaire
Plugin URI: https://iimagine.life/iimagine-questionair
Description: This is light weight ans simple plugin for show questions and their multiple choice answers.
Version: 1.0.0
Author: I Imagine
Author URI: https://iimagine.life/
License: GPL
Text Domain: iimagine-questionair
*/
?>
<?php

add_action('admin_menu', 'iimagine_qstnair_menu');

function iimagine_qstnair_menu(){
   add_menu_page(__('Questionnair','iimagine-questionair'), __('Questionnair','iimagine-questionair'), 'manage_options', 'questionair', 'qstnaire_page' );
   add_submenu_page( 'questionair', 'Questionair Listings', 'Listings',
    'manage_options', 'qlisting', 'show_all_listings');
}

function qstnaire_page(){
   ob_start();
	 include plugin_dir_path(__FILE__)."/recordform.php";
	echo ob_get_clean();
}

function show_all_listings(){
   ob_start();
	 include plugin_dir_path(__FILE__)."/record-listing.php";
	echo ob_get_clean();
}

add_shortcode( 'QUESTIONAIRE', 'iimagine_questionaires' );

function iimagine_questionaires() {
	ob_start();
	include plugin_dir_path(__FILE__)."/qnstform.php";
	echo ob_get_clean();
	
}


function send_user_info_to_aweber_account($name, $email){

require_once(plugin_dir_path( __FILE__ ).'aweber_api/aweber_api.php');

$consumerKey    = "AkGRLwBgUwSuVPPnaB0lg3FS";
$consumerSecret = "p4YySq128faCRFqxLdtdterWdQJsUEgqixMQmCy2";
$accessKey      = "AgMDBi4wY7MWtoPdJy2n5Kx9";
$accessSecret   = "xzu4TVabY4r2Wc3xl8gbtaCEJ0I5RnmNyYAodG9z";
$account_id     = "1209998";
$list_id        = "4981357";

$aweber = new AWeberAPI($consumerKey, $consumerSecret);
$aweber->adapter->debug = false;

try {
    $account = $aweber->getAccount($accessKey, $accessSecret);
    $listURL = "/accounts/{$account_id}/lists/{$list_id}";
    $list = $account->loadFromUrl($listURL);

    # create a subscriber
    $params = array(
        'email' => $email,
        'ip_address' => '127.0.0.1',
        'ad_tracking' => 'client_lib_example',
        'misc_notes' => 'iimagine user signups',
        'name' => $name,        
        'tags' => array(),
    );
    $subscribers = $list->subscribers;
    $new_subscriber = $subscribers->create($params);
    
    } catch(AWeberAPIException $exc) {    
    exit(1);
  }
}

add_action( 'wp_ajax_insert_qrecoreds', 'my_ajax_insert_qrecoreds_handler' );

function my_ajax_insert_qrecoreds_handler() {
    // Make your response and echo it.
   parse_str($_REQUEST['data']);
   $bs_title = trim($bs_title);
   if($bs_title){
   global $wpdb;
      $ind_qn_table = $wpdb->prefix.'qn_datarecords';
      
      $qdata = array('bs_title'=> $bs_title,'bs_descrp'=>$bs_descrp, 'bs_scost_from'=>$bs_scost_from, 'bs_scost_to'=>$bs_scost_to, 'bs_loc'=>$bs_loc, 'bs_jobtype'=>$bs_jobtype, 'bs_online'=>$bs_online, 'ind_cat'=>$ind_cat);
      $qdataformate = array('%s','%s','%d','%d','%s','%s','%s');
     $status = $wpdb->insert($ind_qn_table ,$qdata, $qdataformate);
     if($status) 
      echo $bs_title;
   
   }else{
     echo 'Title must required!';  
   }
    // Don't forget to stop execution afterward.
    wp_die();
}
