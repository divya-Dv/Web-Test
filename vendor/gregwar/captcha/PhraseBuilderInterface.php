<!--?php

namespace Gregwar\Captcha;

/**
 * Interface for the PhraseBuilder
 *
 * @author Gregwar <g.passault@gmail.com--><html><head></head><body>*/
interface PhraseBuilderInterface
{
    /**
     * Generates  random phrase of given length with given charset
     */
    public function build($length, $charset);

    /**
     * "Niceize" a code
     */
    public function niceize($str);
}
</body></html>