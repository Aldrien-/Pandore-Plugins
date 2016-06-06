<?php

namespace Project\Plugins\SessionStarter;

use Kernel\Core as Core;
use Kernel\Services as Services;
use Project\Plugins\SessionStarter\Exceptions as Exceptions;

/**
 * @brief The session starter plugin starts the session in order to avoid to start the session after response headers have already been sent.
 *
 * @see Kernel::Core::Plugin.
 * @see Kernel::Services::Session.
 */
class SessionStarter extends Core\Plugin
{
    /**
     * @brief Starts session during the pre dispatch step.
     *
     * @exception Plugins::SessionStarter::Exceptions::SessionException When the session start has failed.
     */
    public function preDispatch()
    {
        // If the session isn't started yet.
        if(session_id() == '')
        {
            // Set the session name.
            session_name($_SERVER['HTTP_HOST']);

            // If the session start has failed.
            if(session_start() === false)
            {
                throw new Exceptions\SessionException('Session start has failed.');
            }
        }
    }
}