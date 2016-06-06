<?php

namespace Project\Plugins\SessionExpiration;

use Kernel\Core as Core;
use Kernel\Services as Services;
use Project\Plugins\SessionExpiration\Exceptions as Exceptions;

/**
 * @brief The session expiration plugin decrements the number of remaining access of a part or some values of the session in order to make the access limitation mechanism works.
 *
 * @see Kernel::Core::Plugin.
 * @see Kernel::Services::Session.
 */
class SessionExpiration extends Core\Plugin
{
    /**
     * @brief Execute a pre dispatch.
     *
     * @exception Plugins::SessionExpiration::Exceptions::SessionException When the session start has failed.
     * @exception Plugins::SessionExpiration::Exceptions::SessionException When something bad occurs during session expiration management.
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

        try {
            foreach($_SESSION as $namespace)
            {
                // Get the current number of remaining access of the part of the session.
                $hops = $namespace->offsetGet(Services\Session::EXP_HOP);

                // If the number of remaining access is defined and not 0.
                if($hops !== null && $hops > 0)
                {
                    // Decrements the number of remaining access.
                    $hops--;

                    // Set the number of remaining access.
                    $namespace->offsetSet(Services\Session::EXP_HOP, $hops);
                }

                // Get an iterator on session values.
                $iterator = $namespace->offsetGet(Services\Session::DATA)->getIterator();

                while($iterator->valid())
                {
                    // Get the current number of remaining access of the value.
                    $hops = $iterator->current()->offsetGet(Services\Session::EXP_HOP);

                    // If the number of remaining access of the value is defined and not 0.
                    if($hops !== null && $hops > 0)
                    {
                        // Decrements the number of remaining access.
                        $hops--;

                        // Set the number of remaining access.
                        $iterator->current()->offsetSet(Services\Session::EXP_HOP, $hops);
                    }

                    $iterator->next();
                }
            }
        } catch(\Exceptions $exception) {
            throw new Exceptions\SessionException('Something bad occurs during sessions expiration management.');
        }
    }
}