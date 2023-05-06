<?php
/**
 * Plugin Name: k-review
 * Description: k-review
 * Version: 0.0.1
 * Author: 
 * Author URI: 
 */

 /** @todo générer la dépendance automatiquement avec la dépendance de plugin */
 
 require_once(plugin_dir_path(__FILE__) . 'vendor/autoload.php');

 /** Utilisation de la classe Plugin et Path pour construire l'appel du plugin */
 use KLib2\Core\Plugin;
 use KReview\Path;
 
 /** Chargment du plugin */
 $kreview_plugin = new Plugin(new Path());
 $kreview_plugin->load();
 