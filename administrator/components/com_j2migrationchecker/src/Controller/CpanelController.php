<?php
/**
 * @copyright Copyright (C) 2024-26 J2Commerce, Inc. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */

namespace J2Commerce\Component\J2migrationchecker\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

class CpanelController extends BaseController
{
    public function unpublish()
    {
        // Check for request forgeries.
        //$this->checkToken();
        $app = Factory::getApplication();
        $link = Route::_('index.php?option=com_j2migrationchecker&view=cpanel',false);
        $ids = $this->input->get('cid', [], 'array');

        // Get the model and use its unpublishExtensions method
        $model = $this->getModel('Cpanel');

        if ($model->unpublishExtensions($ids)) {
            $app->enqueueMessage(Text::_('COM_J2MIGRATIONCHECKER_EXTENSIONS_UNPUBLISHED_SUCCESS'), 'success');
        } else {
            $app->enqueueMessage(Text::_('COM_J2MIGRATIONCHECKER_EXTENSIONS_UNPUBLISHED_FAILED'), 'error');
        }

        $app->redirect($link);
    }

    public function publish()
    {
        // Check for request forgeries.
        //$this->checkToken();
        $app = Factory::getApplication();
        $link = Route::_('index.php?option=com_j2migrationchecker&view=cpanel',false);
        $ids = $this->input->get('cid', [], 'array');

        // Get the model and use its republishExtensions method
        $model = $this->getModel('Cpanel');

        if ($model->republishExtensions($ids)) {
            $app->enqueueMessage(Text::_('COM_J2MIGRATIONCHECKER_EXTENSIONS_REPUBLISHED_SUCCESS'), 'success');
        } else {
            $app->enqueueMessage(Text::_('COM_J2MIGRATIONCHECKER_EXTENSIONS_REPUBLISHED_FAILED'), 'error');
        }

        $app->redirect($link);
    }

    public function publishall()
    {
        // Check for request forgeries.
        //$this->checkToken();
        $app = Factory::getApplication();
        $link = Route::_('index.php?option=com_j2migrationchecker&view=cpanel',false);

        // Get the model and re-enable all disabled extensions
        $model = $this->getModel('Cpanel');

        if ($model->republishAllDisabledExtensions()) {
            $app->enqueueMessage(Text::_('COM_J2MIGRATIONCHECKER_ALL_EXTENSIONS_REPUBLISHED_SUCCESS'), 'success');
        } else {
            $app->enqueueMessage(Text::_('COM_J2MIGRATIONCHECKER_ALL_EXTENSIONS_REPUBLISHED_FAILED'), 'error');
        }

        $app->redirect($link);
    }

    public function unpublishall()
    {
        // Check for request forgeries.
        //$this->checkToken();
        $app = Factory::getApplication();
        $link = Route::_('index.php?option=com_j2migrationchecker&view=cpanel',false);

        // Get the model and disable all tracked extensions
        $model = $this->getModel('Cpanel');

        if ($model->disableAllTrackedExtensions()) {
            $app->enqueueMessage(Text::_('COM_J2MIGRATIONCHECKER_ALL_EXTENSIONS_DISABLED_SUCCESS'), 'success');
        } else {
            $app->enqueueMessage(Text::_('COM_J2MIGRATIONCHECKER_ALL_EXTENSIONS_DISABLED_FAILED'), 'error');
        }

        $app->redirect($link);
    }
}
