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

$row_class = 'row';
$col_class = 'co-md-';
?>
<?php if (!empty($this->list_plugins)) : ?>
    <div class="<?php echo $row_class ?>">
        <div class="<?php echo $col_class ?>12">
            <h3><?php echo Text::_('COM_J2MIGRATIONCHECKER_J2STORE_PLUGINS');?></h3>
        </div>
    </div>
    <br>
    <table class="table table-striped table-hover" id="pluginList">
        <thead>
            <tr>
                <?php if (version_compare(JVERSION, '6.0.0', '<')) : ?>
                <th width="1%" class="text-center">
                </th>
                <?php endif; ?>
                <th>
                    <?php echo Text::_('COM_J2MIGRATIONCHECKER_PLUGIN_NAME'); ?>
                </th>
                <th width="15%">
                    <?php echo Text::_('COM_J2MIGRATIONCHECKER_PLUGIN_ELEMENT'); ?>
                </th>
                <th width="10%" class="text-center">
                    <?php echo Text::_('COM_J2MIGRATIONCHECKER_CORE'); ?>
                </th>
                <th width="10%" class="text-center">
                    <?php echo Text::_('COM_J2MIGRATIONCHECKER_ENABLED'); ?>
                </th>
                <th width="10%" class="text-center">
                    <?php echo Text::_('COM_J2MIGRATIONCHECKER_PLUGIN_ID'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->list_plugins as $i => $row) : ?>
                <?php
                    // Check if extension is disabled and NOT tracked in extensions_check
                    $isDisabledAndUntracked = !$row->enabled && !in_array($row->extension_id, $this->tracked_extension_ids);
                ?>
                <tr <?php if ($isDisabledAndUntracked) echo 'style="opacity: 0.5;"'; ?>>
                    <?php if (version_compare(JVERSION, '6.0.0', '<')) : ?>
                    <td class="text-center">
                        <?php if ($isDisabledAndUntracked) : ?>
                            <input type="checkbox" disabled class="form-check-input" title="<?php echo Text::_('COM_J2MIGRATIONCHECKER_CANNOT_MODIFY_UNTRACKED'); ?>">
                        <?php else : ?>
                            <?php echo HTMLHelper::_('grid.id', $i, $row->extension_id); ?>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                    <td>
                        <strong><?php echo $this->escape($row->name); ?></strong>
                    </td>
                    <td>
                        <code><?php echo $this->escape($row->element); ?></code>
                    </td>
                    <td class="text-center">
                        <?php if ($row->core) : ?>
                            <span class="badge bg-primary">
                                <span class="icon-star" aria-hidden="true"></span>
                                <?php echo Text::_('JYES'); ?>
                            </span>
                        <?php else : ?>
                            <span class="badge bg-light text-dark">
                                <?php echo Text::_('JNO'); ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if ($row->enabled) : ?>
                            <span class="badge bg-success">
                                <span class="icon-check" aria-hidden="true"></span>
                                <?php echo Text::_('JENABLED'); ?>
                            </span>
                        <?php else : ?>
                            <span class="badge bg-secondary">
                                <span class="icon-times" aria-hidden="true"></span>
                                <?php echo Text::_('JDISABLED'); ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php echo $row->extension_id; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-end">
                    <?php
                        $totalPlugins = count($this->list_plugins);
                        $modifiablePlugins = 0;
                        foreach ($this->list_plugins as $row) {
                            $isDisabledAndUntracked = !$row->enabled && !in_array($row->extension_id, $this->tracked_extension_ids);
                            if (!$isDisabledAndUntracked) {
                                $modifiablePlugins++;
                            }
                        }
                    ?>
                    <strong><?php echo Text::sprintf('COM_J2MIGRATIONCHECKER_FOOTER_TOTAL', $totalPlugins); ?></strong>
                    | <?php echo Text::sprintf('COM_J2MIGRATIONCHECKER_FOOTER_MODIFIABLE', $modifiablePlugins); ?>
                </td>
            </tr>
        </tfoot>
    </table>
<?php endif; ?>
