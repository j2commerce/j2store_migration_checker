<?php
/**
 * @copyright Copyright (C) 2024-26 J2Commerce, Inc. All rights reserved.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or later
 * @website https://www.j2commerce.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$compatStatus = isset($this->compatibility_plugin_status) ? $this->compatibility_plugin_status : [];
$compatElement = !empty($compatStatus['element']) ? $compatStatus['element'] : 'compat';
$compatEnabled = !empty($compatStatus['enabled']);
$compatExists = !empty($compatStatus['exists']);
$compatClass = $compatEnabled ? 'alert-success' : 'alert-warning';
$compatLabel = $compatEnabled ? Text::_('JENABLED') : Text::_('JDISABLED');
?>
<div class="well">

    <?php if (!$compatStatus['isJoomla6']) : ?>
        <div class="alert alert-info" role="alert">
            <?php echo Text::_('COM_J2MIGRATIONCHECKER_JOOMLA5_INFO'); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($compatStatus['isJoomla6']) && !$compatEnabled) : ?>
        <div class="alert alert-warning" role="alert">
            <?php echo Text::_('COM_J2MIGRATIONCHECKER_COMPAT6_ENABLE_ALL_NOTE'); ?>
        </div>
    <?php endif; ?>

    <nav class="quick-icons p-3 mb-4">
        <ul class="nav flex-wrap">
            <?php
                $iconClass = $compatEnabled ? 'icon-check text-success' : 'icon-times text-danger';
                $badgeClass = $compatEnabled ? 'success' : 'danger';
                $statusText = $compatEnabled ? Text::_('JENABLED') : Text::_('JDISABLED');
                $not_found = !$compatExists ? Text::_('COM_J2MIGRATIONCHECKER_COMPAT_MISSING_SHORT') : '';

                if ($compatExists) {
                    echo HTMLHelper::_('icons.button', [
                        'name'  => Text::sprintf('COM_J2MIGRATIONCHECKER_COMPAT_PLUGIN_STATUS', $statusText),
                        'image' => $iconClass,
                        'link'  => '#',
                        'class' => $badgeClass
                    ]);
                } else {
                    echo HTMLHelper::_('icons.button', [
                        'name'  => Text::sprintf('COM_J2MIGRATIONCHECKER_COMPAT_PLUGIN_STATUS', $not_found),
                        'image' => 'icon-warning text-warning',
                        'link'  => '#',
                        'class' => 'danger'
                    ]);
                }

                if (!empty($this->disabled_extensions_count) || !empty($this->reenabled_extensions_count)) {
                    // Disabled Extensions Count Icon
                    if (!empty($this->disabled_extensions_count)) {
                        echo HTMLHelper::_('icons.button', [
                            'name'  =>  Text::sprintf('COM_J2MIGRATIONCHECKER_TOTAL_TO_REENABLE', $this->disabled_extensions_count),
                            'image' => 'icon-lock text-danger',
                            'amount'  => (string)$this->disabled_extensions_count,
                            'link'  => '#',
                            'class' => 'danger'
                        ]);
                    }

                    // Re-enabled Extensions Count Icon
                    if (!empty($this->reenabled_extensions_count)) {
                        echo HTMLHelper::_('icons.button', [
                            'name'  => Text::sprintf('COM_J2MIGRATIONCHECKER_TOTAL_REENABLED', $this->reenabled_extensions_count),
                            'image' => 'icon-unlock text-success',
                            'amount'  => (string)$this->reenabled_extensions_count,
                            'link'  => '#',
                            'class' => 'success'
                        ]);
                    }

                    // Show success message if all re-enabled
                    /*if ($this->disabled_extensions_count === $this->reenabled_extensions_count && !empty($this->disabled_extensions_count)) {
                        echo HTMLHelper::_('icons.button', [
                            'name'  => Text::_('COM_J2MIGRATIONCHECKER_COMPLETE'),
                            'image' => 'icon-check-circle text-success',
                            'text'  => Text::_('JYES'),
                            'link'  => '#',
                            'title' => Text::_('COM_J2MIGRATIONCHECKER_COUNTS_MATCH'),
                            'class' => 'extensions-count-icon migration-complete'
                        ]);
                    }*/
                }
            ?>
        </ul>
    </nav>

    <form action="<?php echo Route::_('index.php?option=com_j2migrationchecker&view=cpanel'); ?>" method="post" id="adminForm" name="adminForm">
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo HTMLHelper::_('form.token'); ?>

        <?php if (version_compare(JVERSION, '6.0.0', '<')) : ?>
            <div class="form-check d-flex align-items-center mb-1">
                <?php echo HTMLHelper::_('grid.checkall'); ?>
                <label class="form-check-label ms-2" for="checkall-toggle">
                    <?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>
                </label>
            </div>
            <div class="small text-muted mb-3">
                <?php echo Text::_('COM_J2MIGRATIONCHECKER_CHECK_ALL_HINT'); ?>
            </div>
        <?php endif; ?>

        <?php include 'components.php'; ?>
        <?php include 'modules.php'; ?>
        <?php include 'plugins.php'; ?>
    </form>
</div>
