<?php
/**
 * @version 1.0 $Id: default.php 160 2009-05-29 16:16:39Z julien $
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

defined('_JEXEC') or die('Restricted access');
?>

<script language="javascript">
window.addEvent('domready', function() {
	// allow to deselect the category
	$('noparent').addEvent('click', function(){
		  $('parent_id').selectedIndex = '-1';
  });

	$('image').addEvent('change', function(){
		if (this.get('value')) {
			$('imagelib').empty().adopt(
					new Element('img', {
						src: '../'+this.get('value'),
						class: 're-image-preview',
						alt: 'preview'
					}));
		}
		else {
			$('imagelib').empty();
		}
	}).fireEvent('change');
});

function submitbutton(pressbutton)
{
	var form = document.adminForm;
	var description = <?php echo $this->editor->getContent( 'description' ); ?>
	
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// do field validation
	if (form.name.value == ""){
		alert( "<?php echo JText::_('COM_REDEVENT_ADD_NAME_CATEGORY' ); ?>" );
	} else {
		<?php echo $this->editor->save( 'description' ); ?>
		submitform( pressbutton );
	}
}
</script>


<form action="index.php" method="post" name="adminForm" id="adminForm">

	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td valign="top">
				<table  class="adminform editevent">
					<tr>
						<td>
							<label for="name">
								<?php echo JText::_('COM_REDEVENT_CATEGORY' ).':'; ?>
							</label>
						</td>
						<td>
							<input name="name" value="<?php echo $this->row->name; ?>" size="50" maxlength="100" />
						</td>
						<td>
							<label for="published">
								<?php echo JText::_('COM_REDEVENT_PUBLISHED' ).':'; ?>
							</label>
						</td>
						<td>
							<?php
							$html = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $this->row->published );
							echo $html;
							?>
						</td>
					</tr>
					<tr>
						<td>
							<label for="alias">
								<?php echo JText::_('COM_REDEVENT_Alias' ).':'; ?>
							</label>
						</td>
						<td colspan="3">
							<input class="inputbox" type="text" name="alias" id="alias" size="50" maxlength="100" value="<?php echo $this->row->alias; ?>" />
						</td>
					</tr>
				</table>

			<table class="adminform editevent">
				<tr>
					<td>
						<?php
						// parameters : areaname, content, hidden field, width, height, rows, cols
						echo $this->editor->display( 'description',  $this->row->description, '100%;', '350', '75', '20', array('pagebreak', 'readmore') ) ;
						?>
					</td>
				</tr>
			</table>
			</td>
			<td valign="top" width="320px" style="padding: 7px 0 0 5px">
			<?php
			echo $this->pane->startPane( 'det-pane' );
			$title = JText::_('COM_REDEVENT_CATEGORIES' );
			echo $this->pane->startPanel( $title, 'categories' );
			?>
			<table>
				<tr>
					<td>
						<label for="categories">
							<?php echo JText::_('COM_REDEVENT_PARENT_CATEGORY' ).':'; ?>
						</label>
					</td>
					<td>
						<?php
						echo $this->lists['categories'];
						?>						
					</td>
				  <td><a href="#" id="noparent"><?php echo JText::_('COM_REDEVENT_NONE'); ?></a></td>
				</tr>
			</table>
			<?php
			echo $this->pane->endPanel();
			$title = JText::_('COM_REDEVENT_ACCESS' );
			echo $this->pane->startPanel( $title, 'access' );
			?>
			<table>
				<tr>
					<td>
						<label for="access">
							<?php echo JText::_('COM_REDEVENT_ACCESS' ).':'; ?>
						</label>
					</td>
					<td>
						<?php
						echo $this->lists['access'];
						?>
					</td>
				</tr>
				<tr>
					<td>
						<label for="private" class="hasTip" title="<?php echo JText::_('COM_REDEVENT_CATEGORY_PRIVATE_LABEL').'::'.JText::_('COM_REDEVENT_CATEGORY_PRIVATE_TIP'); ?>">
							<?php echo JText::_( 'COM_REDEVENT_CATEGORY_PRIVATE_LABEL' ).':'; ?>
						</label>
					</td>
					<td>
						<?php
						echo JHTML::_('select.booleanlist', 'private', '', $this->row->private);
						?>
					</td>
				</tr>
			</table>
			<?php
			$title = JText::_('COM_REDEVENT_GROUP' );
			echo $this->pane->endPanel();
			echo $this->pane->startPanel( $title, 'group' );
			?>
			<table>
				<tr>
					<td>
						<label for="groups">
							<?php echo JText::_('COM_REDEVENT_GROUP' ).':'; ?>
						</label>
					</td>
					<td>
						<?php echo $this->lists['groups']; ?>
					</td>
				</tr>
			</table>
			<?php
			$title = JText::_('COM_REDEVENT_IMAGE' );
			echo $this->pane->endPanel();
			echo $this->pane->startPanel( $title, 'vcimage' );
			?>
			<table>
				<tr>
					<td>
						<?php echo $this->form->getLabel('image'); ?>
					</td>
					<td>
						<?php echo $this->form->getInput('image'); ?>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<span id="imagelib"></span>
					</td>
				</tr>
			</table>
			<?php
			$title = JText::_('COM_REDEVENT_METADATA_INFORMATION' );
			echo $this->pane->endPanel();
			echo $this->pane->startPanel( $title, 'metadata' );
			?>
		<table>
		<tr>
			<td>
				<label for="metadesc">
					<?php echo JText::_('COM_REDEVENT_META_DESCRIPTION' ); ?>:
				</label>
				<br />
				<textarea class="inputbox" cols="40" rows="5" name="meta_description" id="metadesc" style="width:300px;"><?php echo str_replace('&','&amp;',$this->row->meta_description); ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<label for="metakey">
					<?php echo JText::_('COM_REDEVENT_META_KEYWORDS' ); ?>:
				</label>
				<br />
				<textarea class="inputbox" cols="40" rows="5" name="meta_keywords" id="metakey" style="width:300px;"><?php echo str_replace('&','&amp;',$this->row->meta_keywords); ?></textarea>
			</td>
		</tr>
		<tr>
			<td>
				<input type="button" class="button" value="<?php echo JText::_('COM_REDEVENT_ADD_CATNAME' ); ?>" onclick="f=document.adminForm;f.metakey.value=f.catname.value;" />
			</td>
		</tr>
		</table>

		<?php
		echo $this->pane->endPanel();
		echo $this->pane->endPane();
		?>
		</td>
	</tr>
</table>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_redevent" />
<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
<input type="hidden" name="controller" value="venuescategories" />
<input type="hidden" name="view" value="venuescategory" />
<input type="hidden" name="task" value="" />
</form>

<?php
//keep session alive while editing
JHTML::_('behavior.keepalive');
?>