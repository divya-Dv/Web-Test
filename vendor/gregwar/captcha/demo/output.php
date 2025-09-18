<!--?php

include(__DIR__.'/../CaptchaBuilderInterface.php');
include(__DIR__.'/../PhraseBuilderInterface.php');
include(__DIR__.'/../CaptchaBuilder.php');
include(__DIR__.'/../PhraseBuilder.php');

use Gregwar\Captcha\CaptchaBuilder;

header('Content-type: image/jpeg');

CaptchaBuilder::create()
    ---><html><head></head><body>build()
    -&gt;output()
;
</body></html>