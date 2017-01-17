<?php
    namespace UpagesTemplate;

    
    abstract class Abstract_Content extends Html_Core
    {
        
        public function __construct($attributes = null, $content = null)
        {
            parent::__construct($this->_tag, $attributes, $content);
        }
    }
