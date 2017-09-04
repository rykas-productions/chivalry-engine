<?php

namespace JBBCode\validators;

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'InputValidator.php';

/**
 * An InputValidator for urls. This can be used to make [url] bbcodes secure.
 *
 * @author jbowens
 * @since May 2013
 */
class UrlValidator implements \JBBCode\InputValidator
{

    /**
     * Returns true iff $input is a valid url.
     *
     * @param $input  the string to validate
     */
    public function validate($input)
    {
        $valid = filter_var($input, FILTER_VALIDATE_URL);
        // Simple workaround for protocol relative urls.
        // If sticking a protocol on the front makes it valid, assume it's valid
        if (!$valid)
            $valid = filter_var('http://test.com/' . $input, FILTER_VALIDATE_URL);
        return !!$valid;
    }
}
