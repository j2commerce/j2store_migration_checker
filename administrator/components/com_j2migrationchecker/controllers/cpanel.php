<?php


/**
 * @package     3.x
 * @subpackage  J2 Store Easy Checkout
 * @author      Alagesan, J2Store <support@j2store.org>
 * @copyright   Copyright (c) 2018 J2Store . All rights reserved.
 * @license     GNU GPL v3 or later
 * @link        http://j2store.org
 * --------------------------------------------------------------------------------
 *
 * */
// No direct access to this file
defined('_JEXEC') or die;

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
		JToolbarHelper::title(JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLDS'));
        //JToolBarHelper::publish('testpublish');
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
        $status = JText::_('COM_EXTENSIONCHECK_DEFAULT_INSTALLATION_STATUS');
        if($components_status !== 'Not Ready' && $modules_status !== 'Not Ready' && $plugins_status !== 'Not Ready' && $template_status !== 'Not Ready' ) {
            $status = JText::_('COM_EXTENSIONCHECK_INSTALLATION_STATUS');
        }
        return $status;
    }

    public function renameFolder(){
        $app = JFactory::getApplication();
        $data = $app->input->getArray($_POST);
        $link = JRoute::_('index.php?option=com_j2migrationchecker&view=cpanel',false);
        if(isset($data['folder_Path']) && !empty($data['folder_Path'])) {
            $newFolderPath = str_replace('com_j2store', 'old_com_j2store', $data['folder_Path']);
            if (rename($data['folder_Path'], $newFolderPath)) {
               // $this->setMessage("Folder renamed successfully.");
                $this->setRedirect($link,JText::_("COM_EXTENSIONCHECK_RENAMED_SUCCESSFULLY"));
            } else {
               // $this->setMessage("Error renaming the folder.");
                $this->setRedirect($link,JText::_("COM_EXTENSIONCHECK_RENAMED_FAILED"));
            }
        }

    }
    public function customunpublish()
    {

        // Check for request forgeries.
        //$this->checkToken();
        $app = JFactory::getApplication();
        $link = JRoute::_('index.php?option=com_j2migrationchecker&view=cpanel',false);
        $ids = $this->input->get('cid', array(), 'array');
        foreach ($ids as $id) {

            $db = JFactory::getDbo();
            $updateQuery = $db->getQuery(true)
                ->update($db->quoteName('#__extensions'))
                ->set($db->quoteName('enabled') . ' = 0')
                ->where($db->quoteName('extension_id') . ' = ' . (int)$id);
            $db->setQuery($updateQuery);
            $db->execute();
        }
        $app->redirect($link);
    }

}
