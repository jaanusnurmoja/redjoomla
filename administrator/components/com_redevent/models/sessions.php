<?php
/**
 * @version 2.0
 * @package Joomla
 * @subpackage redEVENT
 * @copyright redEVENT (C) 2008,2009,2010,2011 redCOMPONENT.com / EventList (C) 2005 - 2008 Christoph Lukes
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

/**
 * Joomla Redevent Component Model
 *
 * @package		Redevent
 * @since 2.0
 */
class RedeventModelSessions extends JModel 
{
	/**
	 * event id
	 * @var int
	 */
	var $_eventid = 0;
   /**
   * list data array
   *
   * @var array
   */
  var $_data = null;

  /**
   * total
   *
   * @var integer
   */
  var $_total = null;

  /**
   * Pagination object
   *
   * @var object
   */
  var $_pagination = null;
  
  /**
   * Constructor
   *
   * @since 0.1
   */
  function __construct()
  {
    parent::__construct();
    
    $app    = &JFactory::getApplication();
    $option = Jrequest::getCmd('option');

    // Get the pagination request variables
    $limit      = $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );
    $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
 
		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
    $this->setState('limit', $limit);
    $this->setState('limitstart', $limitstart);
    
    // filters and ordering
    $filter_order     = $app->getUserStateFromRequest( 'com_redevent.sessions.filter_order', 'filter_order', 'obj.dates', 'cmd' );
    $filter_order_Dir = $app->getUserStateFromRequest( 'com_redevent.sessions.filter_order_Dir', 'filter_order_Dir', 'asc', 'word' );    
    
    $search  = $app->getUserStateFromRequest( 'com_redevent.sessions.search', 'search', '', 'string' );
    $eventid = $app->getUserStateFromRequest( 'com_redevent.sessions.eventid', 'eventid', 0, 'int');
    $venueid = $app->getUserStateFromRequest( 'com_redevent.sessions.venueid', 'venueid', 0, 'int');
    
    $filter_state     = $app->getUserStateFromRequest( 'com_redevent.sessions.filter_state', 'filter_state', 'notarchived', 'cmd' );
    $filter_featured  = $app->getUserStateFromRequest( 'com_redevent.sessions.filter_featured', 'filter_featured', '', 'cmd' );
    $filter_group  = $app->getUserStateFromRequest( 'com_redevent.sessions.filter_group', 'filter_group', 0, 'int' );
    $filter_group_manage  = $app->getUserStateFromRequest( 'com_redevent.sessions.filter_group_manage', 'filter_group_manage', 1, 'int' );
    
    $this->setState('filter_order',      $filter_order);
    $this->setState('filter_order_Dir',  $filter_order_Dir);
    $this->setState('filter_state',      $filter_state);
    $this->setState('filter_featured',   $filter_featured);
    $this->setState('filter_group',      $filter_group);
    $this->setState('filter_group_manage', $filter_group_manage);
    $this->setState('search',            strtolower($search));
    $this->setState('eventid',           $eventid);
    $this->setState('venueid',           $venueid);
    
    $this->setEventId($eventid);
  }
  
  function setEventId($id)
  {
  	$this->_eventid = (int) $id;
  	$this->_data = null;
  }
  
  /**
   * Method to get List data
   *
   * @access public
   * @return array
   */
  function getData()
  {
    // Lets load the content if it doesn't already exist
    if (empty($this->_data))
    {
      $query = $this->_buildQuery();
      $pagination = $this->getPagination();
      $res = $this->_getList($query, $pagination->limitstart, $pagination->limit);

      if (!$res) {
   	  	echo $this->_db->getErrorMsg();
   	  	return false;
  		}
  		
  		$this->_data = $res;
  		$this->_addAttendeesStats();
    }
    return $this->_data;
  }
  
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$query = ' SELECT obj.*, 0 AS checked_out, '
		  . ' e.title AS event_title, e.checked_out as event_checked_out, e.registra, '
		  . ' v.venue, v.checked_out as venue_checked_out '
			. ' FROM #__redevent_event_venue_xref AS obj '
			. ' INNER JOIN #__redevent_events AS e ON obj.eventid = e.id '
		  . ' LEFT JOIN #__redevent_venues AS v ON v.id = obj.venueid '
		  ;
		if ($gp = $this->getState('filter_group')) {
			$query .= ' LEFT JOIN #__redevent_venue_category_xref AS xvcat ON v.id = xvcat.venue_id'
		        . ' LEFT JOIN #__redevent_venues_categories AS vc ON xvcat.category_id = vc.id'
            . ' INNER JOIN #__redevent_event_category_xref AS xcat ON xcat.event_id = e.id'
	          . ' INNER JOIN #__redevent_categories AS c ON c.id = xcat.category_id '
	          . ' LEFT JOIN #__redevent_groups_venues AS gv ON gv.venue_id = v.id AND gv.group_id = '.$gp
	          . ' LEFT JOIN #__redevent_groups AS gvg ON gvg.id = gv.group_id '
	          . ' LEFT JOIN #__redevent_groups_venues_categories AS gvc ON gvc.category_id = vc.id AND gvc.group_id = '.$gp
	          . ' LEFT JOIN #__redevent_groups_categories AS gc ON gc.category_id = c.id AND gc.group_id = '.$gp
	          ;
		}
		  
		$query .= $where;
		if ($gp = $this->getState('filter_group')) 
		{
			if ($this->getState('filter_group_manage')) // manage ?
			{
				$group = $this->getGroup($gp);
				if ($group->edit_events == 0) {
					$query .= ' AND 0 ';
				}
				else {
					$query .= ' AND (gv.id IS NOT NULL OR gvc.id IS NOT NULL) '
				       . ' AND (gc.id IS NOT NULL) '
			          ;
				}
			}
			else {
			$query .= ' AND (v.private = 0 OR gv.id IS NOT NULL) '
		       . ' AND (c.private = 0 OR gc.id IS NOT NULL) '
		       . ' AND (vc.private = 0 OR vc.private IS NULL OR gvc.id IS NOT NULL) '
	          ;				
			}
		}
		$query .= $orderby;

		return $query;
	}
	
	/**
	 * get group details
	 * 
	 * @param int $id
	 * @return object
	 */
	function getGroup($id)
	{
		$query = ' SELECT g.* ' 
		       . ' FROM #__redevent_groups AS g ' 
		       . ' WHERE g.id = ' . $this->_db->Quote($id);
		$this->_db->setQuery($query);
		$res = $this->_db->loadObject();
		return $res;
	}

	function _buildContentOrderBy()
	{
		$mainframe = &JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$filter_order		  = $this->getState('filter_order');
		$filter_order_Dir	= $this->getState('filter_order_Dir');

		if ($filter_order == 'obj.dates'){
			$orderby 	= ' ORDER BY obj.dates '.$filter_order_Dir;
		} else {
			$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.' , obj.dates ';
		}

		return $orderby;
	}

	function _buildContentWhere()
	{
		$mainframe = &JFactory::getApplication();
		$option = JRequest::getCmd('option');

		$search				= $this->getState('search');

		$where = array();

		if ($this->_eventid) {
			$where[] = ' obj.eventid = '. $this->_eventid;
		}
		
		if ($search) {
			$where[] = '(LOWER(e.title) LIKE '.$this->_db->Quote('%'.$search.'%').' OR '
			         . ' LOWER(obj.title) LIKE '.$this->_db->Quote('%'.$search.'%').')';
		}
		
		switch ($this->getState('filter_state'))
		{
			case 'unpublished':
				$where[] = ' obj.published = 0 ';
				break;
				
			case 'published':
				$where[] = ' obj.published = 1 ';
				break;
				
			case 'archived':
				$where[] = ' obj.published = -1 ';
				break;
				
			case 'notarchived':
				$where[] = ' obj.published >= 0 ';
				break;
		}
		
		switch ($this->getState('filter_featured'))
		{
			case 'featured':
				$where[] = ' obj.featured = 1 ';
				break;
				
			case 'unfeatured':
				$where[] = ' obj.featured = 0 ';
				break;
		}
		
		if ($this->getState('venueid'))
		{
			$where[] = ' obj.venueid = '.$this->getState('venueid');
		}

		$where 		= ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );

		return $where;
	}
	
  /**
   * Method to get a pagination object
   *
   * @access public
   * @return integer
   */
  function getPagination()
  {
    // Lets load the content if it doesn't already exist
    if (empty($this->_pagination))
    {
      jimport('joomla.html.pagination');
      $this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
    }

    return $this->_pagination;
  }
  

  /**
   * Total nr of items
   *
   * @access public
   * @return integer
   */
  function getTotal()
  {
    // Lets load the total nr if it doesn't already exist
    if (empty($this->_total))
    {
      $query = $this->_buildQuery();
      $this->_total = $this->_getListCount($query);
    }

    return $this->_total;
  }
  
  /**
   * returns event data
   * 
   * @return object event
   */
  function getEvent()
  {
  	if (!$this->_eventid) {
  		return false;
  	}
  	$query = ' SELECT e.id, e.title, e.registra ' 
  	       . ' FROM #__redevent_events AS e '
  	       . ' WHERE id = ' . $this->_db->Quote($this->_eventid);
  	$this->_db->setQuery($query);
  	$res = $this->_db->loadObject();
  	return $res;
  }
  
  /**
   * returns venue data
   * 
   * @return object event
   */
  function getVenue()
  {
  	if (!$this->getState('venueid')) {
  		return false;
  	}
  	$query = ' SELECT v.id, v.venue ' 
  	       . ' FROM #__redevent_venues AS v '
  	       . ' WHERE id = ' . $this->getState('venueid');
  	$this->_db->setQuery($query);
  	$res = $this->_db->loadObject();
  	return $res;
  }
  
  /**
   * adds attendees stats to session
   * 
   * @return boolean true on success
   */
  private function _addAttendeesStats()
  {
  	if (!count($this->_data)) {
  		return false;
  	}
  	
  	$ids = array();
		foreach ($this->_data as $session)
		{
			$ids[] = $session->id;
		}
		
		$query = ' SELECT x.id, COUNT(*) AS total, SUM(r.waitinglist) AS waiting, SUM(1-r.waitinglist) AS attending ' 
		       . ' FROM #__redevent_event_venue_xref AS x'
		       . ' LEFT JOIN #__redevent_register AS r ON x.id = r.xref ' 
		       . ' WHERE x.id IN ('.implode(', ', $ids).')'
		       . ' AND r.confirmed = 1 '
		       . ' AND r.cancelled = 0 '
		       . ' GROUP BY r.xref ';
		$this->_db->setQuery($query);
		$res = $this->_db->loadObjectList('id');

		$noreg = new stdclass();
		$noreg->total = 0;
		$noreg->waiting = 0;
		$noreg->attending = 0;
		
		foreach ($this->_data as &$session)
		{
			if (isset($res[$session->id])) 
			{
				$session->attendees = $res[$session->id];
			}
			else {
				$session->attendees = $noreg;
			}
		}
		return true;
  }

	/**
	 * Method to (un)publish/archive
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function publish($cid = array(), $publish = 1)
	{
		$user 	=& JFactory::getUser();

		if (count( $cid ))
		{
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__redevent_event_venue_xref'
				. ' SET published = ' . (int) $publish
				. ' WHERE id IN ('. $cids .')'
//				. ' AND ( checked_out = 0 OR ( checked_out = ' . (int) $user->get('id'). ' ) )'
			;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to (un)feature
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.9
	 */
	function featured($cid = array(), $featured = 1)
	{
		$user 	=& JFactory::getUser();

		if (count( $cid ))
		{
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__redevent_event_venue_xref'
				. ' SET featured = ' . (int) $featured
				. ' WHERE id IN ('. $cids .')'
			;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}
	
	/**
	 * returns groups as options
	 * @return array
	 */
	function getGroupsOptions()
	{
		$query = ' SELECT g.id AS value, g.name AS text ' 
		       . ' FROM #__redevent_groups AS g ' 
		       . ' ORDER BY g.name ';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
