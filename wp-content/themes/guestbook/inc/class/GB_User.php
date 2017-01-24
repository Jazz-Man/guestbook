<?php
    namespace GB;

    /**
     * Class GB_User
     *
     * @package GB
     */
    class GB_User
    {

        public $_user;

        /**
         * GB_User constructor.
         *
         * @param null $user
         */
        public function __construct($user = null)
        {
            if (null !== $user) {
                switch ($user) {
                    case is_numeric($user):
                        $current_user = get_user_by('id', $user);
                        break;
                    case is_email($user):
                        $current_user = get_user_by('email', $user);
                        break;
                }
            } else {
                $current_user = wp_get_current_user();
            }
            $this->_user = $current_user;
        }

        /**
         * @return mixed
         */
        public function getId()
        {
            return $this->getField('ID');
        }

        /**
         * @param $key
         *
         * @return mixed
         */
        public function getField($key)
        {
            return $this->_user->$key;
        }

        /**
         * @param $key
         * @param $value
         */
        public function setMeta($key, $value)
        {
            update_user_meta($this->getId(), $key, $value);
        }

        /**
         * @return mixed
         */
        public function getDisplayName()
        {
            return $this->getField('display_name');
        }

        /**
         * @return string
         */
        public function getUserRoleName()
        {
            $editable_roles = array_reverse(get_editable_roles());
            $name           = '';
            foreach ($editable_roles as $role => $details) {
                if ($role === $this->getUserRole()) {
                    $name = translate_user_role($details['name']);
                }
            }

            return $name;
        }

        /**
         * @return mixed
         */
        public function getUserRole()
        {
            return $this->getField('roles')[0];
        }

        /**
         * @return mixed
         */
        public function getFirstName()
        {
            return $this->getMeta('first_name');
        }

        /**
         * @param $key
         *
         * @return mixed
         */
        public function getMeta($key)
        {
            return get_user_meta($this->getId(), $key, true);
        }

        /**
         * @return mixed
         */
        public function getLastName()
        {
            return $this->getMeta('last_name');
        }

        /**
         * @return bool
         */
        public function loggedIn()
        {
            return $this->getId() ? true : false;
        }

        /**
         * @return mixed
         */
        public function getUserLogin()
        {
            return $this->getField('user_login');
        }

        /**
         * @return mixed
         */
        public function getEmail()
        {
            return $this->getField('user_email');
        }

        /**
         * @param array $args
         * @param int   $size
         *
         * @return false|string
         */
        public function getAvatar(array $args = [], $size = 96)
        {
            $_args = [
                'class' => 'img-responsive img-circle center-block'
            ];
            $_args = wp_parse_args($args, $_args);

            return get_avatar($this->getId(), $size, '', '', $_args);
        }

        /**
         * @return mixed
         */
        public function getDescription()
        {
            return $this->getMeta('description');
        }
    }