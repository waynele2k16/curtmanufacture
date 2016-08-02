<?php
class DataObject implements ArrayAccess
{
    /**
     * Object attributes
     *
     * @var array
     */
    protected $_data = array ();
    
    /**
     * Map short fields names to its full names
     *
     * @var array
     */
    protected $_oldFieldsMap = array ();
    
    /**
     * Setter/Getter underscore transformation cache
     *
     * @var array
     */
    protected static $_underscoreCache = array ();
    
    /**
     * Map of fields to sync to other fields upon changing their data
     */
    protected $_syncFieldsMap = array ();
    
    /**
     * Constructor
     * By default is looking for first argument as array and assignes it as object attributes
     * This behaviour may change in child classes
     */
    public function __construct()
    {
        $args = func_get_args ();
        if (empty ( $args [0] )) {
            $args [0] = array ();
        }
        $this->_data = $args [0];
    }
    protected function _addFullNames()
    {
        $existedShortKeys = array_intersect ( $this->_syncFieldsMap, array_keys ( $this->_data ) );
        if (! empty ( $existedShortKeys )) {
            foreach ( $existedShortKeys as $key ) {
                $fullFieldName = array_search ( $key, $this->_syncFieldsMap );
                $this->_data [$fullFieldName] = $this->_data [$key];
            }
        }
    }
    
    /**
     * Called after old fields are inited.
     * Forms synchronization map to sync old fields and new fields
     * between each other.
     *
     * @return Object
     */
    protected function _prepareSyncFieldsMap()
    {
        $old2New = $this->_oldFieldsMap;
        $new2Old = array_flip ( $this->_oldFieldsMap );
        $this->_syncFieldsMap = array_merge ( $old2New, $new2Old );
        return $this;
    }
    
    /**
     * Add data to the object.
     *
     * Retains previous data in the object.
     *
     * @param array $arr            
     * @return Object
     */
    public function addData(array $arr)
    {
        foreach ( $arr as $index => $value ) {
            $this->setData ( $index, $value );
        }
        return $this;
    }
    
    /**
     * Overwrite data in the object.
     *
     * $key can be string or array.
     * If $key is string, the attribute value will be overwritten by $value
     *
     * If $key is an array, it will overwrite all the data in the object.
     *
     * @param string|array $key            
     * @param mixed $value            
     * @return Object
     */
    public function setData($key, $value = null)
    {
        $this->_hasDataChanges = true;
        if (is_array ( $key )) {
            $this->_data = $key;
            $this->_addFullNames ();
        } else {
            $this->_data [$key] = $value;
            if (isset ( $this->_syncFieldsMap [$key] )) {
                $fullFieldName = $this->_syncFieldsMap [$key];
                $this->_data [$fullFieldName] = $value;
            }
        }
        return $this;
    }
    
    /**
     * Retrieves data from the object
     *
     * If $key is empty will return all the data as an array
     * Otherwise it will return value of the attribute specified by $key
     *
     * If $index is specified it will assume that attribute data is an array
     * and retrieve corresponding member.
     *
     * @param string $key            
     * @param string|int $index            
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        if ('' === $key) {
            return $this->_data;
        }
        
        $default = null;
        
        // accept a/b/c as ['a']['b']['c']
        if (strpos ( $key, '/' )) {
            $keyArr = explode ( '/', $key );
            $data = $this->_data;
            foreach ( $keyArr as $i => $k ) {
                if ($k === '') {
                    return $default;
                }
                if (is_array ( $data )) {
                    if (! isset ( $data [$k] )) {
                        return $default;
                    }
                    $data = $data [$k];
                } elseif ($data instanceof Object) {
                    $data = $data->getData ( $k );
                } else {
                    return $default;
                }
            }
            return $data;
        }
        
        // legacy functionality for $index
        if (isset ( $this->_data [$key] )) {
            if (is_null ( $index )) {
                return $this->_data [$key];
            }
            
            $value = $this->_data [$key];
            if (is_array ( $value )) {
                /**
                 * If we have any data, even if it empty - we should use it, anyway
                 */
                if (isset ( $value [$index] )) {
                    return $value [$index];
                }
                return null;
            } elseif (is_string ( $value )) {
                $arr = explode ( "\n", $value );
                return (isset ( $arr [$index] ) && (! empty ( $arr [$index] ) || strlen ( $arr [$index] ) > 0)) ? $arr [$index] : null;
            } elseif ($value instanceof Object) {
                return $value->getData ( $index );
            } else {
                return $default;
            }
        }
        return $default;
    }
    
    /**
     * Get value from _data array without parse key
     * 
     * @param string $key            
     * @return mixed
     */
    protected function _getData($key)
    {
        return isset ( $this->_data [$key] ) ? $this->_data [$key] : null;
    }
    
    /**
     * Set/Get attribute wrapper
     * 
     * @param string $method            
     * @param array $args            
     * @return mixed
     */
    public function __call($method, $args)
    {
        switch (substr ( $method, 0, 3 )) {
            case 'get' :
                $key = $this->_underscore ( substr ( $method, 3 ) );
                $data = $this->getData ( $key, isset ( $args [0] ) ? $args [0] : null );
                return $data;
            
            case 'set' :
                $key = $this->_underscore ( substr ( $method, 3 ) );
                $result = $this->setData ( $key, isset ( $args [0] ) ? $args [0] : null );
                return $result;
            
            case 'uns' :
                $key = $this->_underscore ( substr ( $method, 3 ) );
                $result = $this->unsetData ( $key );
                return $result;
            
            case 'has' :
                $key = $this->_underscore ( substr ( $method, 3 ) );
                return isset ( $this->_data [$key] );
            default:
                return false;
        }
        throw new Exception ( "Invalid method " . get_class ( $this ) . "::" . $method . "(" . print_r ( $args, 1 ) . ")" );
    }
    
    /**
     * Attribute getter (deprecated)
     * 
     * @param string $var            
     * @return mixed
     */
    public function __get($var)
    {
        $var = $this->_underscore ( $var );
        return $this->getData ( $var );
    }
    
    /**
     * Attribute setter (deprecated)
     * 
     * @param string $var            
     * @param mixed $value            
     */
    public function __set($var, $value)
    {
        $var = $this->_underscore ( $var );
        $this->setData ( $var, $value );
    }
    
    /**
     * Converts field names for setters and geters
     * $this->setMyField($value) === $this->setData('my_field', $value)
     * Uses cache to eliminate unneccessary preg_replace
     * 
     * @param string $name            
     * @return string
     */
    protected function _underscore($name)
    {
        if (isset ( static::$_underscoreCache [$name] )) {
            return static::$_underscoreCache [$name];
        }
        $result = strtolower ( preg_replace ( '/(.)([A-Z])/', "$1_$2", $name ) );
        static::$_underscoreCache [$name] = $result;
        return $result;
    }
    
    /**
     * Implementation of ArrayAccess::offsetSet()
     * 
     * @param string $offset            
     * @param mixed $value            
     */
    public function offsetSet($offset, $value)
    {
        $this->_data [$offset] = $value;
    }
    
    /**
     * Implementation of ArrayAccess::offsetExists()
     * 
     * @param string $offset            
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset ( $this->_data [$offset] );
    }
    
    /**
     * Implementation of ArrayAccess::offsetUnset()
     * 
     * @param string $offset            
     */
    public function offsetUnset($offset)
    {
        unset ( $this->_data [$offset] );
    }
    
    /**
     * Implementation of ArrayAccess::offsetGet()
     * 
     * @param string $offset            
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset ( $this->_data [$offset] ) ? $this->_data [$offset] : null;
    }
}