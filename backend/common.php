<?php  
ob_start();
@session_start();
@header("P3P:CP=\"IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT\"");
define("_TEXEC", 1);
define("TPATH_BASE", dirname(__FILE__));
define("DS", DIRECTORY_SEPARATOR);
require_once(TPATH_BASE . DS . "assets" . DS . "libraries" . DS . "defines.php");
require_once(TPATH_BASE . DS . "assets" . DS . "libraries" . DS . "configuration.php");
if( isset($currency) && $currency != "" ) 
{
    $_SESSION["sess_currency"] = $currency;
}
else
{
    $sql1 = "SELECT * FROM `currency` WHERE `eDefault` = 'Yes' AND `eStatus` = 'Active' ";
    $db_currency_mst = $obj->MySQLSelect($sql1);
    $_SESSION["sess_currency"] = $db_currency_mst[0]["vName"];
    $_SESSION["sess_currency_smybol"] = $db_currency_mst[0]["vSymbol"];
}

$lang = (isset($_REQUEST["lang"]) ? $_REQUEST["lang"] : "");
if( isset($lang) && $lang != "" ) 
{
    $_SESSION["sess_lang"] = $lang;
    $sql1 = "select vTitle, vCode, vCurrencyCode, eDefault,eDirectionCode from language_master where  vCode = '" . $_SESSION["sess_lang"] . "' limit 0,1";
    $db_lng_mst1 = $obj->MySQLSelect($sql1);
    $_SESSION["eDirectionCode"] = $db_lng_mst1[0]["eDirectionCode"];
    $posturi = $_SERVER["HTTP_REFERER"];
    header("Location:" . $posturi);
    exit();
}

if( !isset($_SESSION["sess_lang"]) ) 
{
    $sql = "select vTitle, vCode, vCurrencyCode, eDefault,eDirectionCode from language_master where eDefault='Yes' limit 0,1";
    $db_lng_mst = $obj->MySQLSelect($sql);
    $_SESSION["sess_lang"] = $db_lng_mst[0]["vCode"];
    $_SESSION["eDirectionCode"] = $db_lng_mst[0]["eDirectionCode"];
}

$APP_TYPE = "Ride";
define("APP_TYPE", $APP_TYPE);
$REFERRAL_SCHEME_ENABLE = "Yes";
define("REFERRAL_SCHEME_ENABLE", $REFERRAL_SCHEME_ENABLE);
$WALLET_ENABLE = "Yes";
define("WALLET_ENABLE", $WALLET_ENABLE);
$parent_ufx_catid = "0";
include_once("common_inc.php");
if( $_SERVER["HTTP_HOST"] == "192.168.0.75" ) 
{
}
else
{
    exit();
}


class Memory_Object_Cache
{
    public $cache = array(  );
    public $cache_hits = 0;
    public $cache_misses = 0;
    public $global_groups = array(  );
    public $ride_prefix = NULL;

    public function add($key_val_val, $data, $group = "default", $kill_me = 0)
    {
        if( Memory_suspend_cache_addition() ) 
        {
            return false;
        }

        if( empty($group) ) 
        {
            $group = "default";
        }

        $id = $key_val_val;
        if( $this->multisite && !isset($this->global_groups[$group]) ) 
        {
            $id = $this->ride_prefix . $key_val_val;
        }

        if( $this->_exists($id, $group) ) 
        {
            return false;
        }

        return $this->set($key_val_val, $data, $group, (int) $kill_me);
    }

    public function add_global_groups($groups)
    {
        $groups = (array) $groups;
        $groups = array_fill_keys($groups, true);
        $this->global_groups = array_merge($this->global_groups, $groups);
    }

    public function decr($key_val_val, $offset = 1, $group = "default")
    {
        if( empty($group) ) 
        {
            $group = "default";
        }

        if( $this->multisite && !isset($this->global_groups[$group]) ) 
        {
            $key_val = $this->ride_prefix . $key_val;
        }

        if( !$this->_exists($key_val, $group) ) 
        {
            return false;
        }

        if( !is_numeric($this->cache[$group][$key_val]) ) 
        {
            $this->cache[$group][$key_val] = 0;
        }

        $offset = (int) $offset;
        $this->cache[$group][$key_val] -= $offset;
        if( $this->cache[$group][$key_val] < 0 ) 
        {
            $this->cache[$group][$key_val] = 0;
        }

        return $this->cache[$group][$key_val];
    }

    public function delete($key_val, $group = "default", $force = false)
    {
        if( empty($group) ) 
        {
            $group = "default";
        }

        if( $this->multisite && !isset($this->global_groups[$group]) ) 
        {
            $key_val = $this->ride_prefix . $key_val;
        }

        if( !$force && !$this->_exists($key_val, $group) ) 
        {
            return false;
        }

        unset($this->cache[$group][$key_val]);
        return true;
    }

    public function flush()
    {
        $this->cache = array(  );
        return true;
    }

    public function get($key_val, $group = "default", $force = false, $found = NULL)
    {
        if( empty($group) ) 
        {
            $group = "default";
        }

        if( $this->multisite && !isset($this->global_groups[$group]) ) 
        {
            $key_val = $this->ride_prefix . $key_val;
        }

        if( $this->_exists($key_val, $group) ) 
        {
            $found = true;
            $this->cache_hits += 1;
            if( is_object($this->cache[$group][$key_val]) ) 
            {
                return clone $this->cache[$group][$key_val];
            }

            return $this->cache[$group][$key_val];
        }

        $found = false;
        $this->cache_misses += 1;
        return false;
    }

    public function incr($key_val, $offset = 1, $group = "default")
    {
        if( empty($group) ) 
        {
            $group = "default";
        }

        if( $this->multisite && !isset($this->global_groups[$group]) ) 
        {
            $key_val = $this->ride_prefix . $key_val;
        }

        if( !$this->_exists($key_val, $group) ) 
        {
            return false;
        }

        if( !is_numeric($this->cache[$group][$key_val]) ) 
        {
            $this->cache[$group][$key_val] = 0;
        }

        $offset = (int) $offset;
        $this->cache[$group][$key_val] += $offset;
        if( $this->cache[$group][$key_val] < 0 ) 
        {
            $this->cache[$group][$key_val] = 0;
        }

        return $this->cache[$group][$key_val];
    }

    public function replace($key_val, $data, $group = "default", $kill_me = 0)
    {
        if( empty($group) ) 
        {
            $group = "default";
        }

        $id = $key_val;
        if( $this->multisite && !isset($this->global_groups[$group]) ) 
        {
            $id = $this->ride_prefix . $key_val;
        }

        if( !$this->_exists($id, $group) ) 
        {
            return false;
        }

        return $this->set($key_val, $data, $group, (int) $kill_me);
    }

    public function reset()
    {
        _deprecated_function("reset", "3.5", "switch_to_ride()");
        foreach( array_keys($this->cache) as $group ) 
        {
            if( !isset($this->global_groups[$group]) ) 
            {
                unset($this->cache[$group]);
            }

        }
    }

    public function set($key_val, $data, $group = "default", $kill_me = 0)
    {
        if( empty($group) ) 
        {
            $group = "default";
        }

        if( $this->multisite && !isset($this->global_groups[$group]) ) 
        {
            $key_val = $this->ride_prefix . $key_val;
        }

        if( is_object($data) ) 
        {
            $data = clone $data;
        }

        $this->cache[$group][$key_val] = $data;
        return true;
    }

    public function stats()
    {
        echo "<p>";
        echo "<strong>Cache Hits:</strong> " . $this->cache_hits . "<br />";
        echo "<strong>Cache Misses:</strong> " . $this->cache_misses . "<br />";
        echo "</p><ul>";
        foreach( $this->cache as $group => $cache ) 
        {
            echo "<li><strong>Group:</strong> " . $group . " - ( " . number_format(strlen(serialize($cache)) / 1024, 2) . "k )</li>";
        }
        echo "</ul>";
    }

    public function switch_to_ride($ride_id)
    {
        $ride_id = (int) $ride_id;
        $this->ride_prefix = ($this->multisite ? $ride_id . ":" : "");
    }

    protected function _exists($key_val, $group)
    {
        return isset($this->cache[$group]) && (isset($this->cache[$group][$key_val]) || array_key_exists($key_val, $this->cache[$group]));
    }

    public function __construct()
    {
        global $ride_id;
        $this->multisite = is_multisite();
        $this->ride_prefix = ($this->multisite ? $ride_id . ":" : "");
        register_shutdown_function(array( $this, "__destruct" ));
    }

    public function __destruct()
    {
        return true;
    }

}

function Memory_cache_add($key_val_val, $data, $group = "", $kill_me = 0)
{
    global $Template_object_cache;
    return $Template_object_cache->add($key_val_val, $data, $group, (int) $kill_me);
}

function Memory_cache_close()
{
    return true;
}

function Memory_cache_decr($key_val_val, $offset = 1, $group = "")
{
    global $Template_object_cache;
    return $Template_object_cache->decr($key_val_val, $offset, $group);
}

function Memory_cache_delete($key_val_val, $group = "")
{
    global $Template_object_cache;
    return $Template_object_cache->delete($key_val_val, $group);
}

function Memory_cache_flush()
{
    global $Template_object_cache;
    return $Template_object_cache->flush();
}

function Memory_cache_get($key_val_val, $group = "", $force = false, $found = NULL)
{
    global $Template_object_cache;
    return $Template_object_cache->get($key_val_val, $group, $force, $found);
}

function Memory_cache_incr($key_val_val, $offset = 1, $group = "")
{
    global $Template_object_cache;
    return $Template_object_cache->incr($key_val_val, $offset, $group);
}

function Memory_cache_init()
{
    $GLOBALS["Memory_object_cache"] = new Memory_Object_Cache();
}

function Memory_cache_replace($key_val_val, $data, $group = "", $kill_me = 0)
{
    global $Template_object_cache;
    return $Template_object_cache->replace($key_val_val, $data, $group, (int) $kill_me);
}

function Memory_cache_set($key_val_val, $data, $group = "", $kill_me = 0)
{
    global $Template_object_cache;
    return $Template_object_cache->set($key_val_val, $data, $group, (int) $kill_me);
}

function Memory_cache_switch_to_ride($ride_id)
{
    global $Template_object_cache;
    return $Template_object_cache->switch_to_ride($ride_id);
}

function Memory_cache_add_global_groups($groups)
{
    global $Template_object_cache;
    return $Template_object_cache->add_global_groups($groups);
}

function Memory_cache_add_non_persistent_groups($groups)
{
}

function Memory_cache_reset()
{
    _deprecated_function("Memory_cache_reset", "3.5");
    global $Template_object_cache;
    return $Template_object_cache->reset();
}

function next_ride($format = "%", $next = "next ride: ", $title = "yes", $in_same_cat = "no", $limitnext = 1, $excluded_categories = "")
{
    _deprecated_function("next_ride", "2.0", "next_ride_link()");
    if( empty($in_same_cat) || "no" == $in_same_cat ) 
    {
        $in_same_cat = false;
    }
    else
    {
        $in_same_cat = true;
    }

    $ride = get_next_ride($in_same_cat, $excluded_categories);
    if( !$ride ) 
    {
        return NULL;
    }

    $string = "<a href=\"" . get_permalink($ride->ID) . "\">" . $next;
    if( "yes" == $title ) 
    {
        $string .= apply_filters("the_title", $ride->ride_title, $ride->ID);
    }

    $string .= "</a>";
    $format = str_replace("%", $string, $format);
    echo $format;
}

function user_can_create_ride($user_id, $ride_id = 1, $category_id = "None")
{
    _deprecated_function("user_can_create_ride", "2.0", "current_user_can()");
    $author_data = get_userdata($user_id);
    return 1 < $author_data->user_level;
}

function user_can_create_draft($user_id, $ride_id = 1, $category_id = "None")
{
    _deprecated_function("user_can_create_draft", "2.0", "current_user_can()");
    $author_data = get_userdata($user_id);
    return 1 <= $author_data->user_level;
}

function user_can_edit_ride($user_id, $ride_id = 1)
{
    _deprecated_function("user_can_edit_ride", "2.0", "current_user_can()");
    $author_data = get_userdata($user_id);
    $ride = get_ride($ride_id);
    $ride_author_data = get_userdata($ride->ride_author);
    if( $user_id == $ride_author_data->ID && !($ride->ride_status == "publish" && $author_data->user_level < 2) || $ride_author_data->user_level < $author_data->user_level || 10 <= $author_data->user_level ) 
    {
        return true;
    }

    return false;
}

function user_can_delete_ride($user_id, $ride_id = 1)
{
    _deprecated_function("user_can_delete_ride", "2.0", "current_user_can()");
    return user_can_edit_ride($user_id, $ride_id);
}

function user_can_set_ride_date($user_id, $ride_id = 1, $category_id = "None")
{
    _deprecated_function("user_can_set_ride_date", "2.0", "current_user_can()");
    $author_data = get_userdata($user_id);
    return 4 < $author_data->user_level && user_can_create_ride($user_id, $ride_id, $category_id);
}

function user_can_edit_ride_date($user_id, $ride_id = 1)
{
    _deprecated_function("user_can_edit_ride_date", "2.0", "current_user_can()");
    $author_data = get_userdata($user_id);
    return 4 < $author_data->user_level && user_can_edit_ride($user_id, $ride_id);
}

function user_can_edit_ride_comments($user_id, $ride_id = 1)
{
    _deprecated_function("user_can_edit_ride_comments", "2.0", "current_user_can()");
    return user_can_edit_ride($user_id, $ride_id);
}

function user_can_delete_ride_comments($user_id, $ride_id = 1)
{
    _deprecated_function("user_can_delete_ride_comments", "2.0", "current_user_can()");
    return user_can_edit_ride_comments($user_id, $ride_id);
}

function user_can_edit_user($user_id, $other_user)
{
    _deprecated_function("user_can_edit_user", "2.0", "current_user_can()");
    $user = get_userdata($user_id);
    $other = get_userdata($other_user);
    if( $other->user_level < $user->user_level || 8 < $user->user_level || $user->ID == $other->ID ) 
    {
        return true;
    }

    return false;
}

function get_linksbyname($cat_name = "noname", $before = "", $after = "<br />", $between = " ", $show_images = true, $orderby = "id", $show_description = true, $show_rating = false, $limit = -1, $show_updated = 0)
{
    _deprecated_function("get_linksbyname", "2.1", "get_bookmarks()");
    $cat_id = -1;
    $cat = get_term_by("name", $cat_name, "link_category");
    if( $cat ) 
    {
        $cat_id = $cat->term_id;
    }

    get_links($cat_id, $before, $after, $between, $show_images, $orderby, $show_description, $show_rating, $limit, $show_updated);
}

function ct_ride_get_linksbyname($category, $args = "")
{
    _deprecated_function("ct_ride_get_linksbyname", "2.1", "ct_ride_list_bookmarks()");
    $defaults = array( "after" => "<br />", "before" => "", "categorize" => 0, "category_after" => "", "category_before" => "", "category_name" => $category, "show_description" => 1, "title_li" => "" );
    $r = ct_ride_parse_args($args, $defaults);
    return ct_ride_list_bookmarks($r);
}

function get_linkobjectsbyname($cat_name = "noname", $orderby = "name", $limit = -1)
{
    _deprecated_function("get_linkobjectsbyname", "2.1", "get_bookmarks()");
    $cat_id = -1;
    $cat = get_term_by("name", $cat_name, "link_category");
    if( $cat ) 
    {
        $cat_id = $cat->term_id;
    }

    return get_linkobjects($cat_id, $orderby, $limit);
}

function get_linkobjects($category = 0, $orderby = "name", $limit = 0)
{
    _deprecated_function("get_linkobjects", "2.1", "get_bookmarks()");
    $links = get_bookmarks(array( "category" => $category, "orderby" => $orderby, "limit" => $limit ));
    $links_array = array(  );
    foreach( $links as $link ) 
    {
        $links_array[] = $link;
    }
    return $links_array;
}

function get_linksbyname_withrating($cat_name = "noname", $before = "", $after = "<br />", $between = " ", $show_images = true, $orderby = "id", $show_description = true, $limit = -1, $show_updated = 0)
{
    _deprecated_function("get_linksbyname_withrating", "2.1", "get_bookmarks()");
    get_linksbyname($cat_name, $before, $after, $between, $show_images, $orderby, $show_description, true, $limit, $show_updated);
}

function get_links_withrating($category = -1, $before = "", $after = "<br />", $between = " ", $show_images = true, $orderby = "id", $show_description = true, $limit = -1, $show_updated = 0)
{
    _deprecated_function("get_links_withrating", "2.1", "get_bookmarks()");
    get_links($category, $before, $after, $between, $show_images, $orderby, $show_description, true, $limit, $show_updated);
}

function get_autotoggle($id = 0)
{
    _deprecated_function("get_autotoggle", "2.1");
    return 0;
}

function list_cats($optionall = 1, $all = "All", $sort_column = "ID", $sort_order = "asc", $file = "", $list = true, $optiondates = 0, $optioncount = 0, $hide_empty = 1, $use_desc_for_title = 1, $children = false, $child_of = 0, $categories = 0, $recurse = 0, $feed = "", $feed_image = "", $exclude = "", $hierarchical = false)
{
    _deprecated_function("list_cats", "2.1", "ct_ride_list_categories()");
    $query = compact("optionall", "all", "sort_column", "sort_order", "file", "list", "optiondates", "optioncount", "hide_empty", "use_desc_for_title", "children", "child_of", "categories", "recurse", "feed", "feed_image", "exclude", "hierarchical");
    return ct_ride_list_cats($query);
}

function ct_ride_list_cats($args = "")
{
    _deprecated_function("ct_ride_list_cats", "2.1", "ct_ride_list_categories()");
    $r = ct_ride_parse_args($args);
    if( isset($r["optionall"]) && isset($r["all"]) ) 
    {
        $r["show_option_all"] = $r["all"];
    }

    if( isset($r["sort_column"]) ) 
    {
        $r["orderby"] = $r["sort_column"];
    }

    if( isset($r["sort_order"]) ) 
    {
        $r["order"] = $r["sort_order"];
    }

    if( isset($r["optiondates"]) ) 
    {
        $r["show_last_update"] = $r["optiondates"];
    }

    if( isset($r["optioncount"]) ) 
    {
        $r["show_count"] = $r["optioncount"];
    }

    if( isset($r["list"]) ) 
    {
        $r["style"] = ($r["list"] ? "list" : "break");
    }

    $r["title_li"] = "";
    return ct_ride_list_categories($r);
}

function dropdown_cats($optionall = 1, $all = "All", $orderby = "ID", $order = "asc", $show_last_update = 0, $show_count = 0, $hide_empty = 1, $optionnone = false, $selected = 0, $exclude = 0)
{
    _deprecated_function("dropdown_cats", "2.1", "ct_ride_dropdown_categories()");
    $show_option_all = "";
    if( $optionall ) 
    {
        $show_option_all = $all;
    }

    $show_option_none = "";
    if( $optionnone ) 
    {
        $show_option_none = __("None");
    }

    $vars = compact("show_option_all", "show_option_none", "orderby", "order", "show_last_update", "show_count", "hide_empty", "selected", "exclude");
    $query = add_query_arg($vars, "");
    return ct_ride_dropdown_categories($query);
}

function list_authors($optioncount = false, $exclude_admin = true, $show_fullname = false, $hide_empty = true, $feed = "", $feed_image = "")
{
    _deprecated_function("list_authors", "2.1", "ct_ride_list_authors()");
    $args = compact("optioncount", "exclude_admin", "show_fullname", "hide_empty", "feed", "feed_image");
    return ct_ride_list_authors($args);
}

function ct_ride_get_ride_cats($rideid = "1", $ride_ID = 0)
{
    _deprecated_function("ct_ride_get_ride_cats", "2.1", "ct_ride_get_ride_categories()");
    return ct_ride_get_ride_categories($ride_ID);
}

function ct_ride_set_ride_cats($rideid = "1", $ride_ID = 0, $ride_categories = array(  ))
{
    _deprecated_function("ct_ride_set_ride_cats", "2.1", "ct_ride_set_ride_categories()");
    return ct_ride_set_ride_categories($ride_ID, $ride_categories);
}

function get_archives($type = "", $limit = "", $format = "html", $before = "", $after = "", $show_ride_count = false)
{
    _deprecated_function("get_archives", "2.1", "ct_ride_get_archives()");
    $args = compact("type", "limit", "format", "before", "after", "show_ride_count");
    return ct_ride_get_archives($args);
}

function get_author_link($echo, $author_id, $author_nicename = "")
{
    _deprecated_function("get_author_link", "2.1", "get_author_rides_url()");
    $link = get_author_rides_url($author_id, $author_nicename);
    if( $echo ) 
    {
        echo $link;
    }

    return $link;
}

function link_pages($before = "<br />", $after = "<br />", $next_or_number = "number", $nextpagelink = "next page", $previouspagelink = "previous page", $pagelink = "%", $more_file = "")
{
    _deprecated_function("link_pages", "2.1", "ct_ride_link_pages()");
    $args = compact("before", "after", "next_or_number", "nextpagelink", "previouspagelink", "pagelink", "more_file");
    return ct_ride_link_pages($args);
}

function get_settings($option)
{
    _deprecated_function("get_settings", "2.1", "get_option()");
    return get_option($option);
}

function permalink_link()
{
    _deprecated_function("permalink_link", "1.2", "the_permalink()");
    the_permalink();
}

function permalink_single_rss($deprecated = "")
{
    _deprecated_function("permalink_single_rss", "2.3", "the_permalink_rss()");
    the_permalink_rss();
}

function ct_ride_get_links($args = "")
{
    _deprecated_function("ct_ride_get_links", "2.1", "ct_ride_list_bookmarks()");
    if( strpos($args, "=") === false ) 
    {
        $cat_id = $args;
        $args = add_query_arg("category", $cat_id, $args);
    }

    $defaults = array( "after" => "<br />", "before" => "", "between" => " ", "categorize" => 0, "category" => "", "echo" => true, "limit" => -1, "orderby" => "name", "show_description" => true, "show_images" => true, "show_rating" => false, "show_updated" => true, "title_li" => "" );
    $r = ct_ride_parse_args($args, $defaults);
    return ct_ride_list_bookmarks($r);
}

function get_links($category = -1, $before = "", $after = "<br />", $between = " ", $show_images = true, $orderby = "name", $show_description = true, $show_rating = false, $limit = -1, $show_updated = 1, $echo = true)
{
    _deprecated_function("get_links", "2.1", "get_bookmarks()");
    $order = "ASC";
    if( substr($orderby, 0, 1) == "_" ) 
    {
        $order = "DESC";
        $orderby = substr($orderby, 1);
    }

    if( $category == -1 ) 
    {
        $category = "";
    }

    $results = get_bookmarks(array( "category" => $category, "orderby" => $orderby, "order" => $order, "show_updated" => $show_updated, "limit" => $limit ));
    if( !$results ) 
    {
        return NULL;
    }

    $output = "";
    foreach( (array) $results as $row ) 
    {
        if( !isset($row->recently_updated) ) 
        {
            $row->recently_updated = false;
        }

        $output .= $before;
        if( $show_updated && $row->recently_updated ) 
        {
            $output .= get_option("links_recently_updated_prepend");
        }

        $the_link = "#";
        if( !empty($row->link_url) ) 
        {
            $the_link = esc_url($row->link_url);
        }

        $rel = $row->link_rel;
        if( "" != $rel ) 
        {
            $rel = " rel=\"" . $rel . "\"";
        }

        $desc = esc_attr(sanitize_bookmark_field("link_description", $row->link_description, $row->link_id, "display"));
        $name = esc_attr(sanitize_bookmark_field("link_name", $row->link_name, $row->link_id, "display"));
        $title = $desc;
        if( $show_updated && substr($row->link_updated_f, 0, 2) != "00" ) 
        {
            $title .= " (" . __("Last updated") . " " . date(get_option("links_updated_date_format"), $row->link_updated_f + get_option("gmt_offset") * HOUR_IN_SECONDS) . ")";
        }

        if( "" != $title ) 
        {
            $title = " title=\"" . $title . "\"";
        }

        $alt = " alt=\"" . $name . "\"";
        $target = $row->link_target;
        if( "" != $target ) 
        {
            $target = " target=\"" . $target . "\"";
        }

        $output .= "<a href=\"" . $the_link . "\"" . $rel . $title . $target . ">";
        if( $row->link_image != NULL && $show_images ) 
        {
            if( strpos($row->link_image, "http") !== false ) 
            {
                $output .= "<img src=\"" . $row->link_image . "\" " . $alt . " " . $title . " />";
            }
            else
            {
                $output .= "<img src=\"" . get_option("siteurl") . (string) $row->link_image . "\" " . $alt . " " . $title . " />";
            }

        }
        else
        {
            $output .= $name;
        }

        $output .= "</a>";
        if( $show_updated && $row->recently_updated ) 
        {
            $output .= get_option("links_recently_updated_append");
        }

        if( $show_description && "" != $desc ) 
        {
            $output .= $between . $desc;
        }

        if( $show_rating ) 
        {
            $output .= $between . get_linkrating($row);
        }

        $output .= (string) $after . "\n";
    }
    if( !$echo ) 
    {
        return $output;
    }

    echo $output;
}

function get_links_list($order = "name")
{
    _deprecated_function("get_links_list", "2.1", "ct_ride_list_bookmarks()");
    $order = strtolower($order);
    $direction = "ASC";
    if( "_" == substr($order, 0, 1) ) 
    {
        $direction = "DESC";
        $order = substr($order, 1);
    }

    if( !isset($direction) ) 
    {
        $direction = "";
    }

    $cats = get_categories(array( "type" => "link", "orderby" => $order, "order" => $direction, "hierarchical" => 0 ));
    if( $cats ) 
    {
        foreach( (array) $cats as $cat ) 
        {
            echo "  <li id=\"linkcat-" . $cat->term_id . "\" class=\"linkcat\"><h2>" . apply_filters("link_category", $cat->name) . "</h2>\n\t<ul>\n";
            get_links($cat->term_id, "<li>", "</li>", "\n", true, "name", false);
            echo "\n\t</ul>\n</li>\n";
        }
    }

}

function links_popup_script($text = "Links", $width = 400, $height = 400, $file = "links.all.php", $count = true)
{
    _deprecated_function("links_popup_script", "2.1");
}

function get_linkrating($link)
{
    _deprecated_function("get_linkrating", "2.1", "sanitize_bookmark_field()");
    return sanitize_bookmark_field("link_rating", $link->link_rating, $link->link_id, "display");
}

function get_linkcatname($id = 0)
{
    _deprecated_function("get_linkcatname", "2.1", "get_category()");
    $id = (int) $id;
    if( empty($id) ) 
    {
        return "";
    }

    $cats = ct_ride_get_link_cats($id);
    if( empty($cats) || !is_array($cats) ) 
    {
        return "";
    }

    $cat_id = (int) $cats[0];
    $cat = get_category($cat_id);
    return $cat->name;
}

function comments_rss_link($link_text = "Comments RSS")
{
    _deprecated_function("comments_rss_link", "2.5", "ride_comments_feed_link()");
    ride_comments_feed_link($link_text);
}

function get_category_rss_link($echo = false, $cat_ID = 1)
{
    _deprecated_function("get_category_rss_link", "2.5", "get_category_feed_link()");
    $link = get_category_feed_link($cat_ID, "rss2");
    if( $echo ) 
    {
        echo $link;
    }

    return $link;
}

function get_author_rss_link($echo = false, $author_id = 1)
{
    _deprecated_function("get_author_rss_link", "2.5", "get_author_feed_link()");
    $link = get_author_feed_link($author_id);
    if( $echo ) 
    {
        echo $link;
    }

    return $link;
}

function comments_rss()
{
    _deprecated_function("comments_rss", "2.2", "get_ride_comments_feed_link()");
    return esc_url(get_ride_comments_feed_link());
}

function create_user($username, $password, $email)
{
    _deprecated_function("create_user", "2.0", "ct_ride_create_user()");
    return ct_ride_create_user($username, $password, $email);
}

function gzip_compression()
{
    _deprecated_function("gzip_compression", "2.5");
    return false;
}

function get_commentdata($comment_ID, $no_cache = 0, $include_unapproved = false)
{
    _deprecated_function("get_commentdata", "2.7", "get_comment()");
    return get_comment($comment_ID, ARRAY_A);
}

function get_catname($cat_ID)
{
    _deprecated_function("get_catname", "2.8", "get_cat_name()");
    return get_cat_name($cat_ID);
}

function get_category_children($id, $before = "/", $after = "", $visited = array(  ))
{
    _deprecated_function("get_category_children", "2.8", "get_term_children()");
    if( 0 == $id ) 
    {
        return "";
    }

    $chain = "";
    $cat_ids = get_all_category_ids();
    foreach( (array) $cat_ids as $cat_id ) 
    {
        if( $cat_id == $id ) 
        {
            continue;
        }

        $category = get_category($cat_id);
        if( is_ct_ride_error($category) ) 
        {
            return $category;
        }

        if( $category->parent == $id && !in_array($category->term_id, $visited) ) 
        {
            $visited[] = $category->term_id;
            $chain .= $before . $category->term_id . $after;
            $chain .= get_category_children($category->term_id, $before, $after);
        }

    }
    return $chain;
}

function update_ridedata($ct_ride_type, $object_id, $ride_key, $ride_value, $prev_value = "")
{
    if( !$ct_ride_type || !$ride_key ) 
    {
        return false;
    }

    if( !($object_id = absint($object_id)) ) 
    {
        return false;
    }

    if( !($table = _get_ride_table($ct_ride_type)) ) 
    {
        return false;
    }

    global $ct_ridedb;
    $column = sanitize_key($ct_ride_type . "_id");
    $id_column = ("user" == $ct_ride_type ? "uride_id" : "ride_id");
    $ride_key = ct_ride_unslash($ride_key);
    $passed_value = $ride_value;
    $ride_value = ct_ride_unslash($ride_value);
    $ride_value = sanitize_ride($ride_key, $ride_value, $ct_ride_type);
    $check = apply_filters("update_" . $ct_ride_type . "_ridedata", NULL, $object_id, $ride_key, $ride_value, $prev_value);
    if( NULL !== $check ) 
    {
        return (bool) $check;
    }

    if( empty($prev_value) ) 
    {
        $old_value = get_ridedata($ct_ride_type, $object_id, $ride_key);
        if( count($old_value) == 1 && $old_value[0] === $ride_value ) 
        {
            return false;
        }

    }

    if( !($ride_id = $ct_ridedb->get_var($ct_ridedb->prepare("SELECT " . $id_column . " FROM " . $table . " WHERE ride_key = %s AND " . $column . " = %d", $ride_key, $object_id))) ) 
    {
        return add_ridedata($ct_ride_type, $object_id, $ride_key, $passed_value);
    }

    $_ride_value = $ride_value;
    $ride_value = maybe_serialize($ride_value);
    $data = compact("ride_value");
    $where = array( $column => $object_id, "ride_key" => $ride_key );
    if( !empty($prev_value) ) 
    {
        $prev_value = maybe_serialize($prev_value);
        $where["ride_value"] = $prev_value;
    }

    do_action("update_" . $ct_ride_type . "_ride", $ride_id, $object_id, $ride_key, $_ride_value);
    if( "post" == $ct_ride_type ) 
    {
        do_action("update_postride", $ride_id, $object_id, $ride_key, $ride_value);
    }

    $result = $ct_ridedb->update($table, $data, $where);
    if( !$result ) 
    {
        return false;
    }

    ct_ride_cache_delete($object_id, $ct_ride_type . "_ride");
    do_action("updated_" . $ct_ride_type . "_ride", $ride_id, $object_id, $ride_key, $_ride_value);
    if( "post" == $ct_ride_type ) 
    {
        do_action("updated_postride", $ride_id, $object_id, $ride_key, $ride_value);
    }

    return true;
}

function get_ridedata($ride_type, $object_id, $ride_key = "", $single = false)
{
    if( !$ride_type ) 
    {
        return false;
    }

    if( !($object_id = absint($object_id)) ) 
    {
        return false;
    }

    $check = apply_filters("get_" . $ride_type . "_ridedata", NULL, $object_id, $ride_key, $single);
    if( NULL !== $check ) 
    {
        if( $single && is_array($check) ) 
        {
            return $check[0];
        }

        return $check;
    }

    $ride_cache = ct_ride_cache_get($object_id, $ride_type . "_ride");
    if( !$ride_cache ) 
    {
        $ride_cache = update_ride_cache($ride_type, array( $object_id ));
        $ride_cache = $ride_cache[$object_id];
    }

    if( !$ride_key ) 
    {
        return $ride_cache;
    }

    if( isset($ride_cache[$ride_key]) ) 
    {
        if( $single ) 
        {
            return maybe_unserialize($ride_cache[$ride_key][0]);
        }

        return array_map("maybe_unserialize", $ride_cache[$ride_key]);
    }

    if( $single ) 
    {
        return "";
    }

    return array(  );
}

function ridedata_exists($ride_type, $object_id, $ride_key)
{
    if( !$ride_type ) 
    {
        return false;
    }

    if( !($object_id = absint($object_id)) ) 
    {
        return false;
    }

    $check = apply_filters("get_" . $ride_type . "_ridedata", NULL, $object_id, $ride_key, true);
    if( NULL !== $check ) 
    {
        return true;
    }

    $ride_cache = ct_ride_cache_get($object_id, $ride_type . "_ride");
    if( !$ride_cache ) 
    {
        $ride_cache = update_ride_cache($ride_type, array( $object_id ));
        $ride_cache = $ride_cache[$object_id];
    }

    if( isset($ride_cache[$ride_key]) ) 
    {
        return true;
    }

    return false;
}

function get_ridedata_by_mid($ride_type, $ride_id)
{
    global $ct_ridedb;
    if( !$ride_type ) 
    {
        return false;
    }

    if( !($ride_id = absint($ride_id)) ) 
    {
        return false;
    }

    if( !($table = _get_ride_table($ride_type)) ) 
    {
        return false;
    }

    $id_column = ("user" == $ride_type ? "uride_id" : "ride_id");
    if( empty($ride) ) 
    {
        return false;
    }

    if( isset($ride->ride_value) ) 
    {
    }

}

function delete_ridedata_by_mid($ride_type, $ride_id)
{
    global $ct_ridedb;
    if( !$ride_type ) 
    {
        return false;
    }

    if( !($ride_id = absint($ride_id)) ) 
    {
        return false;
    }

    if( !($table = _get_ride_table($ride_type)) ) 
    {
        return false;
    }

    $column = sanitize_key($ride_type . "_id");
    $id_column = ("user" == $ride_type ? "uride_id" : "ride_id");
    if( $ride = get_ridedata_by_mid($ride_type, $ride_id) ) 
    {
        $object_id = $ride->$column;
        do_action("delete_" . $ride_type . "_ride", (array) $ride_id, $object_id, $ride->ride_key, $ride->ride_value);
        if( "post" == $ride_type || "comment" == $ride_type ) 
        {
            do_action("delete_" . $ride_type . "ride", $ride_id);
        }

        $result = (bool) $templatedb->delete($table, array( $id_column => $ride_id ));
        template_cache_delete($object_id, $ride_type . "_ride");
        do_action("deleted_" . $ride_type . "_ride", (array) $ride_id, $object_id, $ride->ride_key, $ride->ride_value);
        if( "post" == $ride_type || "comment" == $ride_type ) 
        {
            do_action("deleted_" . $ride_type . "ride", $ride_id);
        }

        return $result;
    }

    return false;
}

function get_ride_sql($ride_query, $type, $primary_table, $primary_id_column, $context = NULL)
{
    return $ride_query_obj;
}


