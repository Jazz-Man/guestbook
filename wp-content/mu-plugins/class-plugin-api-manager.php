<?php
    /*
    Plugin Name:        Plugin_API_Manager
    Plugin URI:         https://gist.github.com/Jazz-Man/2da99cc7a4ec6f2429425bb65d34bd38
    Description:        A collection of modules to apply theme-agnostic front-end modifications to WordPress.
    Version:            1.0.0
    Author:             CARL ALEXANDER
    Author URI:         https://carlalexander.ca/design-system-wordpress-event-management/

    License:            MIT License
    License URI:        http://opensource.org/licenses/MIT
    */

    /**
     * Class Plugin_API_Manager.
     */
    class Plugin_API_Manager
    {
        private $hook;
        private $priority;
        private $accepted_args;

        /**
         * Plugin_API_Manager constructor.
         *
         * @param string $hook
         */
        public function __construct($hook = null)
        {
            if (null !== $hook) {
                $this->setHook($hook);
            }
            $this->setPriority();
            $this->setAcceptedArgs();
        }

        /**
         * @param      $callback
         * @param null $priority
         * @param null $accepted_args
         */
        public function addCallback($callback, $priority = null, $accepted_args = null)
        {
            if (null !== $priority) {
                $this->setPriority($priority);
            }
            if (null !== $accepted_args) {
                $this->setAcceptedArgs($accepted_args);
            }
            $priority      = $priority ?? $this->priority;
            $accepted_args = $accepted_args ?? $this->accepted_args;
            if (is_array($this->hook)) {
                foreach ($this->hook as $hook) {
                    add_filter($hook, $callback, $priority, $accepted_args);
                }
            } else {
                add_filter($this->hook, $callback, $priority, $accepted_args);
            }
        }

        /**
         * @param mixed $hook
         */
        public function setHook($hook)
        {
            $this->hook = $hook;
        }

        /**
         * @param int $priority
         */
        public function setPriority(int $priority = 10)
        {
            $this->priority = $priority;
        }

        /**
         * @param int $accepted_args
         */
        public function setAcceptedArgs(int $accepted_args = 1)
        {
            $this->accepted_args = $accepted_args;
        }

        /**
         * @return mixed
         */
        public function execute()
        {
            $args = func_get_args();

            return call_user_func_array('do_action', $args);
        }

        /**
         * @return mixed
         */
        public function filter()
        {
            $args = func_get_args();

            return call_user_func_array('apply_filters', $args);
        }

        /**
         * @return string
         */
        public function getCurrentHook()
        {
            return current_filter();
        }

        /**
         * @param      $hook
         * @param bool $callback
         *
         * @return false|int
         */
        public function hasCallback($hook = null, $callback = false)
        {
            $hook = $hook ?? $this->hook;

            return has_filter($hook, $callback);
        }

        /**
         * @param     $hook
         * @param     $callback
         * @param int $priority
         *
         * @return bool
         */
        public function removeCallback($hook = null, $callback, $priority = null)
        {
            $hook     = $hook ?? $this->hook;
            $priority = $priority ?? $this->priority;
            if (is_array($callback)) {
                foreach ($callback as $c) {
                    remove_filter($hook, $c, $priority);
                }
            } else {
                remove_filter($hook, $callback, $priority);
            }

        }
    }