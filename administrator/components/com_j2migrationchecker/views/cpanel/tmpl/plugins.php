<?php
/**
 * @copyright Copyright (C) 2014-2019 Weblogicx India. All rights reserved.
 * @copyright Copyright (C) 2024 J2Commerce, Inc. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */
// No direct access to this file
defined('_JEXEC') or die;

$row_class = 'row';
$col_class = 'co-md-';
if (version_compare(JVERSION, '3.99.99', 'lt')) {
  $row_class = 'row-fluid';
  $col_class = 'span';
}
?>
<?php if (!empty($this->list_plugins)) : ?>
  <div class="<?php echo $row_class ?>">
    <div class="<?php echo $col_class ?>8">
        <h3><?php echo JText::_('COM_EXTENSIONCHECK_J2STORE_PLUGINS');?></h3>
    </div>
    <div class="<?php echo $col_class ?>1">
        <h3>Status :</h3>
    </div>
    <div class="<?php echo $col_class ?>3">
        <?php $alert_class =  ($this->plugins_status == 'Ready to install' )? 'alert-success' : 'alert-danger' ; ?>
        <div class="alert <?php echo $alert_class; ?> center">
            <h4 class="alert-heading"><?php echo $this->plugins_status; ?></h4>
        </div>
    </div>
</div>
<br>
<table class="table table-striped table-hover" id="plugin">
    <thead>
    <tr>
        <th >
            <?php //echo JHtml::_('grid.checkall','plugin-checkbox-table2'); ?>
        </th>
        <th >
            <?php echo JText::_('COM_EXTENSIONCHECK_PLUGIN_NAME') ;?>
        </th>
        <th >
            <?php echo JText::_('COM_EXTENSIONCHECK_PLUGIN_TYPE'); ?>
        </th>
        <th >
            <?php echo JText::_('COM_EXTENSIONCHECK_PLUGIN_ID'); ?>
        </th>

        <th >
            <?php echo JText::_('COM_EXTENSIONCHECK_ENABLED'); ?>
        </th>

    </tr>
    </thead>
    <tbody>
        <?php foreach ($this->list_plugins as $i => $row) :
            ?>
            <tr>
                <td>
                    <?php echo JHtml::_('grid.id', $i, $row->extension_id); ?>
                </td>
                <td>
                    <?php echo $row->name; ?>
                </td>
                <td align="center">
                    <?php echo $row->type; ?>
                </td>
                <td align="center">
                    <?php echo $row->extension_id; ?>
                </td>
                <td align="center">
                    <?php $btn_class =  isset($row->enabled) && !empty($row->enabled ) ? 'btn-danger ' : 'btn-success '; ?>
                    <?php $unpublish_label =  isset($row->enabled) && !empty($row->enabled ) ? 'DO_UNPUBLISH' : 'UNPUBLISHED'; ?>
                    <?php $icon_class =  isset($row->enabled) && !empty($row->enabled ) ? 'icon-unpublish' : 'fa fa-thumbs-up'; ?>
                    <?php $disabled_link =  isset($row->enabled) && !empty($row->enabled ) ? '' : 'pointer-events: none'; ?>
                    <div class="btn-toolbar">
                        <div class="btn-wrapper">
                            <a style="<?php echo $disabled_link; ?>" href="<?php echo JRoute::_( "index.php?option=com_j2migrationchecker&view=cpanel&cid= $row->extension_id&task=customunpublish",false) ?>" >
                            <span class="btn btn-sm btn-small <?php echo $btn_class; ?>" id="">
                                <i class="<?php echo $icon_class; ?>"></i>
                                    <?php echo JText::_($unpublish_label); ?>
                            </span>
                            </a>
                        </div>
                    </div>
                    <?php // echo JHtml::_('jgrid.published', $row->enabled, $i, '',false, 'cb'); ?>
                </td>

            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
