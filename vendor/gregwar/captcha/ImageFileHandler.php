<!--?php

namespace Gregwar\Captcha;

use Symfony\Component\Finder\Finder;

/**
 * Handles actions related to captcha image files including saving and garbage collection
 *
 * @author Gregwar <g.passault@gmail.com--><html><head></head><body>* @author Jeremy Livingston <jeremy@quizzle.com>
 */
class ImageFileHandler
{
    /**
     * Name of folder for captcha images
     * @var string
     */
    protected $imageFolder;

    /**
     * Absolute path to public web folder
     * @var string
     */
    protected $webPath;

    /**
     * Frequency of garbage collection in fractions of 1
     * @var int
     */
    protected $gcFreq;

    /**
     * Maximum age of images in minutes
     * @var int
     */
    protected $expiration;

    /**
     * @param $imageFolder
     * @param $webPath
     * @param $gcFreq
     * @param $expiration
     */
    public function __construct($imageFolder, $webPath, $gcFreq, $expiration)
    {
        $this-&gt;imageFolder      = $imageFolder;
        $this-&gt;webPath          = $webPath;
        $this-&gt;gcFreq           = $gcFreq;
        $this-&gt;expiration       = $expiration;
    }

    /**
     * Saves the provided image content as a file
     *
     * @param string $contents
     *
     * @return string
     */
    public function saveAsFile($contents)
    {
        $this-&gt;createFolderIfMissing();

        $filename = md5(uniqid()) . '.jpg';
        $filePath = $this-&gt;webPath . '/' . $this-&gt;imageFolder . '/' . $filename;
        imagejpeg($contents, $filePath, 15);

        return '/' . $this-&gt;imageFolder . '/' . $filename;
    }

    /**
     * Randomly runs garbage collection on the image directory
     *
     * @return bool
     */
    public function collectGarbage()
    {
        if (!mt_rand(1, $this-&gt;gcFreq) == 1) {
            return false;
        }

        $this-&gt;createFolderIfMissing();

        $finder = new Finder();
        $criteria = sprintf('&lt;= now - %s minutes', $this-&gt;expiration);
        $finder-&gt;in($this-&gt;webPath . '/' . $this-&gt;imageFolder)
            -&gt;date($criteria);

        foreach($finder-&gt;files() as $file) {
            unlink($file-&gt;getPathname());
        }

        return true;
    }

    /**
     * Creates the folder if it doesn't exist
     */
    protected function createFolderIfMissing()
    {
        if (!file_exists($this-&gt;webPath . '/' . $this-&gt;imageFolder)) {
            mkdir($this-&gt;webPath . '/' . $this-&gt;imageFolder, 0755);
        }
    }
}

</jeremy@quizzle.com></body></html>