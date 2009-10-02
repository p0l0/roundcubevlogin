<?php
/**
 * Vlogin Roundcube plugin
 *
 * @version 1.0
 * @url http://sourceforge.net/projects/roundcubevlogin/
 * @author Marco Neumann <webcoder_at_binware_dot_org>
 * @copyright Copyright (c) 2009, Marco Neumann
 * Licensed under the BSD License. For full terms see the file COPYING.
 * 
 * $Id$
 */
class vlogin extends rcube_plugin
{
    /**
     * Variable to store domain
     * 
     * @var string
     */
    private $_domain;
    
    /**
     * Start plugin and register hook
     */
    public function init()
    {
        $this->add_hook('authenticate', array($this, 'authenticate'));
    }

    /**
     * Change login data if needed
     * 
     * @param array $data
     * @return array
     */
    public function authenticate($data)
    {
        /**
         * Get rcmail instance
         */
        $rcmail = rcmail::get_instance();
        
        /**
         * Merge plugin config with RC Config
         */
        $this->load_config();
        
        /**
         * Check if we have to change login data
         */
        if ($rcmail->config->get('vlogin_adddomain')) {
            /**
             * Check if we need to add domain
             */
            if (!strstr($data['user'], '@')) {
                $data['user'] = $data['user'] . '@' . $this->_getDomain();
            } else if (!$rcmail->config->get('vlogin_adddomain_dif')) {
                /**
                 * Check if we need to deny login because invalid domain
                 */
                if ($this->_getDomain() != substr($data['user'], strrpos($data['user'], '@') + 1)) {
                    $data['abort'] = true;
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Extracts domain from URL
     * 
     * @return string
     */
    private function _getDomain()
    {
        if (!empty($this->_domain)) {
            return $this->_domain;
        }
        
        /**
         * Get rcmail instance
         */
        $rcmail = rcmail::get_instance();
        
        $httpHost = explode('.', $_SERVER['HTTP_HOST']);
        $domain = array();
        $dotCount = count($httpHost);
        for ($i=$rcmail->config->get('vlogin_adddomain_dots'); $i>0; $i--) {
            $domain[] = $httpHost[($dotCount - $i)];
        }
        
        $this->_domain = implode('.', $domain);
        
        return $this->_domain;
    }
}

