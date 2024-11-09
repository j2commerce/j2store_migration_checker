<?php
/**
 * @copyright Copyright (C) 2014-2019 Weblogicx India. All rights reserved.
 * @copyright Copyright (C) 2024 J2Commerce, Inc. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
if(!defined('F0F_INCLUDED')) {
    require_once JPATH_LIBRARIES . '/f0f/include.php';
}
if(!defined('F0F_INCLUDED')) {
    ?>
    <h2>Incomplete installation detected</h2>
    <?php
}
//if j2store does not exist, just exit
if(!JFile::exists(JPATH_ADMINISTRATOR.'/components/com_j2store/j2store.php')) {
    echo '<h2>Incomplete installation detected</h2>';
    jexit(0);
}

if(!class_exists('J2StoreStrapper')){
    require_once (JPATH_ADMINISTRATOR.'/components/com_j2store/helpers/strapper.php');
}
J2StoreStrapper::addJS();
J2StoreStrapper::addCSS();

F0FDispatcher::getTmpInstance('com_j2migrationchecker')->dispatch();

