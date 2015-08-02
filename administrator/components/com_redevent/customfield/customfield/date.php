<?php
/**
 * @version 1.0 $Id: archive.php 30 2009-05-08 10:22:21Z roland $
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

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
* Renders a Textbox Custom field
 * @package		redEVENT
 * @since 2.0
*/

class TCustomfieldDate extends TCustomfield {
 
  /**
  * Element name
  *
  * @access protected
  * @var    string
  */
  var $_name = 'date';

  /**
   * returns the html code for the form element
   *
   * @param array $attributes
   * @return string
   */
  function render($attributes = array())
  {
  	if ($this->required) 
  	{
  		if (isset($attributes['class'])) {
  			$attributes['class'] .= ' required';
  		}
  		else {
  			$attributes['class'] = 'required';
  		}
  	}
  
    if (!is_null($this->value))
    {
    	$selected = $this->value;
    }
    else
    {
    	$selected = $this->default_value;
    }
    return JHTML::calendar( $selected, 'custom'.$this->id, 'custom'.$this->id, '%Y-%m-%d', $this->attributesToString($attributes) );
  }
}
