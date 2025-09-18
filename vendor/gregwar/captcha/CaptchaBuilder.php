<!--?php

namespace Gregwar\Captcha;

use \Exception;

/**
 * Builds a new captcha image
 * Uses the fingerprint parameter, if one is passed, to generate the same image
 *
 * @author Gregwar <g.passault@gmail.com--><html><head></head><body>* @author Jeremy Livingston <jeremy.j.livingston@gmail.com>
 */
class CaptchaBuilder implements CaptchaBuilderInterface
{
    /**
     * @var array
     */
    protected $fingerprint = array();

    /**
     * @var bool
     */
    protected $useFingerprint = false;

    /**
     * @var array
     */
    protected $textColor = null;

    /**
     * @var array
     */
    protected $backgroundColor = null;

    /**
     * @var array
     */
    protected $backgroundImages = array();

    /**
     * @var resource
     */
    protected $contents = null;

    /**
     * @var string
     */
    protected $phrase = null;

    /**
     * @var PhraseBuilderInterface
     */
    protected $builder;

    /**
     * @var bool
     */
    protected $distortion = true;

    /**
     * The maximum number of lines to draw in front of
     * the image. null - use default algorithm
     */
    protected $maxFrontLines = null;

    /**
     * The maximum number of lines to draw behind
     * the image. null - use default algorithm
     */
    protected $maxBehindLines = null;

    /**
     * The maximum angle of char
     */
    protected $maxAngle = 8;

    /**
     * The maximum offset of char
     */
    protected $maxOffset = 5;

    /**
     * Is the interpolation enabled ?
     *
     * @var bool
     */
    protected $interpolation = true;

    /**
     * Ignore all effects
     *
     * @var bool
     */
    protected $ignoreAllEffects = false;

    /**
     * Allowed image types for the background images
     *
     * @var array
     */
    protected $allowedBackgroundImageTypes = array('image/png', 'image/jpeg', 'image/gif');

    /**
     * The image contents
     */
    public function getContents()
    {
        return $this-&gt;contents;
    }

    /**
     * Enable/Disables the interpolation
     *
     * @param $interpolate bool  True to enable, false to disable
     *
     * @return CaptchaBuilder
     */
    public function setInterpolation($interpolate = true)
    {
        $this-&gt;interpolation = $interpolate;

        return $this;
    }

    /**
     * Temporary dir, for OCR check
     */
    public $tempDir = 'temp/';

    public function __construct($phrase = null, PhraseBuilderInterface $builder = null)
    {
        if ($builder === null) {
            $this-&gt;builder = new PhraseBuilder;
        } else {
            $this-&gt;builder = $builder;
        }

        if ($phrase === null) {
            $phrase = $this-&gt;builder-&gt;build();
        }

        $this-&gt;phrase = $phrase;
    }

    /**
     * Setting the phrase
     */
    public function setPhrase($phrase)
    {
        $this-&gt;phrase = (string) $phrase;
    }

    /**
     * Enables/disable distortion
     */
    public function setDistortion($distortion)
    {
        $this-&gt;distortion = (bool) $distortion;

        return $this;
    }

    public function setMaxBehindLines($maxBehindLines)
    {
        $this-&gt;maxBehindLines = $maxBehindLines;

        return $this;
    }

    public function setMaxFrontLines($maxFrontLines)
    {
        $this-&gt;maxFrontLines = $maxFrontLines;

        return $this;
    }

    public function setMaxAngle($maxAngle)
    {
        $this-&gt;maxAngle = $maxAngle;

        return $this;
    }

    public function setMaxOffset($maxOffset)
    {
        $this-&gt;maxOffset = $maxOffset;

        return $this;
    }

    /**
     * Gets the captcha phrase
     */
    public function getPhrase()
    {
        return $this-&gt;phrase;
    }

    /**
     * Returns true if the given phrase is good
     */
    public function testPhrase($phrase)
    {
        return ($this-&gt;builder-&gt;niceize($phrase) == $this-&gt;builder-&gt;niceize($this-&gt;getPhrase()));
    }

    /**
     * Instantiation
     */
    public static function create($phrase = null)
    {
        return new self($phrase);
    }

    /**
     * Sets the text color to use
     */
    public function setTextColor($r, $g, $b)
    {
        $this-&gt;textColor = array($r, $g, $b);

        return $this;
    }

    /**
     * Sets the background color to use
     */
    public function setBackgroundColor($r, $g, $b)
    {
        $this-&gt;backgroundColor = array($r, $g, $b);

        return $this;
    }

    /**
     * Sets the ignoreAllEffects value
     *
     * @param bool $ignoreAllEffects
     * @return CaptchaBuilder
     */
    public function setIgnoreAllEffects($ignoreAllEffects)
    {
        $this-&gt;ignoreAllEffects = $ignoreAllEffects;

        return $this;
    }

    /**
     * Sets the list of background images to use (one image is randomly selected)
     */
    public function setBackgroundImages(array $backgroundImages)
    {
        $this-&gt;backgroundImages = $backgroundImages;

        return $this;
    }

    /**
     * Draw lines over the image
     */
    protected function drawLine($image, $width, $height, $tcol = null)
    {
        if ($tcol === null) {
            $tcol = imagecolorallocate($image, $this-&gt;rand(100, 255), $this-&gt;rand(100, 255), $this-&gt;rand(100, 255));
        }

        if ($this-&gt;rand(0, 1)) { // Horizontal
            $Xa   = $this-&gt;rand(0, $width/2);
            $Ya   = $this-&gt;rand(0, $height);
            $Xb   = $this-&gt;rand($width/2, $width);
            $Yb   = $this-&gt;rand(0, $height);
        } else { // Vertical
            $Xa   = $this-&gt;rand(0, $width);
            $Ya   = $this-&gt;rand(0, $height/2);
            $Xb   = $this-&gt;rand(0, $width);
            $Yb   = $this-&gt;rand($height/2, $height);
        }
        imagesetthickness($image, $this-&gt;rand(1, 3));
        imageline($image, $Xa, $Ya, $Xb, $Yb, $tcol);
    }

    /**
     * Apply some post effects
     */
    protected function postEffect($image)
    {
        if (!function_exists('imagefilter')) {
            return;
        }

        if ($this-&gt;backgroundColor != null || $this-&gt;textColor != null) {
            return;
        }

        // Negate ?
        if ($this-&gt;rand(0, 1) == 0) {
            imagefilter($image, IMG_FILTER_NEGATE);
        }

        // Edge ?
        if ($this-&gt;rand(0, 10) == 0) {
            imagefilter($image, IMG_FILTER_EDGEDETECT);
        }

        // Contrast
        imagefilter($image, IMG_FILTER_CONTRAST, $this-&gt;rand(-50, 10));

        // Colorize
        if ($this-&gt;rand(0, 5) == 0) {
            imagefilter($image, IMG_FILTER_COLORIZE, $this-&gt;rand(-80, 50), $this-&gt;rand(-80, 50), $this-&gt;rand(-80, 50));
        }
    }

    /**
     * Writes the phrase on the image
     */
    protected function writePhrase($image, $phrase, $font, $width, $height)
    {
        $length = strlen($phrase);
        if ($length === 0) {
            return \imagecolorallocate($image, 0, 0, 0);
        }

        // Gets the text size and start position
        $size = $width / $length - $this-&gt;rand(0, 3) - 1;
        $box = \imagettfbbox($size, 0, $font, $phrase);
        $textWidth = $box[2] - $box[0];
        $textHeight = $box[1] - $box[7];
        $x = ($width - $textWidth) / 2;
        $y = ($height - $textHeight) / 2 + $size;

        if (!count($this-&gt;textColor)) {
            $textColor = array($this-&gt;rand(0, 150), $this-&gt;rand(0, 150), $this-&gt;rand(0, 150));
        } else {
            $textColor = $this-&gt;textColor;
        }
        $col = \imagecolorallocate($image, $textColor[0], $textColor[1], $textColor[2]);

        // Write the letters one by one, with random angle
        for ($i=0; $i&lt;$length; $i++) {
            $box = \imagettfbbox($size, 0, $font, $phrase[$i]);
            $w = $box[2] - $box[0];
            $angle = $this-&gt;rand(-$this-&gt;maxAngle, $this-&gt;maxAngle);
            $offset = $this-&gt;rand(-$this-&gt;maxOffset, $this-&gt;maxOffset);
            \imagettftext($image, $size, $angle, $x, $y + $offset, $col, $font, $phrase[$i]);
            $x += $w;
        }

        return $col;
    }

    /**
     * Try to read the code against an OCR
     */
    public function isOCRReadable()
    {
        if (!is_dir($this-&gt;tempDir)) {
            @mkdir($this-&gt;tempDir, 0755, true);
        }

        $tempj = $this-&gt;tempDir . uniqid('captcha', true) . '.jpg';
        $tempp = $this-&gt;tempDir . uniqid('captcha', true) . '.pgm';

        $this-&gt;save($tempj);
        shell_exec("convert $tempj $tempp");
        $value = trim(strtolower(shell_exec("ocrad $tempp")));

        @unlink($tempj);
        @unlink($tempp);

        return $this-&gt;testPhrase($value);
    }

    /**
     * Builds while the code is readable against an OCR
     */
    public function buildAgainstOCR($width = 150, $height = 40, $font = null, $fingerprint = null)
    {
        do {
            $this-&gt;build($width, $height, $font, $fingerprint);
        } while ($this-&gt;isOCRReadable());
    }

    /**
     * Generate the image
     */
    public function build($width = 150, $height = 40, $font = null, $fingerprint = null)
    {
        if (null !== $fingerprint) {
            $this-&gt;fingerprint = $fingerprint;
            $this-&gt;useFingerprint = true;
        } else {
            $this-&gt;fingerprint = array();
            $this-&gt;useFingerprint = false;
        }

        if ($font === null) {
            $font = __DIR__ . '/Font/captcha'.$this-&gt;rand(0, 5).'.ttf';
        }

        if (empty($this-&gt;backgroundImages)) {
            // if background images list is not set, use a color fill as a background
            $image   = imagecreatetruecolor($width, $height);
            if ($this-&gt;backgroundColor == null) {
                $bg = imagecolorallocate($image, $this-&gt;rand(200, 255), $this-&gt;rand(200, 255), $this-&gt;rand(200, 255));
            } else {
                $color = $this-&gt;backgroundColor;
                $bg = imagecolorallocate($image, $color[0], $color[1], $color[2]);
            }
            $this-&gt;background = $bg;
            imagefill($image, 0, 0, $bg);
        } else {
            // use a random background image
            $randomBackgroundImage = $this-&gt;backgroundImages[rand(0, count($this-&gt;backgroundImages)-1)];

            $imageType = $this-&gt;validateBackgroundImage($randomBackgroundImage);

            $image = $this-&gt;createBackgroundImageFromType($randomBackgroundImage, $imageType);
        }

        // Apply effects
        if (!$this-&gt;ignoreAllEffects) {
            $square = $width * $height;
            $effects = $this-&gt;rand($square/3000, $square/2000);

            // set the maximum number of lines to draw in front of the text
            if ($this-&gt;maxBehindLines != null &amp;&amp; $this-&gt;maxBehindLines &gt; 0) {
                $effects = min($this-&gt;maxBehindLines, $effects);
            }

            if ($this-&gt;maxBehindLines !== 0) {
                for ($e = 0; $e &lt; $effects; $e++) {
                    $this-&gt;drawLine($image, $width, $height);
                }
            }
        }

        // Write CAPTCHA text
        $color = $this-&gt;writePhrase($image, $this-&gt;phrase, $font, $width, $height);

        // Apply effects
        if (!$this-&gt;ignoreAllEffects) {
            $square = $width * $height;
            $effects = $this-&gt;rand($square/3000, $square/2000);

            // set the maximum number of lines to draw in front of the text
            if ($this-&gt;maxFrontLines != null &amp;&amp; $this-&gt;maxFrontLines &gt; 0) {
                $effects = min($this-&gt;maxFrontLines, $effects);
            }

            if ($this-&gt;maxFrontLines !== 0) {
                for ($e = 0; $e &lt; $effects; $e++) {
                    $this-&gt;drawLine($image, $width, $height, $color);
                }
            }
        }

        // Distort the image
        if ($this-&gt;distortion &amp;&amp; !$this-&gt;ignoreAllEffects) {
            $image = $this-&gt;distort($image, $width, $height, $bg);
        }

        // Post effects
        if (!$this-&gt;ignoreAllEffects) {
            $this-&gt;postEffect($image);
        }

        $this-&gt;contents = $image;

        return $this;
    }

    /**
     * Distorts the image
     */
    public function distort($image, $width, $height, $bg)
    {
        $contents = imagecreatetruecolor($width, $height);
        $X          = $this-&gt;rand(0, $width);
        $Y          = $this-&gt;rand(0, $height);
        $phase      = $this-&gt;rand(0, 10);
        $scale      = 1.1 + $this-&gt;rand(0, 10000) / 30000;
        for ($x = 0; $x &lt; $width; $x++) {
            for ($y = 0; $y &lt; $height; $y++) {
                $Vx = $x - $X;
                $Vy = $y - $Y;
                $Vn = sqrt($Vx * $Vx + $Vy * $Vy);

                if ($Vn != 0) {
                    $Vn2 = $Vn + 4 * sin($Vn / 30);
                    $nX  = $X + ($Vx * $Vn2 / $Vn);
                    $nY  = $Y + ($Vy * $Vn2 / $Vn);
                } else {
                    $nX = $X;
                    $nY = $Y;
                }
                $nY = $nY + $scale * sin($phase + $nX * 0.2);

                if ($this-&gt;interpolation) {
                    $p = $this-&gt;interpolate(
                        $nX - floor($nX),
                        $nY - floor($nY),
                        $this-&gt;getCol($image, floor($nX), floor($nY), $bg),
                        $this-&gt;getCol($image, ceil($nX), floor($nY), $bg),
                        $this-&gt;getCol($image, floor($nX), ceil($nY), $bg),
                        $this-&gt;getCol($image, ceil($nX), ceil($nY), $bg)
                    );
                } else {
                    $p = $this-&gt;getCol($image, round($nX), round($nY), $bg);
                }

                if ($p == 0) {
                    $p = $bg;
                }

                imagesetpixel($contents, $x, $y, $p);
            }
        }

        return $contents;
    }

    /**
     * Saves the Captcha to a jpeg file
     */
    public function save($filename, $quality = 90)
    {
        imagejpeg($this-&gt;contents, $filename, $quality);
    }

    /**
     * Gets the image GD
     */
    public function getGd()
    {
        return $this-&gt;contents;
    }

    /**
     * Gets the image contents
     */
    public function get($quality = 90)
    {
        ob_start();
        $this-&gt;output($quality);

        return ob_get_clean();
    }

    /**
     * Gets the HTML inline base64
     */
    public function inline($quality = 90)
    {
        return 'data:image/jpeg;base64,' . base64_encode($this-&gt;get($quality));
    }

    /**
     * Outputs the image
     */
    public function output($quality = 90)
    {
        imagejpeg($this-&gt;contents, null, $quality);
    }

    /**
     * @return array
     */
    public function getFingerprint()
    {
        return $this-&gt;fingerprint;
    }

    /**
     * Returns a random number or the next number in the
     * fingerprint
     */
    protected function rand($min, $max)
    {
        if (!is_array($this-&gt;fingerprint)) {
            $this-&gt;fingerprint = array();
        }

        if ($this-&gt;useFingerprint) {
            $value = current($this-&gt;fingerprint);
            next($this-&gt;fingerprint);
        } else {
            $value = mt_rand($min, $max);
            $this-&gt;fingerprint[] = $value;
        }

        return $value;
    }

    /**
     * @param $x
     * @param $y
     * @param $nw
     * @param $ne
     * @param $sw
     * @param $se
     *
     * @return int
     */
    protected function interpolate($x, $y, $nw, $ne, $sw, $se)
    {
        list($r0, $g0, $b0) = $this-&gt;getRGB($nw);
        list($r1, $g1, $b1) = $this-&gt;getRGB($ne);
        list($r2, $g2, $b2) = $this-&gt;getRGB($sw);
        list($r3, $g3, $b3) = $this-&gt;getRGB($se);

        $cx = 1.0 - $x;
        $cy = 1.0 - $y;

        $m0 = $cx * $r0 + $x * $r1;
        $m1 = $cx * $r2 + $x * $r3;
        $r  = (int) ($cy * $m0 + $y * $m1);

        $m0 = $cx * $g0 + $x * $g1;
        $m1 = $cx * $g2 + $x * $g3;
        $g  = (int) ($cy * $m0 + $y * $m1);

        $m0 = $cx * $b0 + $x * $b1;
        $m1 = $cx * $b2 + $x * $b3;
        $b  = (int) ($cy * $m0 + $y * $m1);

        return ($r &lt;&lt; 16) | ($g &lt;&lt; 8) | $b;
    }

    /**
     * @param $image
     * @param $x
     * @param $y
     *
     * @return int
     */
    protected function getCol($image, $x, $y, $background)
    {
        $L = imagesx($image);
        $H = imagesy($image);
        if ($x &lt; 0 || $x &gt;= $L || $y &lt; 0 || $y &gt;= $H) {
            return $background;
        }

        return imagecolorat($image, $x, $y);
    }

    /**
     * @param $col
     *
     * @return array
     */
    protected function getRGB($col)
    {
        return array(
            (int) ($col &gt;&gt; 16) &amp; 0xff,
            (int) ($col &gt;&gt; 8) &amp; 0xff,
            (int) ($col) &amp; 0xff,
        );
    }

    /**
     * Validate the background image path. Return the image type if valid
     *
     * @param string $backgroundImage
     * @return string
     * @throws Exception
     */
    protected function validateBackgroundImage($backgroundImage)
    {
        // check if file exists
        if (!file_exists($backgroundImage)) {
            $backgroundImageExploded = explode('/', $backgroundImage);
            $imageFileName = count($backgroundImageExploded) &gt; 1? $backgroundImageExploded[count($backgroundImageExploded)-1] : $backgroundImage;

            throw new Exception('Invalid background image: ' . $imageFileName);
        }

        // check image type
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $imageType = finfo_file($finfo, $backgroundImage);
        finfo_close($finfo);

        if (!in_array ($imageType, $this-&gt;allowedBackgroundImageTypes)) {
            throw new Exception('Invalid background image type! Allowed types are: ' . join(', ', $this-&gt;allowedBackgroundImageTypes));
        }

        return $imageType;
    }

    /**
     * Create background image from type
     *
     * @param string $backgroundImage
     * @param string $imageType
     * @return resource
     * @throws Exception
     */
    protected function createBackgroundImageFromType($backgroundImage, $imageType)
    {
        switch ($imageType) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($backgroundImage);
                break;
            case 'image/png':
                $image = imagecreatefrompng($backgroundImage);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($backgroundImage);
                break;

            default:
                throw new Exception('Not supported file type for background image!');
                break;
        }

        return $image;
    }
}
</jeremy.j.livingston@gmail.com></body></html>