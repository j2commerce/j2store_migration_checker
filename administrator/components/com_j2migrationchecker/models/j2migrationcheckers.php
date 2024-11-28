<?php
/**
 * @copyright Copyright (C) 2014-2019 Weblogicx India. All rights reserved.
 * @copyright Copyright (C) 2024 J2Commerce, Inc. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorld Model
 *
 * @since  0.0.1
 */
class J2MigrationCheckerModelJ2MigrationCheckers extends F0FModel
{
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	 */

    protected function getSelectQuery(&$query)
    {
        $query->select("*")
            ->from("#__extensions");
    }


    public function getListComponents(){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $this->getSelectQuery($query);
        $query->where("element='com_easycheckout'");
        $db->setQuery($query);
        return $db->loadObjectList();

    }
    public function getListPlugins()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $this->getSelectQuery($query);
        $query->where("type='plugin'");
        $query->where("folder='j2store' or element='easycheckout'");
        $db->setQuery($query);

        $items = $db->loadObjectList();

        $core_plugins = array();
        $core_plugins[] = 'app_bootstrap3'; // in v4.0.5
        $core_plugins[] = 'app_bootstrap4'; // in v4.0.5
        $core_plugins[] = 'app_bootstrap5'; // in v4.0.5
        $core_plugins[] = 'app_currencyupdater';
        $core_plugins[] = 'app_diagnostics';
        $core_plugins[] = 'app_flexivariable';
        $core_plugins[] = 'app_localization_data';
        $core_plugins[] = 'app_schemaproducts';
        $core_plugins[] = 'payment_banktransfer';
        $core_plugins[] = 'payment_cash';
        $core_plugins[] = 'payment_moneyorder';
        $core_plugins[] = 'payment_paypal';
        $core_plugins[] = 'payment_sagepayform';
        $core_plugins[] = 'report_itemised';
        $core_plugins[] = 'report_products';
        $core_plugins[] = 'shipping_free';
        $core_plugins[] = 'shipping_standard';

        foreach ($items as $item) {
            $item->core = 0;
            if (in_array($item->element, $core_plugins)) {
                $item->core = 1;
            }
        }

        return $items;
    }

    public function getListModules(){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $this->getSelectQuery($query);
        $query->where("type='module'");
        $db->setQuery($query);
        $data =   $db->loadObjectList();

        // why this list? 2 are core J2Store (cart and menu)
        $j2store_module = array();
        $j2store_module[] = 'mod_j2store_related_products';
        $j2store_module[] = 'mod_j2store_search';
        $j2store_module[] = 'mod_j2store_categories';
        $j2store_module[] = 'mod_j2products';
        $j2store_module[] = 'mod_j2store_cart';
        $j2store_module[] = 'mod_j2store_menu';

        $result = [];
        foreach($data as $key => $value) {
             if(isset($value->element) && !empty($value->element)){
                if(in_array($value->element,$j2store_module)){
                    $result[] = $value;
                }
             }
        }

        return $result;
    }

    public function getTemplate(){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select("*")->from("#__template_styles");
        $db->setQuery($query);
        $result =   $db->loadObjectList();

        return $result;
    }

    public function componentsStatus(){
       $components_status =  $this->getListComponents();
        $status = '';
       if(is_array($components_status) && !empty($components_status) ){
        foreach ( $components_status as $key => $value) {
            if (empty($value->enabled)) {
                $status = 'Ready to install';
            } else {
                $status = 'Not Ready';
                break;
            }
        }
       }
       return $status;
    }
    public function pluginsStatus(){
        $plugins_status =  $this->getListPlugins();
        $status = '';
        if(is_array($plugins_status) && !empty($plugins_status) ) {
            foreach ($plugins_status as $key => $value) {
                if (empty($value->enabled)) {
                    $status = 'Ready to install';
                } else {
                    $status = 'Not Ready';
                    break;
                }
            }
        }
        return $status;
    }
    public function modulesStatus(){
        $module_status =  $this->getListModules();
        $status = '';
        if(is_array($module_status) && !empty($module_status) ) {
            foreach ($module_status as $key => $value) {
                if (empty($value->enabled)) {
                    $status = 'Ready to install';
                } else {
                    $status = 'Not Ready';
                    break;
                }
            }
        }
        return $status;
    }
    public function getTemplateOverride(){
        $template_override = $this->getTemplate();
        $template_overridePath = [];
        foreach ($template_override as $key => $value){

            if(empty($value->client_id) ) {
                $templatePath = JPATH_SITE . '/templates/' . $value->template;
            }elseif($value->client_id == 1){
                $templatePath = JPATH_ADMINISTRATOR . '/templates/' . $value->template;
            }
            $component = 'com_j2store';
            $overridePath = $templatePath . '/html/' . $component ;
            if (file_exists($overridePath)) {
                $template_overridePath[] = $overridePath;
            }
        }
        return $template_overridePath;
    }
    public function templateStatus(){
        $template_status =  $this->getTemplateOverride();
        $status = '';
        if (empty($template_status)) {
            $status = 'Ready to install';
        } else {
            $status = 'Not Ready';
        }
        return $status;
    }
    public function saveData()
    {
        $columns = array('component_status', 'plugins_status','modules_status','template_status','installation_status');
        $modules_status =  $this->modulesStatus();
        $plugins_status = $this->pluginsStatus();
        $components_status = $this->componentsStatus();
        $template_status = $this->templateStatus();
        $installation_status = 0;
        if($components_status !== 'Not Ready' && $modules_status !== 'Not Ready' && $plugins_status !== 'Not Ready' && $template_status !== 'Not Ready' ) {
            $installation_status = 1;
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*')->from('#__extension_check');
        $db->setQuery($query);
        $result = $db->loadObjectList();

        if( is_array($result) && count($result)> 0 ){
            foreach ($result as $key => $value ) {
                $query->clear();
                $query->update($db->qn('#__extension_check'));
                $query->set($db->qn('component_status') . ' = ' . $db->q($components_status));
                $query->set($db->qn('plugins_status') . ' = ' . $db->q($plugins_status));
                $query->set($db->qn('modules_status') . ' = ' . $db->q($modules_status));
                $query->set($db->qn('template_status') . ' = ' . $db->q($template_status));
                $query->set($db->qn('installation_status') . ' = ' . $db->q($installation_status));
                $query->where($db->qn('extension_check_id') . ' = ' . (int)$value->extension_check_id);
                $db->setQuery($query);
                $db->execute();
            }
        } else {
            $query->clear();
            $query->insert($db->qn('#__extension_check'))
                ->columns($columns)
                ->values($db->q($components_status) . ', ' . $db->q($plugins_status) . ',' . $db->q($modules_status). ',' . $db->q($template_status). ',' . $db->q($installation_status));
            $db->setQuery($query);
            $db->execute();
        }
    }
}
