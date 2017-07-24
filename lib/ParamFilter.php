<?php
/*
 * Copyright (C) 2017 Markus Schlegel <tacki@posteo.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class ParamFilter extends Prefab
{
    /**
     * @var array
     */
    protected $options = [];
    
    /**   
     * @var array
     */
    protected $errors = [];
    
    /**   
     * @var array
     */
    protected $debug = [];    
    
    /**
     * Constructor
     * @param array $options
     */
    public function __construct($options=array())
    {
        $options['prefix'] = $options['prefix']?:'filter';       
        
        $this->options = $options;        
    }    
    
    /**
     * Check all available Filters
     * @return boolean
     */
    public function checkAllFilters()
    {
        $f3 = \Base::instance();
        $result = true;

        $filters = $f3->get($this->options['prefix']);
        
        if(!is_array($filters)) {
            // No filter found
            return true;
        }          
        
        foreach ($filters as $type=>$data) {
            if (!is_array($data)) {
                continue;
            }
            
            foreach (array_keys($data) as $parameter) {
                if (!$this->checkParam($type, $parameter)) {
                    $result = false;
                }
            }
        }
        
        return $result;
    }    
    
    /**
     * Check a specific Filter (PARAMS,GET,POST,...)
     * @param string $type
     * @return boolean
     */
    public function checkFilter($type)
    {
        $f3 = \Base::instance();
        $result = true;        
        
        $data = $f3->get($this->options['prefix'].'.'.$type);        
        
        if(!is_array($data)) {
            // No setting found for this filter
            return true;
        }
        
        foreach (array_keys($data) as $parameter) {
            if (!$this->checkParam($type, $parameter)) {
                $result = false;
            }
        }
        
        return $result;        
    }  
    
    /**
     * Get last Error
     * @return array
     */
    public function getLastError()
    {
        return end($this->errors);
    }
    
    /**
     * Get all Errors
     * @return array
     */
    public function getAllErrors()
    {
        return $this->errors;
    }
    
    /**
     * Get debuglog of all checks
     * @return array
     */
    public function getDebug()
    {
        return $this->debug;
    }
    
    /**
     * Test the assigned filter on the given parameter
     * @param string $type
     * @param string $parameter
     * @return boolean
     */
    protected function checkParam($type, $parameter)
    {
        $f3 = \Base::instance();

        $filter = $f3->get($this->options['prefix'].'.'.$type.'.'.$parameter);
        $value  = $f3->get($type.'.'.$parameter);
        
        $res =  preg_match("/^".$filter."$/", $value);
        
        $result = [];
        $result['type'] = $type;
        $result['filter'] = "/^".$filter."$/";
        $result['value'] = $value; 
        
        if ($filter && $value && !$res) {            
            // Bad Value
            $result['message'] = $res===false?"Invalid Filter":"No Match"; 
            $this->errors[] = $result;
            $this->debug[] = $result;
            return false;
        } else {
            $result['message'] = "Match";             
            $this->debug[] = $result;
            return true;
        }                   
    }    
}