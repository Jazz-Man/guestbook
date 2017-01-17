<?php
    namespace UpagesTemplate;

    
    abstract class Abstract_Empty extends Html_Core
    {
        
        public function __construct($attributes = null)
        {
            parent::__construct($this->_tag, $attributes);
        }
    }
