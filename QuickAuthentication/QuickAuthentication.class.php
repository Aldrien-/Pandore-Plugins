<?php

namespace Project\Plugins\QuickAuthentication;

use Kernel\Core as Core;
use Kernel\Services as Services;

/**
 * @brief The quick authenticate plugin defines a quick way to authenticate users. It is particularly useful in development environment.
 *
 * @see Kernel::Core::Plugin.
 * @see Kernel::Services::Session.
 */
class QuickAuthentication extends Core\Plugin
{
    /**
     * @brief The name of the session variable associated with the current group.
     */
    const GROUP = 'Group';

    /**
     * @brief Execute a pre dispatch.
     */
    public function preDispatch()
    {
        // Get the part of the session dedicated to the authentication plugin.
        $session = new Services\Session('AuthenticationPlugin');

        // Whether the application is run in debug.
        try {
            $debug = (bool) $this->config->getValue('debug');
        } catch(\Exception $exception) {
            $debug = false;
        }

        // Whether the application is run in debug.
        if($debug)
        {
            // Set the current user group.
            $session->__set(self::GROUP, 'Admin');
        }
        else
        {
            // Set the current user group.
            $session->__set(self::GROUP, 'Guest');
        }
    }
}