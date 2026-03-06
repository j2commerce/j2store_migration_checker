<?php
/**
 * @copyright Copyright (C) 2024-26 J2Commerce, Inc. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\Database\DatabaseInterface;

class Com_J2MigrationCheckerInstallerScript extends InstallerScript
{
    /*
	 * Minimum J2Commerce version required
	 */
    protected $minimumJ2Commerce = '4.1.0';

    /**
     * J2Commerce download link
     */
    protected $J2CommerceDownloadLink = 'https://github.com/j2commerce/j2cart/releases/download/v4.1.0/com_j2store_v4-4.1.0-pro.zip';


    /**
     * Extension script constructor
     */
    public function __construct($parent)
    {
        $this->extension = 'com_j2migrationchecker';
        $this->minimumJoomla = '5.4.0';
    }

    /**
     * Called before any type of action
     *
     * @param string $action Which action is happening (install|uninstall|discover_install|update)
     * @param InstallerAdapter $installer The class calling this method
     *
     * @return boolean True on success
     */
    public function preflight($action, $installer)
    {
        if ($action === 'uninstall') {
            return true;
        }

        // checks minimum PHP and Joomla versions and that an upgrade is performed
        if (!parent::preflight($action, $installer)) {
            return false;
        }

        $app = Factory::getApplication();
        $version_file = JPATH_ADMINISTRATOR . '/components/com_j2store/version.php';
        if (!is_file($version_file)) {
            $app->enqueueMessage('J2Commerce was not found. You can install a fresh version of J2Commerce v4.1+ in Joomla 6.', 'warning');
            return false;
        }

        $version = strval(simplexml_load_file(JPATH_ADMINISTRATOR . '/components/com_j2store/j2store.xml')->version);
        if (!version_compare($version, $this->minimumJ2Commerce, 'ge')) {
            $app->enqueueMessage('J2Commerce 4.1+ must be installed before you can start the update to Joomla 6.', 'warning');
            return false;
        }

        $db = Factory::getContainer()->get(DatabaseInterface::class);

        // Delete old extension_check table if it exists from previous migration
        $db->setQuery('DROP TABLE IF EXISTS `#__extension_check`');
        try {
            $db->execute();
        } catch (\Exception $e) {
        }

        // Create new extensions_check table
        $db->setQuery('CREATE TABLE IF NOT EXISTS `#__extensions_check` (
            `extension_id` int(11) NOT NULL AUTO_INCREMENT,
            `disabled` tinyint NOT NULL DEFAULT 0,
            PRIMARY KEY (`extension_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;');

        try {
            $db->execute();
        } catch (\Exception $e) {
        }

        return true;
    }

    /**
     * method to uninstall the component
     *
     * @return void
     */
    public function uninstall($installer)
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $db->setQuery('DROP TABLE IF EXISTS `#__extensions_check`');

        try {
            $db->execute();
        } catch (\Exception $e) {
        }
    }
}
