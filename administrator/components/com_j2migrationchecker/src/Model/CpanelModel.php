<?php
/**
 * @copyright Copyright (C) 2024-26 J2Commerce, Inc. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */

namespace J2Commerce\Component\J2migrationchecker\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;

class CpanelModel extends AdminModel
{
    /**
     * Method to get a form object.
     *
     * @param array $data Data for the form.
     * @param boolean $loadData True if the form is to load its own data.
     * @return \Joomla\CMS\Form\Form|bool Form object on success, false on failure.
     */
    public function getForm($data = array(), $loadData = true)
    {
        // This model doesn't use a form
        return false;
    }

    /**
     * Get list of J2Store components
     *
     * @return array List of component objects
     */
    public function getListComponents()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_easycheckout'));

        $db->setQuery($query);

        try {
            return $db->loadObjectList();
        } catch (\Exception $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return [];
        }
    }

    /**
     * Get list of J2Store plugins
     *
     * @return array List of plugin objects with core flag
     */
    public function getListPlugins()
    {
        $extraPlugins = [
            ['folder' => 'flexicontent_fields', 'element' => 'j2commerce'],
            ['folder' => 'flexicontent_fields', 'element' => 'j2store'],
            ['folder' => 'system', 'element' => 'flexicontentj2store'],
            ['folder' => 'ars', 'element' => 'j2creleases'],
            ['folder' => 'content', 'element' => 'j2store'],
            ['folder' => 'finder', 'element' => 'j2store'],
            ['folder' => 'install', 'element' => 'j2store'],
            ['folder' => 'osmap', 'element' => 'com_j2store'],
            ['folder' => 'quickicon', 'element' => 'j2store'],
            ['folder' => 'schemaorg', 'element' => 'ecommerce'],
            ['folder' => 'search', 'element' => 'j2store'],
            ['folder' => 'system', 'element' => 'advancedcart'],
            ['folder' => 'system', 'element' => 'articleredirect'],
            ['folder' => 'system', 'element' => 'j2canonical'],
            ['folder' => 'system', 'element' => 'easycheckout'],
            ['folder' => 'system', 'element' => 'ecommerceschema'],
            ['folder' => 'system', 'element' => 'hotjar'],
            ['folder' => 'system', 'element' => 'j2pagecache'],
            ['folder' => 'system', 'element' => 'j2store'],
            ['folder' => 'system', 'element' => 'klavyioapi'],
            ['folder' => 'system', 'element' => 'payboxurl'],
            ['folder' => 'system', 'element' => 'yootheme_j2commerce'],
            ['folder' => 'user', 'element' => 'j2userregister'],
        ];

        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));

        // Build WHERE clause for j2store folder OR extra plugins
        $whereConditions = [];
        $whereConditions[] = $db->quoteName('folder') . ' = ' . $db->quote('j2store');

        // Add each extra plugin as a condition (folder AND element)
        foreach ($extraPlugins as $plugin) {
            $whereConditions[] = '(' . $db->quoteName('folder') . ' = ' . $db->quote($plugin['folder']) .
                                ' AND ' . $db->quoteName('element') . ' = ' . $db->quote($plugin['element']) . ')';
        }

        $query->where('(' . implode(' OR ', $whereConditions) . ')');
        $query->order($db->quoteName('element') . ' ASC');

        $db->setQuery($query);

        try {
            $items = $db->loadObjectList();
        } catch (\Exception $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return [];
        }

        // Define core J2Store plugins
        $corePlugins = [
            ['folder' => 'j2store', 'element' => 'app_bootstrap3'],
            ['folder' => 'j2store', 'element' => 'app_bootstrap4'],
            ['folder' => 'j2store', 'element' => 'app_bootstrap5'],
            ['folder' => 'j2store', 'element' => 'app_currencyupdater'],
            ['folder' => 'j2store', 'element' => 'app_diagnostics'],
            ['folder' => 'j2store', 'element' => 'app_flexivariable'],
            ['folder' => 'j2store', 'element' => 'app_localization_data'],
            ['folder' => 'j2store', 'element' => 'app_schemaproducts'],
            ['folder' => 'j2store', 'element' => 'payment_banktransfer'],
            ['folder' => 'j2store', 'element' => 'payment_cash'],
            ['folder' => 'j2store', 'element' => 'payment_moneyorder'],
            ['folder' => 'j2store', 'element' => 'payment_paypal'],
            ['folder' => 'j2store', 'element' => 'payment_sagepayform'],
            ['folder' => 'j2store', 'element' => 'report_itemised'],
            ['folder' => 'j2store', 'element' => 'report_products'],
            ['folder' => 'j2store', 'element' => 'shipping_free'],
            ['folder' => 'j2store', 'element' => 'shipping_standard'],
            ['folder' => 'content', 'element' => 'j2store'],
            ['folder' => 'finder', 'element' => 'j2store'],
            ['folder' => 'install', 'element' => 'j2store'],
            ['folder' => 'search', 'element' => 'j2store'],
            ['folder' => 'system', 'element' => 'j2canonical'],
            ['folder' => 'system', 'element' => 'j2pagecache'],
            ['folder' => 'system', 'element' => 'j2store'],
            ['folder' => 'user', 'element' => 'j2userregister'],
        ];

        // Mark core plugins by comparing both folder and element
        foreach ($items as $item) {
            $item->core = 0;
            foreach ($corePlugins as $corePlugin) {
                if ($item->folder === $corePlugin['folder'] && $item->element === $corePlugin['element']) {
                    $item->core = 1;
                    break;
                }
            }
        }

        return $items;
    }

    /**
     * Get list of J2Store modules
     *
     * @return array List of J2Store module objects
     */
    public function getListModules()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('module'));

        $db->setQuery($query);

        try {
            $allModules = $db->loadObjectList();
        } catch (\Exception $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return [];
        }

        // Define J2Store modules
        $j2storeModules = [
            'mod_j2commerce_advancedcart',
            'mod_j2commerce_advancedcart_toggle',
            'mod_j2commerce_categories',
            'mod_j2commerce_chart',
            'mod_j2commerce_checklist',
            'mod_j2commerce_productreviews',
            'mod_j2commerce_products',
            'mod_j2commerce_search',
            'mod_j2products',
            'mod_j2store_detailcart',
            'mod_j2store_cart',
            'mod_j2store_categories',
            'mod_j2store_chart',
            'mod_j2store_currency',
            'mod_j2store_menu',
            'mod_j2store_orders',
            'mod_j2store_related_products',
            'mod_j2store_search',
            'mod_j2store_stats',
            'mod_j2store_stats_mini',
            'mod_j2store_wishlist',
        ];

        $coreModules = [
            'mod_j2store_cart',
            'mod_j2store_chart',
            'mod_j2store_currency',
            'mod_j2store_menu',
            'mod_j2store_orders',
            'mod_j2store_stats',
            'mod_j2store_stats_mini',
        ];

        // Filter only J2Store modules and mark core modules
        $result = [];
        foreach ($allModules as $module) {
            if (!empty($module->element) && in_array($module->element, $j2storeModules, true)) {
                $module->core = in_array($module->element, $coreModules, true) ? 1 : 0;
                $result[] = $module;
            }
        }

        return $result;
    }

    /**
     * Unpublish extensions and track them in extensions_check table
     *
     * @param array $ids Array of extension IDs to unpublish
     * @return bool True on success
     */
    public function unpublishExtensions($ids)
    {
        if (empty($ids)) {
            return false;
        }

        $db = $this->getDatabase();

        try {
            // Disable extensions in #__extensions table
            foreach ($ids as $id) {
                $updateQuery = $db->getQuery(true)
                    ->update($db->quoteName('#__extensions'))
                    ->set($db->quoteName('enabled') . ' = 0')
                    ->where($db->quoteName('extension_id') . ' = ' . (int)$id);
                $db->setQuery($updateQuery);
                $db->execute();

                // Add or update entry in extensions_check table
                $checkQuery = $db->getQuery(true)
                    ->select('COUNT(*)')
                    ->from($db->quoteName('#__extensions_check'))
                    ->where($db->quoteName('extension_id') . ' = ' . (int)$id);
                $db->setQuery($checkQuery);
                $exists = $db->loadResult();

                if ($exists) {
                    // Update existing record
                    $updateCheckQuery = $db->getQuery(true)
                        ->update($db->quoteName('#__extensions_check'))
                        ->set($db->quoteName('disabled') . ' = 1')
                        ->where($db->quoteName('extension_id') . ' = ' . (int)$id);
                    $db->setQuery($updateCheckQuery);
                    $db->execute();
                } else {
                    // Insert new record
                    $insertQuery = $db->getQuery(true)
                        ->insert($db->quoteName('#__extensions_check'))
                        ->columns([$db->quoteName('extension_id'), $db->quoteName('disabled')])
                        ->values((int)$id . ', 1');
                    $db->setQuery($insertQuery);
                    $db->execute();
                }
            }

            return true;
        } catch (\Exception $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Get list of tracked extension IDs from extensions_check table
     *
     * @return array Array of tracked extension IDs
     */
    public function getTrackedExtensionIds()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select($db->quoteName('extension_id'))
            ->from($db->quoteName('#__extensions_check'));
        $db->setQuery($query);

        try {
            return (array) $db->loadColumn();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get list of disabled extensions from extensions_check table
     *
     * @return array Array of extension IDs that are disabled
     */
    public function getDisabledExtensions()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select($db->quoteName('extension_id'))
            ->from($db->quoteName('#__extensions_check'))
            ->where($db->quoteName('disabled') . ' = 1');
        $db->setQuery($query);

        try {
            return $db->loadColumn();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get total count of disabled extensions that will be re-enabled
     *
     * @return int Count of disabled extensions
     */
    public function getDisabledExtensionsCount()
    {
        return count($this->getDisabledExtensions());
    }

    /**
     * Get count of extensions that are currently enabled and tracked (were previously disabled)
     *
     * @return int Count of re-enabled extensions
     */
    public function getReenabledExtensionsCount()
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__extensions_check'))
            ->where($db->quoteName('disabled') . ' = 0');
        $db->setQuery($query);

        try {
            return (int) $db->loadResult();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Re-enable extensions and remove them from extensions_check tracking table
     *
     * @param array $ids Array of extension IDs to re-enable
     * @return bool True on success
     */
    public function republishExtensions($ids)
    {
        if (empty($ids)) {
            return false;
        }

        $db = $this->getDatabase();

        try {
            // Enable extensions in #__extensions table
            foreach ($ids as $id) {
                $updateQuery = $db->getQuery(true)
                    ->update($db->quoteName('#__extensions'))
                    ->set($db->quoteName('enabled') . ' = 1')
                    ->where($db->quoteName('extension_id') . ' = ' . (int)$id);
                $db->setQuery($updateQuery);
                $db->execute();

                // Remove from extensions_check table or set disabled to 0
                $checkQuery = $db->getQuery(true)
                    ->select('COUNT(*)')
                    ->from($db->quoteName('#__extensions_check'))
                    ->where($db->quoteName('extension_id') . ' = ' . (int)$id);
                $db->setQuery($checkQuery);
                $exists = $db->loadResult();

                if ($exists) {
                    // Update existing record to mark as not disabled
                    $updateCheckQuery = $db->getQuery(true)
                        ->update($db->quoteName('#__extensions_check'))
                        ->set($db->quoteName('disabled') . ' = 0')
                        ->where($db->quoteName('extension_id') . ' = ' . (int)$id);
                    $db->setQuery($updateCheckQuery);
                    $db->execute();

                    // Optionally, delete the record instead of updating
                    // $deleteQuery = $db->getQuery(true)
                    //     ->delete($db->quoteName('#__extensions_check'))
                    //     ->where($db->quoteName('extension_id') . ' = ' . (int)$id);
                    // $db->setQuery($deleteQuery);
                    // $db->execute();
                }
            }

            return true;
        } catch (\Exception $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Return compatibility plugin status for current Joomla major version.
     *
     * @return array{element:string,enabled:bool,exists:bool,isJoomla6:bool}
     */
    public function getCompatibilityPluginStatus()
    {
        $element = $this->isJoomla6OrLater() ? 'compat6' : 'compat';
        $plugin = $this->getBehaviourPluginByElement($element);

        return [
            'element' => $element,
            'enabled' => !empty($plugin->enabled),
            'exists' => !empty($plugin),
            'isJoomla6' => $this->isJoomla6OrLater(),
        ];
    }

    /**
     * Re-enable all previously disabled extensions.
     * On Joomla 6 this also enforces enabling compat6 + boolean-like options.
     *
     * @return bool True on success
     */
    public function republishAllDisabledExtensions()
    {
        $disabledIds = $this->getDisabledExtensions();

        if (!empty($disabledIds) && !$this->republishExtensions($disabledIds)) {
            return false;
        }

        if ($this->isJoomla6OrLater() && !$this->enableCompat6WithOptions()) {
            return false;
        }

        if (empty($disabledIds)) {
            Factory::getApplication()->enqueueMessage(Text::_('COM_J2MIGRATIONCHECKER_NO_DISABLED_EXTENSIONS'), 'info');
        }

        return true;
    }

    /**
     * Disable all extensions that are tracked in extensions_check table
     *
     * @return bool True on success
     */
    public function disableAllTrackedExtensions()
    {
        $db = $this->getDatabase();

        try {
            // Get all tracked extension IDs
            $trackedIds = $this->getTrackedExtensionIds();

            if (empty($trackedIds)) {
                Factory::getApplication()->enqueueMessage(Text::_('COM_J2MIGRATIONCHECKER_NO_TRACKED_EXTENSIONS'), 'info');
                return true;
            }

            // Disable all tracked extensions
            foreach ($trackedIds as $id) {
                $updateQuery = $db->getQuery(true)
                    ->update($db->quoteName('#__extensions'))
                    ->set($db->quoteName('enabled') . ' = 0')
                    ->where($db->quoteName('extension_id') . ' = ' . (int)$id);
                $db->setQuery($updateQuery);
                $db->execute();

                // Mark as disabled in extensions_check table
                $checkQuery = $db->getQuery(true)
                    ->select('COUNT(*)')
                    ->from($db->quoteName('#__extensions_check'))
                    ->where($db->quoteName('extension_id') . ' = ' . (int)$id);
                $db->setQuery($checkQuery);
                $exists = $db->loadResult();

                if ($exists) {
                    $updateCheckQuery = $db->getQuery(true)
                        ->update($db->quoteName('#__extensions_check'))
                        ->set($db->quoteName('disabled') . ' = 1')
                        ->where($db->quoteName('extension_id') . ' = ' . (int)$id);
                    $db->setQuery($updateCheckQuery);
                    $db->execute();
                }
            }

            Factory::getApplication()->enqueueMessage(
                Text::sprintf('COM_J2MIGRATIONCHECKER_ALL_EXTENSIONS_DISABLED', count($trackedIds)),
                'success'
            );

            return true;
        } catch (\Exception $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Check Joomla major version.
     *
     * @return bool
     */
    private function isJoomla6OrLater()
    {
        return version_compare(JVERSION, '6.0.0', '>=');
    }

    /**
     * Load a behaviour plugin row by element.
     *
     * @param string $element
     * @return object|null
     */
    private function getBehaviourPluginByElement($element)
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
            ->where($db->quoteName('folder') . ' = ' . $db->quote('behaviour'))
            ->where($db->quoteName('element') . ' = ' . $db->quote($element));
        $db->setQuery($query, 0, 1);

        try {
            $plugin = $db->loadObject();
        } catch (\Exception $e) {
            return null;
        }

        return $plugin ?: null;
    }

    /**
     * Enable compat6 plugin and set boolean-like options to enabled values.
     *
     * @return bool
     */
    private function enableCompat6WithOptions()
    {
        $plugin = $this->getBehaviourPluginByElement('compat6');

        if (!$plugin) {
            return true;
        }

        $params = [];
        if (!empty($plugin->params)) {
            $decoded = json_decode($plugin->params, true);
            if (is_array($decoded)) {
                $params = $decoded;
            }
        }

        $updatedParams = $this->enableBooleanLikeParams($params);
        $paramsJson = json_encode($updatedParams);
        if ($paramsJson === false) {
            $paramsJson = '{}';
        }

        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('enabled') . ' = 1')
            ->set($db->quoteName('params') . ' = ' . $db->quote($paramsJson))
            ->where($db->quoteName('extension_id') . ' = ' . (int) $plugin->extension_id);
        $db->setQuery($query);

        try {
            $db->execute();
            return true;
        } catch (\Exception $e) {
            Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Recursively switch boolean-like option values to enabled.
     *
     * @param mixed $value
     * @return mixed
     */
    private function enableBooleanLikeParams($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $child) {
                $value[$key] = $this->enableBooleanLikeParams($child);
            }

            return $value;
        }

        if ($value === false || $value === 0 || $value === '0' || $value === 'false') {
            return '1';
        }

        return $value;
    }
}
