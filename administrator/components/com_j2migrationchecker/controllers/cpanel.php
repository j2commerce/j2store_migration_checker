<?php
/**
 * @copyright Copyright (C) 2014-2019 Weblogicx India. All rights reserved.
 * @copyright Copyright (C) 2024 J2Commerce, Inc. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

class J2MigrationCheckerControllerCpanel extends F0FController
{
    public function execute($task)
    {
        if (!in_array($task, array('browse','renameFolder','customunpublish'))) {
            $task = 'browse';
        }
        return parent::execute($task);
    }

    public function browse()
    {
        JToolBarHelper::unpublish('customunpublish');
        F0FModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_j2migrationchecker/models');
        $model = F0FModel::getTmpInstance('J2MigrationCheckers', 'J2MigrationCheckerModel');
        $list_components = $model->getListComponents();
        $list_plugins = $model->getListPlugins();
        $list_modules = $model->getListModules();
        $templates = $model->getTemplate();
        $components_status = $model->componentsStatus();
        $modules_status = $model->modulesStatus();
        $plugins_status = $model->pluginsStatus();
        $template_status = $model->templateStatus();
        $model->saveData();
        $install_status = $this->installStatus();
        $templates_override = $model->getTemplateOverride();
        $renamed_template_override = $this->getRenamedTemaplateOverride();
        $pagination = '';
        $view   = $this->getThisView('Cpanel');
        $view->set('renamed_template_override',$renamed_template_override);
        $view->set('install_status',$install_status);
        $view->set('components_status',$components_status);
        $view->set('modules_status',$modules_status);
        $view->set('plugins_status',$plugins_status);
        $view->set('templates_status',$template_status);
        $view->set('list_modules',$list_modules);
        $view->set('list_components',$list_components);
        $view->set('list_plugins',$list_plugins);
        $view->set('pagination',$pagination);
        $view->set('template_override',$templates_override);
        $view->setModel( $model, true );
        $view->setLayout( 'default' );
        $view->display();
    }

    public function getRenamedTemaplateOverride(){
        F0FModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_j2migrationchecker/models');
        $model = F0FModel::getTmpInstance('J2MigrationCheckers', 'J2MigrationCheckerModel');
        $template_override = $model->getTemplate();
        $template_overridePath = [];
        foreach ($template_override as $key => $value){
            if(empty($value->client_id) ) {
                $templatePath = JPATH_SITE . '/templates/' . $value->template;
            }elseif($value->client_id == 1){
                $templatePath = JPATH_ADMINISTRATOR . '/templates/' . $value->template;
            }
            $component = 'old_com_j2store';
            $overridePath = $templatePath . '/html/' . $component ;
            if (file_exists($overridePath)) {
                $template_overridePath[] = $overridePath;
            }
        }
        return $template_overridePath;
    }
    public function installStatus()
    {
        F0FModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_j2migrationchecker/models');
        $model = F0FModel::getTmpInstance('J2MigrationCheckers', 'J2MigrationCheckerModel');
        $components_status = $model->componentsStatus();
        $modules_status = $model->modulesStatus();
        $plugins_status = $model->pluginsStatus();
        $template_status = $model->templateStatus();
        $status = false;
        if($components_status !== 'Not Ready' && $modules_status !== 'Not Ready' && $plugins_status !== 'Not Ready' && $template_status !== 'Not Ready' ) {
            $status = true;

            // set the update server site to look for J2Store 4
            $this->_updateSite('J2Store Professional', 'component', 'com_j2store', '', 'https://updates.j2commerce.com/j2store/j2store4.xml');
        }
        return $status;
    }

    public function renameFolder(){
        $app = Factory::getApplication();
        $data = $app->input->getArray($_POST);
        $link = Route::_('index.php?option=com_j2migrationchecker&view=cpanel',false);
        if(isset($data['folder_Path']) && !empty($data['folder_Path'])) {
            $newFolderPath = str_replace('com_j2store', 'old_com_j2store', $data['folder_Path']);
            if (rename($data['folder_Path'], $newFolderPath)) {
               // $this->setMessage("Folder renamed successfully.");
                $this->setRedirect($link, Text::_("COM_EXTENSIONCHECK_RENAMED_SUCCESSFULLY"));
            } else {
               // $this->setMessage("Error renaming the folder.");
                $this->setRedirect($link, Text::_("COM_EXTENSIONCHECK_RENAMED_FAILED"));
            }
        }

    }

    public function customunpublish()
    {
        // Check for request forgeries.
        //$this->checkToken();
        $app = Factory::getApplication();
        $link = Route::_('index.php?option=com_j2migrationchecker&view=cpanel',false);
        $ids = $this->input->get('cid', array(), 'array');
        foreach ($ids as $id) {

            $db = Factory::getDbo();
            $updateQuery = $db->getQuery(true)
                ->update($db->quoteName('#__extensions'))
                ->set($db->quoteName('enabled') . ' = 0')
                ->where($db->quoteName('extension_id') . ' = ' . (int)$id);
            $db->setQuery($updateQuery);
            $db->execute();
        }
        $app->redirect($link);
    }

  private function _updateSite($name, $type, $element, $folder = '', $location = '')
  {
    $db = Factory::getDBO();

    $query = $db->getQuery(true);

    $query->select('extension_id');
    $query->from('#__extensions');
    $query->where($db->quoteName('type').'='.$db->quote($type));
    $query->where($db->quoteName('element').'='.$db->quote($element));
    if ($folder) {
      $query->where($db->quoteName('folder').'='.$db->quote($folder));
    }

    $db->setQuery($query);

    $extension_id = '';
    try {
      $extension_id = $db->loadResult();
    } catch (\RuntimeException $e) {
      Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
    }

    if ($extension_id) {

      // Prevent the deletion/creation if done already
      $query->clear();

      $query->select('update_site_id');
      $query->from('#__update_sites');
      $query->where($db->quoteName('location').'='.$db->quote($location));

      $db->setQuery($query);

      $updatesite_id = '';
      try {
        $updatesite_id = $db->loadResult();
      } catch (\RuntimeException $e) {
        Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
      }

      if ($updatesite_id) {

        // Get the installer model
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_installer/models');
        $model = JModelLegacy::getInstance('Update', 'InstallerModel', array('ignore_request' => true));

        // Trigger the update check
        $model->findUpdates($extension_id);

        return true;
      }

      $query->clear();

      $query->select('update_site_id');
      $query->from('#__update_sites_extensions');
      $query->where($db->quoteName('extension_id').'='.$extension_id);

      $db->setQuery($query);

      $updatesite_id = array(); // can have several results
      try {
        $updatesite_id = $db->loadColumn();
      } catch (\RuntimeException $e) {
        Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
      }

      if (!empty($updatesite_id)) {
        // Delete all occurrences
        $query->clear();

        $query->delete($db->quoteName('#__update_sites'));
        $query->where($db->quoteName('update_site_id').' IN ('.implode(',', $updatesite_id).')');

        $db->setQuery($query);

        try {
          $db->execute();
        } catch (\RuntimeException $e) {
          Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
        }

        $query->clear();

        $query->delete($db->quoteName('#__update_sites_extensions'));
        $query->where($db->quoteName('extension_id').' = '. $extension_id);

        $db->setQuery($query);

        try {
          $db->execute();
        } catch (\RuntimeException $e) {
          Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
        }
      }

      // Create the update site
      $query->clear();

      $query->insert($db->quoteName('#__update_sites'));
      $query->columns($db->quoteName(array('name', 'type', 'location', 'enabled')));
      $query->values(implode(',', array($db->quote($name), $db->quote('extension'), $db->quote($location), 1)));

      $db->setQuery($query);

      try {
        $db->execute();
      } catch (\RuntimeException $e) {
        Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
      }

      $query->clear();

      $query->select('update_site_id');
      $query->from('#__update_sites');
      $query->where($db->quoteName('location').'='.$db->quote($location));

      $db->setQuery($query);

      $updatesite_id = '';
      try {
        $updatesite_id = $db->loadResult();
      } catch (\RuntimeException $e) {
        Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
      }

      if ($updatesite_id) {
        $query->clear();

        $query->insert($db->quoteName('#__update_sites_extensions'));
        $query->columns($db->quoteName(array('update_site_id', 'extension_id')));
        $query->values(implode(',', array($updatesite_id, $extension_id)));

        $db->setQuery($query);

        try {
          $db->execute();
        } catch (\RuntimeException $e) {
          Factory::getApplication()->enqueueMessage(Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), 'error');
        }
      }

      // Get the installer model
      JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_installer/models');
      $model = JModelLegacy::getInstance('Update', 'InstallerModel', array('ignore_request' => true));

      // Trigger the update check
      $model->findUpdates($extension_id);
    } else {
      return false;
    }

    return true;
  }
}
