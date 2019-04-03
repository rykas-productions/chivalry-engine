<?php

namespace AG;

use Exception;

/**
 * Message
 *
 * @author Alexandr Gorlov <a.gorlov@gmail.com>
 */
interface Msg
{
    /**
     * Send message
     *
     * @throws Exception
     */
    public function send(): void;
}