
<?php
$row_class = 'row';
$col_class = 'co-md-';
if (version_compare(JVERSION, '3.99.99', 'lt')) {
  $row_class = 'row-fluid';
  $col_class = 'span';
}
?>
<div class="<?php echo $row_class; ?>">
  <div class="<?php echo $col_class; ?>8">
        <h3><?php echo JText::_('COM_EXTENSIONCHECK_J2STORE_TEMPLATE_OVERRIDES');?></h3>
    </div>
    <div class="<?php echo $col_class; ?>1">
        <h3>Status :</h3>
    </div>
    <div class="<?php echo $col_class; ?>3">
        <?php $alert_class =  ($this->templates_status == 'Ready to install' )? 'alert-success' : 'alert-danger' ; ?>
        <div class="alert <?php echo $alert_class; ?> center">
            <h4 class="alert-heading"><?php echo $this->templates_status; ?></h4>
        </div>
    </div>
</div>

<?php if (!empty($this->template_override)) : ?>
    <?php foreach ($this->template_override as $key => $value) : ?>
        <br>
        <div class="alert alert-info">
        <h4 class="alert-heading">Please ask to rename this template override folder</h4>
                <div style="display: flex;">
                    <div>
                    <h5><?php echo $value; ?></h5>
                    </div>
                    <div class="btn-toolbar">
                        <div class="btn-wrapper">
                            <a href="<?php echo JRoute::_( "index.php?option=com_j2migrationchecker&view=cpanel&folder_Path=$value&task=renameFolder") ?>" >
                            <span class="btn btn-sm btn-small btn-success" id="">
                               Rename
                            </span>
                            </a>
                        </div>
                    </div>
                </div>
       <?php endforeach; ?>
    </div>
 <?php endif; ?>
<?php  if (!empty($this->renamed_template_override)) : ?>

    <div class="alert alert-info">
        <h4 class="alert-heading">Renamed Template override file</h4>
     <?php foreach ($this->renamed_template_override as $key => $value) : ?>
        <div>
            <h5><?php echo $value; ?></h5>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>
