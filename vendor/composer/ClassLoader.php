<!--?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de--><html><head></head><body>*     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Autoload;

/**
 * ClassLoader implements a PSR-0, PSR-4 and classmap class loader.
 *
 *     $loader = new \Composer\Autoload\ClassLoader();
 *
 *     // register classes with namespaces
 *     $loader-&gt;add('Symfony\Component', __DIR__.'/component');
 *     $loader-&gt;add('Symfony',           __DIR__.'/framework');
 *
 *     // activate the autoloader
 *     $loader-&gt;register();
 *
 *     // to enable searching the include path (eg. for PEAR packages)
 *     $loader-&gt;setUseIncludePath(true);
 *
 * In this example, if you try to use a class in the Symfony\Component
 * namespace or one of its children (Symfony\Component\Console for instance),
 * the autoloader will first look for the class under the component/
 * directory, and it will then fallback to the framework/ directory if not
 * found before giving up.
 *
 * This class is loosely based on the Symfony UniversalClassLoader.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 * @see    http://www.php-fig.org/psr/psr-0/
 * @see    http://www.php-fig.org/psr/psr-4/
 */
class ClassLoader
{
    // PSR-4
    private $prefixLengthsPsr4 = array();
    private $prefixDirsPsr4 = array();
    private $fallbackDirsPsr4 = array();

    // PSR-0
    private $prefixesPsr0 = array();
    private $fallbackDirsPsr0 = array();

    private $useIncludePath = false;
    private $classMap = array();
    private $classMapAuthoritative = false;
    private $missingClasses = array();
    private $apcuPrefix;

    public function getPrefixes()
    {
        if (!empty($this-&gt;prefixesPsr0)) {
            return call_user_func_array('array_merge', $this-&gt;prefixesPsr0);
        }

        return array();
    }

    public function getPrefixesPsr4()
    {
        return $this-&gt;prefixDirsPsr4;
    }

    public function getFallbackDirs()
    {
        return $this-&gt;fallbackDirsPsr0;
    }

    public function getFallbackDirsPsr4()
    {
        return $this-&gt;fallbackDirsPsr4;
    }

    public function getClassMap()
    {
        return $this-&gt;classMap;
    }

    /**
     * @param array $classMap Class to filename map
     */
    public function addClassMap(array $classMap)
    {
        if ($this-&gt;classMap) {
            $this-&gt;classMap = array_merge($this-&gt;classMap, $classMap);
        } else {
            $this-&gt;classMap = $classMap;
        }
    }

    /**
     * Registers a set of PSR-0 directories for a given prefix, either
     * appending or prepending to the ones previously set for this prefix.
     *
     * @param string       $prefix  The prefix
     * @param array|string $paths   The PSR-0 root directories
     * @param bool         $prepend Whether to prepend the directories
     */
    public function add($prefix, $paths, $prepend = false)
    {
        if (!$prefix) {
            if ($prepend) {
                $this-&gt;fallbackDirsPsr0 = array_merge(
                    (array) $paths,
                    $this-&gt;fallbackDirsPsr0
                );
            } else {
                $this-&gt;fallbackDirsPsr0 = array_merge(
                    $this-&gt;fallbackDirsPsr0,
                    (array) $paths
                );
            }

            return;
        }

        $first = $prefix[0];
        if (!isset($this-&gt;prefixesPsr0[$first][$prefix])) {
            $this-&gt;prefixesPsr0[$first][$prefix] = (array) $paths;

            return;
        }
        if ($prepend) {
            $this-&gt;prefixesPsr0[$first][$prefix] = array_merge(
                (array) $paths,
                $this-&gt;prefixesPsr0[$first][$prefix]
            );
        } else {
            $this-&gt;prefixesPsr0[$first][$prefix] = array_merge(
                $this-&gt;prefixesPsr0[$first][$prefix],
                (array) $paths
            );
        }
    }

    /**
     * Registers a set of PSR-4 directories for a given namespace, either
     * appending or prepending to the ones previously set for this namespace.
     *
     * @param string       $prefix  The prefix/namespace, with trailing '\\'
     * @param array|string $paths   The PSR-4 base directories
     * @param bool         $prepend Whether to prepend the directories
     *
     * @throws \InvalidArgumentException
     */
    public function addPsr4($prefix, $paths, $prepend = false)
    {
        if (!$prefix) {
            // Register directories for the root namespace.
            if ($prepend) {
                $this-&gt;fallbackDirsPsr4 = array_merge(
                    (array) $paths,
                    $this-&gt;fallbackDirsPsr4
                );
            } else {
                $this-&gt;fallbackDirsPsr4 = array_merge(
                    $this-&gt;fallbackDirsPsr4,
                    (array) $paths
                );
            }
        } elseif (!isset($this-&gt;prefixDirsPsr4[$prefix])) {
            // Register directories for a new namespace.
            $length = strlen($prefix);
            if ('\\' !== $prefix[$length - 1]) {
                throw new \InvalidArgumentException("A non-empty PSR-4 prefix must end with a namespace separator.");
            }
            $this-&gt;prefixLengthsPsr4[$prefix[0]][$prefix] = $length;
            $this-&gt;prefixDirsPsr4[$prefix] = (array) $paths;
        } elseif ($prepend) {
            // Prepend directories for an already registered namespace.
            $this-&gt;prefixDirsPsr4[$prefix] = array_merge(
                (array) $paths,
                $this-&gt;prefixDirsPsr4[$prefix]
            );
        } else {
            // Append directories for an already registered namespace.
            $this-&gt;prefixDirsPsr4[$prefix] = array_merge(
                $this-&gt;prefixDirsPsr4[$prefix],
                (array) $paths
            );
        }
    }

    /**
     * Registers a set of PSR-0 directories for a given prefix,
     * replacing any others previously set for this prefix.
     *
     * @param string       $prefix The prefix
     * @param array|string $paths  The PSR-0 base directories
     */
    public function set($prefix, $paths)
    {
        if (!$prefix) {
            $this-&gt;fallbackDirsPsr0 = (array) $paths;
        } else {
            $this-&gt;prefixesPsr0[$prefix[0]][$prefix] = (array) $paths;
        }
    }

    /**
     * Registers a set of PSR-4 directories for a given namespace,
     * replacing any others previously set for this namespace.
     *
     * @param string       $prefix The prefix/namespace, with trailing '\\'
     * @param array|string $paths  The PSR-4 base directories
     *
     * @throws \InvalidArgumentException
     */
    public function setPsr4($prefix, $paths)
    {
        if (!$prefix) {
            $this-&gt;fallbackDirsPsr4 = (array) $paths;
        } else {
            $length = strlen($prefix);
            if ('\\' !== $prefix[$length - 1]) {
                throw new \InvalidArgumentException("A non-empty PSR-4 prefix must end with a namespace separator.");
            }
            $this-&gt;prefixLengthsPsr4[$prefix[0]][$prefix] = $length;
            $this-&gt;prefixDirsPsr4[$prefix] = (array) $paths;
        }
    }

    /**
     * Turns on searching the include path for class files.
     *
     * @param bool $useIncludePath
     */
    public function setUseIncludePath($useIncludePath)
    {
        $this-&gt;useIncludePath = $useIncludePath;
    }

    /**
     * Can be used to check if the autoloader uses the include path to check
     * for classes.
     *
     * @return bool
     */
    public function getUseIncludePath()
    {
        return $this-&gt;useIncludePath;
    }

    /**
     * Turns off searching the prefix and fallback directories for classes
     * that have not been registered with the class map.
     *
     * @param bool $classMapAuthoritative
     */
    public function setClassMapAuthoritative($classMapAuthoritative)
    {
        $this-&gt;classMapAuthoritative = $classMapAuthoritative;
    }

    /**
     * Should class lookup fail if not found in the current class map?
     *
     * @return bool
     */
    public function isClassMapAuthoritative()
    {
        return $this-&gt;classMapAuthoritative;
    }

    /**
     * APCu prefix to use to cache found/not-found classes, if the extension is enabled.
     *
     * @param string|null $apcuPrefix
     */
    public function setApcuPrefix($apcuPrefix)
    {
        $this-&gt;apcuPrefix = function_exists('apcu_fetch') &amp;&amp; ini_get('apc.enabled') ? $apcuPrefix : null;
    }

    /**
     * The APCu prefix in use, or null if APCu caching is not enabled.
     *
     * @return string|null
     */
    public function getApcuPrefix()
    {
        return $this-&gt;apcuPrefix;
    }

    /**
     * Registers this instance as an autoloader.
     *
     * @param bool $prepend Whether to prepend the autoloader or not
     */
    public function register($prepend = false)
    {
        spl_autoload_register(array($this, 'loadClass'), true, $prepend);
    }

    /**
     * Unregisters this instance as an autoloader.
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * Loads the given class or interface.
     *
     * @param  string    $class The name of the class
     * @return bool|null True if loaded, null otherwise
     */
    public function loadClass($class)
    {
        if ($file = $this-&gt;findFile($class)) {
            includeFile($file);

            return true;
        }
    }

    /**
     * Finds the path to the file where the class is defined.
     *
     * @param string $class The name of the class
     *
     * @return string|false The path if found, false otherwise
     */
    public function findFile($class)
    {
        // class map lookup
        if (isset($this-&gt;classMap[$class])) {
            return $this-&gt;classMap[$class];
        }
        if ($this-&gt;classMapAuthoritative || isset($this-&gt;missingClasses[$class])) {
            return false;
        }
        if (null !== $this-&gt;apcuPrefix) {
            $file = apcu_fetch($this-&gt;apcuPrefix.$class, $hit);
            if ($hit) {
                return $file;
            }
        }

        $file = $this-&gt;findFileWithExtension($class, '.php');

        // Search for Hack files if we are running on HHVM
        if (false === $file &amp;&amp; defined('HHVM_VERSION')) {
            $file = $this-&gt;findFileWithExtension($class, '.hh');
        }

        if (null !== $this-&gt;apcuPrefix) {
            apcu_add($this-&gt;apcuPrefix.$class, $file);
        }

        if (false === $file) {
            // Remember that this class does not exist.
            $this-&gt;missingClasses[$class] = true;
        }

        return $file;
    }

    private function findFileWithExtension($class, $ext)
    {
        // PSR-4 lookup
        $logicalPathPsr4 = strtr($class, '\\', DIRECTORY_SEPARATOR) . $ext;

        $first = $class[0];
        if (isset($this-&gt;prefixLengthsPsr4[$first])) {
            $subPath = $class;
            while (false !== $lastPos = strrpos($subPath, '\\')) {
                $subPath = substr($subPath, 0, $lastPos);
                $search = $subPath.'\\';
                if (isset($this-&gt;prefixDirsPsr4[$search])) {
                    foreach ($this-&gt;prefixDirsPsr4[$search] as $dir) {
                        $length = $this-&gt;prefixLengthsPsr4[$first][$search];
                        if (file_exists($file = $dir . DIRECTORY_SEPARATOR . substr($logicalPathPsr4, $length))) {
                            return $file;
                        }
                    }
                }
            }
        }

        // PSR-4 fallback dirs
        foreach ($this-&gt;fallbackDirsPsr4 as $dir) {
            if (file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr4)) {
                return $file;
            }
        }

        // PSR-0 lookup
        if (false !== $pos = strrpos($class, '\\')) {
            // namespaced class name
            $logicalPathPsr0 = substr($logicalPathPsr4, 0, $pos + 1)
                . strtr(substr($logicalPathPsr4, $pos + 1), '_', DIRECTORY_SEPARATOR);
        } else {
            // PEAR-like class name
            $logicalPathPsr0 = strtr($class, '_', DIRECTORY_SEPARATOR) . $ext;
        }

        if (isset($this-&gt;prefixesPsr0[$first])) {
            foreach ($this-&gt;prefixesPsr0[$first] as $prefix =&gt; $dirs) {
                if (0 === strpos($class, $prefix)) {
                    foreach ($dirs as $dir) {
                        if (file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr0)) {
                            return $file;
                        }
                    }
                }
            }
        }

        // PSR-0 fallback dirs
        foreach ($this-&gt;fallbackDirsPsr0 as $dir) {
            if (file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr0)) {
                return $file;
            }
        }

        // PSR-0 include paths.
        if ($this-&gt;useIncludePath &amp;&amp; $file = stream_resolve_include_path($logicalPathPsr0)) {
            return $file;
        }

        return false;
    }
}

/**
 * Scope isolated include.
 *
 * Prevents access to $this/self from included files.
 */
function includeFile($file)
{
    include $file;
}
</j.boggiano@seld.be></fabien@symfony.com></j.boggiano@seld.be></body></html>