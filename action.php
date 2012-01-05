<?php
/**
 *
 * @package    solrnamespacebreadcrumb
 * @author     Gabriel Birke <birke@d-scribe.de>
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 */
 
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');
 
class action_plugin_solrnamespacebreadcrumb extends DokuWiki_Action_Plugin {
  /**
   * return some info
   */
  function getInfo(){
    return array(
		 'author' => 'Gabriel Birke',
		 'email'  => 'birke@d-scribe.de',
		 'date'   => '2012-01-05',
		 'name'   => 'Solr Namespace breadcrumb (Action component)',
		 'desc'   => 'Display the namespace breadcrumb path below each search result.',
		 'url'    => 'http://www.d-scribe.de/',
		 );
  }
 
  /**
   * Register the handlers with the dokuwiki's event controller
   */
  function register(&$controller) {
    $controller->register_hook('SOLR_RENDER_RESULT_CONTENT', 'BEFORE',  $this, 'addnamespace');
  }
  
  public function addnamespace(&$event, $params)
  {
    global $ID;
    if(empty($event->data['html']['body'])) {
      return;
    }
    $id = $event->data['id'];
    $bc_links = array();
    while($ns = getNS($id)) {
      $nspage = noNS($ns);
      $nspage_id = "$ns:$nspage";
      if(page_exists($nspage_id)) {
        $name = p_get_first_heading($nspage_id);
        $bc_links[] = '<a href="'.wl($nspage_id).'">'.$name.'</a>';
      }
      else {
        $bc_links[] = '<a href="'.wl('', array('idx'=>$ns)).'">'.$nspage.'</a>';
      }
      $id = $ns;
    }
    if(!empty($bc_links)) {
      //print_r($bc_links);
      $bc_links = array_reverse($bc_links);
      $event->data['html']['namespace_breadcrumb']  = '<div class="search_namespace_breadcrumb">';
      $event->data['html']['namespace_breadcrumb'] .= ' <span>'.$this->getLang('namespaces').'</span> ';
      $event->data['html']['namespace_breadcrumb'] .= implode(' &raquo; ', $bc_links);
      $event->data['html']['namespace_breadcrumb'] .= '</div>';
    }
  }
  
}