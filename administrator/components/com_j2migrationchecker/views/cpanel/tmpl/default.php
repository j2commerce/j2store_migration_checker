<?php
/**
 * @copyright Copyright (C) 2014-2019 Weblogicx India. All rights reserved.
 * @copyright Copyright (C) 2024 J2Commerce, Inc. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */
// No direct access to this file
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR.'/components/com_j2store/helpers/j2html.php';
?>
<style type="text/css">
    input[disabled] {
        background-color: #46a546 !important;
    }
</style>
<div class="well">
    <form action="<?php echo JRoute::_('index.php'); ?>" method="post" id="adminForm" name="adminForm">
        <?php echo J2Html::hidden('option','com_j2migrationchecker');?>
        <?php echo J2Html::hidden('view','cpanel');?>
        <?php echo J2Html::hidden('task','',array('id'=>'task'));?>
        <?php echo JHtml::_('form.token'); ?>
        <?php $alert_class = $this->components_status !== 'Not Ready' && $this->modules_status !== 'Not Ready' && $this->plugins_status !== 'Not Ready' && $this->templates_status !== 'Not Ready' ? 'alert-success' : 'alert-danger'  ;?>
        <div class="alert <?php echo $alert_class; ?> center">
            <h4 class="alert-heading"><?php echo $this->install_status; ?></h4>
        </div
        <br>
             <?php include 'components.php';?>
        <br>
        <?php include 'plugins.php';?>
        <br>
        <?php include 'modules.php';?>
        <br>
        <?php include 'templateoverride.php';?>

        <?php echo JHtml::_('form.token'); ?>
    </form>
</div>
