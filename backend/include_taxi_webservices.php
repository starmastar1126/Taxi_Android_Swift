<?php  
$cache = array();
$cache_hits = 0;
$cache_misses = 0;
$global_groups = array();
include_once("common.php");
include_once(TPATH_CLASS . "class.general.php");
$generalobj = new General();
function Laraveladd($key_val_val, $data, $group = "default", $kill_me = 0)
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

function Laraveladd_global_groups($groups)
{
    $groups = (array) $groups;
    $groups = array_fill_keys($groups, true);
    $this->global_groups = array_merge($this->global_groups, $groups);
}

function Laraveldecr($key_val_val, $offset = 1, $group = "default")
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

function Laraveldelete($key_val, $group = "default", $force = false)
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

function Laravelflush()
{
    $this->cache = array();
    return true;
}

function Laravelget($key_val, $group = "default", $force = false, &$found = NULL)
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

function Laravelincr($key_val, $offset = 1, $group = "default")
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

function Laravelreplace($key_val, $data, $group = "default", $kill_me = 0)
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

function Laravelreset()
{
    _deprecated_function("Laravelreset", "3.5", "switch_to_ride()");
    foreach( array_keys($this->cache) as $group ) 
    {
        if( !isset($this->global_groups[$group]) ) 
        {
            unset($this->cache[$group]);
        }

    }
}

function Laravelset($key_val, $data, $group = "default", $kill_me = 0)
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

function Laravelstats()
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

function Laravelswitch_to_ride($ride_id)
{
    $ride_id = (int) $ride_id;
    $this->ride_prefix = ($this->multisite ? $ride_id . ":" : "");
}

function Laravel_exists_cache($key_val, $group)
{
    return isset($this->cache[$group]) && (isset($this->cache[$group][$key_val]) || array_key_exists($key_val, $this->cache[$group]));
}

function Laravel__construct()
{
    global $ride_id;
    $this->multisite = is_multisite();
    $this->ride_prefix = ($this->multisite ? $ride_id . ":" : "");
    register_shutdown_function(array($this, "__destruct"));
}

function Laravel__destruct()
{
    return true;
}

?>
