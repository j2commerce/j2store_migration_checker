<?php
/**
 * @copyright Copyright (C) 2014-2019 Weblogicx India. All rights reserved.
 * @copyright Copyright (C) 2024 J2Commerce, Inc. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_ADMINISTRATOR . '/components/com_j2store/version.php');

class Com_J2MigrationCheckerInstallerScript
{
    function preflight($type, $parent)
    {
        jimport('joomla.filesystem.file');
        $app = JFactory::getApplication();
        $version_file = JPATH_ADMINISTRATOR . '/components/com_j2store/version.php';
        if (JFile::exists($version_file)) {
            require_once($version_file);
            // abort if the current J2Store release is higher than 4.0.5
            if (($type == 'install') && version_compare(J2STORE_VERSION, '4.0.6', 'ge')) {
                $app->enqueueMessage('You are using a later version of J2Store 4 (higher than 4.0.5). To migrate from Joomla 3, please install 4.0.5.', 'warning');
                return false;
            }
        } else {
            $app->enqueueMessage('J2Store was not found. Make sure you have installed J2Store before installing this extension.', 'warning');
            return false;
        }

        $db = JFactory::getDbo();
        // get the table list
        $tables = $db->getTableList();
        // get prefix
        $prefix = $db->getPrefix();
        if (!in_array($prefix . 'extension_check', $tables)) {
            $query = "CREATE TABLE IF NOT EXISTS `#__extension_check` (
                                  `extension_check_id` int(11) NOT NULL AUTO_INCREMENT,
                                  `component_status` varchar(50) NOT NULL,
                                  `plugins_status` varchar(50) NOT NULL,
                                  `modules_status` varchar(50) NOT NULL,
                                  `template_status` varchar(50) NOT NULL,
                                  `installation_status` int(11) NOT NULL,
                                  PRIMARY KEY (`extension_check_id`)
                                ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
            $db->setQuery($query);
            $db->execute();
        }
        return true;
    }
}

