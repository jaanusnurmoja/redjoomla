<?php
/**
 * @version 1.0 $Id: default.php 30 2009-05-08 10:22:21Z roland $
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

<form action="index.php?option=com_redevent&amp;view=xrefelement&amp;tmpl=component&amp;function=<?php echo $this->function; ?>" method="post" name="adminForm" id="adminForm">

<table class="adminform">
	<tr>
		<td width="100%">
			<?php echo JText::_('COM_REDEVENT_SEARCH' ).' '.$this->lists['filter']; ?>
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_('COM_REDEVENT_Go' ); ?></button>
			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_('COM_REDEVENT_Reset' ); ?></button>
		</td>
		<td nowrap="nowrap">
			<?php echo $this->lists['state'];	?>
		</td>
	</tr>
</table>

<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5">#</th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_REDEVENT_EVENT_TITLE', 'a.title', $this->lists['order_Dir'], $this->lists['order'], 'eventelement' ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_REDEVENT_DATE', 'x.dates', $this->lists['order_Dir'], $this->lists['order'], 'eventelement' ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_REDEVENT_Start', 'a.times', $this->lists['order_Dir'], $this->lists['order'], 'eventelement' ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_REDEVENT_VENUE', 'loc.venue', $this->lists['order_Dir'], $this->lists['order'], 'eventelement' ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_REDEVENT_CITY', 'loc.city', $this->lists['order_Dir'], $this->lists['order'], 'eventelement' ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_REDEVENT_CATEGORY', 'cat.catname', $this->lists['order_Dir'], $this->lists['order'], 'eventelement' ); ?></th>
		    <th width="1%" nowrap="nowrap"><?php echo JText::_('COM_REDEVENT_PUBLISHED' ); ?></th>
		</tr>
	</thead>

	<tfoot>
		<tr>
			<td colspan="8">
				<?php echo $this->pageNav->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>

	<tbody>
		<?php
			$k = 0;
			for ($i=0, $n=count( $this->rows ); $i < $n; $i++) {
				$row = &$this->rows[$i];
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td>
				<span class="editlinktip hasTip" title="<?php echo JText::_('COM_REDEVENT_SELECT' );?>::<?php echo $row->title; ?>">
        <a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($this->function);?>('<?php echo $row->xref; ?>', '<?php echo $this->escape(addslashes($row->title)); ?>');"><?php echo $this->escape($row->title); ?></a>
        </span>
			</td>
			<td>
				<?php
					//Format date
					$date = redEVENTHelper::isValidDate($row->dates) ? strftime( $this->params->get('backend_formatdate', '%d.%m.%Y'), strtotime( $row->dates )) : JText::_('COM_REDEVENT_OPEN_DATE');
					if ( !redEVENTHelper::isValidDate($row->enddates) ) {
						$displaydate = $date;
					} else {
						$enddate 	= strftime( $this->params->get('backend_formatdate', '%d.%m.%Y'), strtotime( $row->enddates ));

						$displaydate = $date.' - '.$enddate;
					}

					echo $displaydate;
				?>
			</td>
			<td>
				<?php
					//Prepare time
					if (!$row->times) {
						$displaytime = '-';
					} else {
						$time = strftime( $this->params->get('formattime', '%H:%M'), strtotime( $row->times ));
						$displaytime = $time;
					}
					echo $displaytime;
				?>
			</td>
			<td><?php echo $row->venue ? htmlspecialchars($row->venue, ENT_QUOTES, 'UTF-8') : '-'; ?></td>
			<td><?php echo $row->city ? htmlspecialchars($row->city, ENT_QUOTES, 'UTF-8') : '-'; ?></td>
			<td><?php echo $row->catname ? htmlspecialchars($row->catname, ENT_QUOTES, 'UTF-8') : '-'; ?></td>
			<td align="center">
				<?php $img = $row->session_published ? 'tick.png' : 'publish_x.png'; 
				$alt = $row->published ? 'Published' : 'Unpublished';
				echo JHTML::_('image', 'admin/'.$img, $alt, '', true);
				?>
			</td>
		</tr>
			<?php $k = 1 - $k; } ?>
	</tbody>

</table>

<input type="hidden" name="task" value="" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<input type="hidden" name="form_id" value="<?php echo $this->form_id; ?>"/>
</form>