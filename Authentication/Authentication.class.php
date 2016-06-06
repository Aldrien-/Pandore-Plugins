<?php

namespace Project\Plugins\Authentication;

use Kernel\Core as Core;
use Kernel\Services as Services;

/**
 * @brief The authentication plugin manages access to each action of each module.
 * 
 * @see Kernel::Core::Plugin.
 * @see Kernel::Core::Response.
 * @see Kernel::Services::Session.
 * 
 * @see https://github.com/fabpot/yaml.
 */
class Authentication extends Core\Plugin
{
    /**
     * @brief The plugin configuration path.
     */
    const CONFIG_PATH = 'Project/Plugins/Authentication/Config/Config.yaml';
    /**
     * @brief The name of the session variable associated with the current group.
     */
    const GROUP = 'Group';
    /**
     * @brief The YAML const associated with an authorized permission.
     */
    const YAML_KEY_AUTHORIZED = 'authorized';
    /**
     * @brief The YAML const associated with an unauthorized permission.
     */
    const YAML_KEY_UNAUTHORIZED = 'unauthorized';

    /**
     * @brief The name of the default group.
     * @var String.
     */
    private $defaultGroup;
    /**
     * @brief The current group name.
     * @var String.
     */
    private $group;
    /**
     * @brief Whether the plugin is enabled.
     * @var Bool.
     */
    private $isEnabled;
    /**
     * @brief The permissions array.
     * @var ArrayObject.
     */
    private $permissions;
    
    /**
     * @brief Check authentication during the pre dispatch step.
     */
    public function preDispatch()
    {
        // If the plugin is enabled.
        if($this->isEnabled)
        {
            // Get a part of the session dedicated to the authentication plugin.
            $session = new Services\Session('AuthenticationPlugin');

            // Get the current group name.
            try {
                $this->group = $session->__get(self::GROUP);
            } catch(\Exception $e) {
                $this->group = $this->defaultGroup;
            }
            
            // Get permissions associated the current module and the current action.
            try {
                // Get permissions associated with the current module.
                $modulePermissions = $this->permissions[strtolower($this->request->getModuleName())];

                // Get permissions associated with the current action.
                $permissions = $modulePermissions['actions'][strtolower($this->request->getActionName()) ? strtolower($this->request->getActionName()) : 'default'];
            } catch(\ErrorException $e) {
                try {
                    // Get global permissions of the module.
                    $permissions = $this->permissions[strtolower($this->request->getModuleName())]['permissions'];
                } catch(\ErrorException $e) {
                    // Set an empty permissions array.
                    $permissions = array();
                }
            }
            
            // If there is at least one permission define for the current module and the current action.
            if(!empty($permissions))
            {
                // If positive permissions are defined.
                if(array_key_exists(self::YAML_KEY_AUTHORIZED, $permissions))
                {
                    // If the group isn't in the authorized group array.
                    if(!in_array($this->group, $permissions[self::YAML_KEY_AUTHORIZED]))
                    {
                        // Set an error 403.
                        $this->response->setHttpStatusCode(403);
                    }
                }

                // If negative permissions are defined.
                if(array_key_exists(self::YAML_KEY_UNAUTHORIZED, $permissions))
                {
                    // If the group is in the unauthorized group array.
                    if(in_array($this->group, $permissions[self::YAML_KEY_UNAUTHORIZED]))
                    {
                        // Set an error 403.
                        $this->response->setHttpStatusCode(403);
                    }
                }
            }
        }
    }

    /**
     * @brief Initialize the plugin.
     */
    protected function init()
    {
        // Create a configuration object from the YAML configuration file.
        $config = Services\Yaml\Yaml::load(ROOT_PATH.self::CONFIG_PATH);

        // Set main attributes.
        $this->defaultGroup = $config['DefaultGroup'];
        $this->isEnabled = (bool) $config['IsEnabled'];
        $this->permissions = $this->arrayChangeKeyLowercaseRecursive($config['Modules']);
    }

    /**
     * @brief Changes the case of all keys in an array and its subarray to lowercase.
     * @param Array $array The array.
     * @return Array An array with its keys lower or uppercased
     */
    private function arrayChangeKeyLowercaseRecursive($array)
    {
        // Apply the given callback to the item.
        return array_map(function($item) {
            // If the item is an array.
            if(is_array($item))
            {
                // Changes the case of all keys in the array to lowercase.
                $item = $this->arrayChangeKeyLowercaseRecursive($item);
            }

            return $item;
        }, array_change_key_case($array));
    }
}

?>