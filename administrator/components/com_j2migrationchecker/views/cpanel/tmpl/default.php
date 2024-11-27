<?php
/**
 * @copyright Copyright (C) 2014-2019 Weblogicx India. All rights reserved.
 * @copyright Copyright (C) 2024 J2Commerce, Inc. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$alert_class = $this->components_status !== 'Not Ready' && $this->modules_status !== 'Not Ready' && $this->plugins_status !== 'Not Ready' && $this->templates_status !== 'Not Ready' ? 'alert-success' : 'alert-danger';
?>
<style>
    input[disabled] {
        background-color: #46a546 !important;
    }
</style>
<div class="well">
    <form action="<?php echo Route::_('index.php'); ?>" method="post" id="adminForm" name="adminForm">
        <input type="hidden" name="option" value="com_j2migrationchecker" />
        <input type="hidden" name="view" value="cpanel" />
        <input type="hidden" name="task" id="task" value="" />
        <?php echo HTMLHelper::_('form.token'); ?>

        <div class="alert <?php echo $alert_class; ?> center">
            <?php if ($this->install_status) : ?>
                <h4 class="alert-heading"><?php echo Text::_('COM_EXTENSIONCHECK_INSTALLATION_STATUS'); ?></h4><br>
                <a href="<?php echo Route::_('index.php?option=com_installer&view=update&filter_search=j2store'); ?>" class="btn btn-large btn-info"><?php echo Text::_('COM_EXTENSIONCHECK_INSTALLATION_INSTALL'); ?></a>
            <?php else: ?>
                <h4 class="alert-heading"><?php echo Text::_('COM_EXTENSIONCHECK_DEFAULT_INSTALLATION_STATUS'); ?></h4>
            <?php endif; ?>
        </div>
        <br>
        <?php include 'components.php';?>
        <br>
        <?php include 'plugins.php';?>
        <br>
        <?php include 'modules.php';?>
        <br>
        <?php include 'templateoverride.php';?>
    </form>
</div>
