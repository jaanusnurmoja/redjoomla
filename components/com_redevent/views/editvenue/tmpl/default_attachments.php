<?php
/**
 * @version 1.0 $Id$
 * @package Joomla
 * @subpackage redEVENT
 * @copyright redEVENT (C) 2008 redCOMPONENT.com / EventList (C) 2005 - 2008 Christoph Lukes
 * @license GNU/GPL, see LICENSE.php
 * redEVENT is based on EventList made by Christoph Lukes from schlu.net
 * redEVENT can be downloaded from www.redcomponent.com
 * redEVENT is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License 2
 * as published by the Free Software Foundation.

 * redEVENT is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with redEVENT; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

defined ( '_JEXEC' ) or die ( 'Restricted access' );
?>
<fieldset class="re_fldst_attachments">
<legend><?php echo JText::_('COM_REDEVENT_EVENT_ATTACHMENTS_TAB'); ?></legend>
<table class="adminform" id="re-attachments">
	<thead>
		<tr>
			<th style="width:25%"><?php echo JText::_('COM_REDEVENT_ATTACHMENT_FILE'); ?></th>
			<th style="width:15%"><?php echo JText::_('COM_REDEVENT_ATTACHMENT_NAME'); ?></th>
			<th style="width:40%"><?php echo JText::_('COM_REDEVENT_ATTACHMENT_DESCRIPTION'); ?></th>
			<th style="width:20px"><?php echo JText::_('COM_REDEVENT_ATTACHMENT_ACCESS'); ?></th>
			<th style="width:5px">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($this->row->attachments as $file): ?>
		<tr>
			<td><?php echo $file->file; ?><input type="hidden" name="attached-id[]" value="<?php echo $file->id; ?>"/></td>
			<td><input type="text" name="attached-name[]" style="width: 100%" value="<?php echo $file->name; ?>" /></td>
			<td><input type="text" name="attached-desc[]" style="width: 100%" value="<?php echo $file->description; ?>" /></td>
			<td><?php echo JHTML::_('select.genericlist', $this->access, 'attached-access[]', 'class="inputbox" size="3"', 'value', 'text', $file->access); ?></td>
			<td><?php echo JHTML::image('administrator/components/com_redevent/assets/images/no.png', JText::_('COM_REDEVENT_REMOVE_ATTACHMENT')
			                         , array('id' => 'attach-remove'.$file->id,'class' => 'attach-remove')); ?></td>
		</tr>
		<?php endforeach; ?>
		<tr>
			<td>
				<input type="file" name="attach[]" class="attach-field" size="10"></input>
			</td>
			<td>
				<input type="text" name="attach-name[]" value="" style="width: 100%" />
			</td>
			<td>
				<input type="text" name="attach-desc[]" value="" style="width: 100%" />
			</td>
			<td>
				<?php echo JHTML::_('access.assetgrouplist', 'attach-access[]', 1); ?>
			</td>
			<td>&nbsp;</td>
		</tr>
	</tbody>
</table>
</fieldset>