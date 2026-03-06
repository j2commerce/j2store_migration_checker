<?php
/**
 * @copyright Copyright (C) 2024-26 J2Commerce, Inc. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */

namespace J2Commerce\Component\J2migrationchecker\Administrator\View\Cpanel;

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Control Panel View
 */
class HtmlView extends BaseHtmlView
{
    /**
     * List of components
     * @var array
     */
    protected $list_components;

    /**
     * List of plugins
     * @var array
     */
    protected $list_plugins;

    /**
     * List of modules
     * @var array
     */
    protected $list_modules;

    /**
     * List of tracked extension IDs in extensions_check table
     * @var array
     */
    protected $tracked_extension_ids = [];

    /**
     * Compatibility plugin status for current Joomla major version.
     * @var array
     */
    protected $compatibility_plugin_status = [];

    /**
     * Count of disabled extensions to be re-enabled
     * @var int
     */
    protected $disabled_extensions_count = 0;

    /**
     * Count of already re-enabled extensions
     * @var int
     */
    protected $reenabled_extensions_count = 0;

    /**
     * Display method of view
     *
     * @param string $tpl Template name
     * @return bool|void
     */
    function display($tpl = null)
    {
        /** @var \J2Commerce\Component\J2migrationchecker\Administrator\Model\CpanelModel $model */
        $model = $this->getModel();

        // Load data from model
        $this->list_components = $model->getListComponents();
        $this->list_plugins = $model->getListPlugins();
        $this->list_modules = $model->getListModules();

        // Get list of tracked extension IDs from model
        $this->tracked_extension_ids = $model->getTrackedExtensionIds();
        $this->compatibility_plugin_status = $model->getCompatibilityPluginStatus();

        // Get extension counts
        $this->disabled_extensions_count = $model->getDisabledExtensionsCount();
        $this->reenabled_extensions_count = $model->getReenabledExtensionsCount();

        // Show safe-to-remove message on Joomla 6 if all extensions are enabled
        if (version_compare(JVERSION, '6.0.0', '>=')) {
            if ($this->disabled_extensions_count === 0 && !empty($this->tracked_extension_ids)) {
                Factory::getApplication()->enqueueMessage(
                    Text::_('COM_J2MIGRATIONCHECKER_SAFE_TO_REMOVE'),
                    'success'
                );
            }
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Setting the toolbar
     */
    protected function addToolbar()
    {
        ToolbarHelper::title(Text::_('COM_J2MIGRATIONCHECKER'), 'puzzle-piece');

        if (version_compare(JVERSION, '6.0.0', '<')) {
            // Add toolbar buttons for bulk actions
            ToolbarHelper::publish('cpanel.publish', 'JTOOLBAR_ENABLE', true);
            ToolbarHelper::unpublish('cpanel.unpublish', 'JTOOLBAR_DISABLE', true);
        }

        // Only show 'Enable All Disabled' button on Joomla 6+
        if (version_compare(JVERSION, '6.0.0', '>=')) {
            ToolbarHelper::custom('cpanel.publishall', 'refresh', '', 'COM_J2MIGRATIONCHECKER_ENABLE_ALL', false, false);
            //ToolbarHelper::custom('cpanel.unpublishall', 'refresh', '', 'COM_J2MIGRATIONCHECKER_DISABLE_ALL', false, false);
        }

        ToolbarHelper::help(null, false, 'https://www.j2commerce.com/update-j2commerce-4-to-joomla-6');
    }
}
