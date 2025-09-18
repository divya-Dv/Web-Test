<!--?php
/**
 * PHPMailer - PHP email creation and transport class.
 * PHP Version 5.4
 * @package PHPMailer
 * @link https://github.com/PHPMailer/PHPMailer/ The PHPMailer GitHub project
 * @author Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk--><html><head></head><body>* @author Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author Brent R. Matzelle (original founder)
 * @copyright 2012 - 2014 Marcus Bointon
 * @copyright 2010 - 2012 Jim Jagielski
 * @copyright 2004 - 2009 Andy Prevost
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note This program is distributed in the hope that it will be useful - WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * PHPMailerOAuth - PHPMailer subclass adding OAuth support.
 * @package PHPMailer
 * @author @sherryl4george
 * @author Marcus Bointon (@Synchro) <phpmailer@synchromedia.co.uk>
 */
class PHPMailerOAuth extends PHPMailer
{
    /**
     * The OAuth user's email address
     * @var string
     */
    public $oauthUserEmail = '';

    /**
     * The OAuth refresh token
     * @var string
     */
    public $oauthRefreshToken = '';

    /**
     * The OAuth client ID
     * @var string
     */
    public $oauthClientId = '';

    /**
     * The OAuth client secret
     * @var string
     */
    public $oauthClientSecret = '';

    /**
     * An instance of the PHPMailerOAuthGoogle class.
     * @var PHPMailerOAuthGoogle
     * @access protected
     */
    protected $oauth = null;

    /**
     * Get a PHPMailerOAuthGoogle instance to use.
     * @return PHPMailerOAuthGoogle
     */
    public function getOAUTHInstance()
    {
        if (!is_object($this-&gt;oauth)) {
            $this-&gt;oauth = new PHPMailerOAuthGoogle(
                $this-&gt;oauthUserEmail,
                $this-&gt;oauthClientSecret,
                $this-&gt;oauthClientId,
                $this-&gt;oauthRefreshToken
            );
        }
        return $this-&gt;oauth;
    }

    /**
     * Initiate a connection to an SMTP server.
     * Overrides the original smtpConnect method to add support for OAuth.
     * @param array $options An array of options compatible with stream_context_create()
     * @uses SMTP
     * @access public
     * @return bool
     * @throws phpmailerException
     */
    public function smtpConnect($options = array())
    {
        if (is_null($this-&gt;smtp)) {
            $this-&gt;smtp = $this-&gt;getSMTPInstance();
        }

        if (is_null($this-&gt;oauth)) {
            $this-&gt;oauth = $this-&gt;getOAUTHInstance();
        }

        // Already connected?
        if ($this-&gt;smtp-&gt;connected()) {
            return true;
        }

        $this-&gt;smtp-&gt;setTimeout($this-&gt;Timeout);
        $this-&gt;smtp-&gt;setDebugLevel($this-&gt;SMTPDebug);
        $this-&gt;smtp-&gt;setDebugOutput($this-&gt;Debugoutput);
        $this-&gt;smtp-&gt;setVerp($this-&gt;do_verp);
        $hosts = explode(';', $this-&gt;Host);
        $lastexception = null;

        foreach ($hosts as $hostentry) {
            $hostinfo = array();
            if (!preg_match('/^((ssl|tls):\/\/)*([a-zA-Z0-9\.-]*):?([0-9]*)$/', trim($hostentry), $hostinfo)) {
                // Not a valid host entry
                continue;
            }
            // $hostinfo[2]: optional ssl or tls prefix
            // $hostinfo[3]: the hostname
            // $hostinfo[4]: optional port number
            // The host string prefix can temporarily override the current setting for SMTPSecure
            // If it's not specified, the default value is used
            $prefix = '';
            $secure = $this-&gt;SMTPSecure;
            $tls = ($this-&gt;SMTPSecure == 'tls');
            if ('ssl' == $hostinfo[2] or ('' == $hostinfo[2] and 'ssl' == $this-&gt;SMTPSecure)) {
                $prefix = 'ssl://';
                $tls = false; // Can't have SSL and TLS at the same time
                $secure = 'ssl';
            } elseif ($hostinfo[2] == 'tls') {
                $tls = true;
                // tls doesn't use a prefix
                $secure = 'tls';
            }
            //Do we need the OpenSSL extension?
            $sslext = defined('OPENSSL_ALGO_SHA1');
            if ('tls' === $secure or 'ssl' === $secure) {
                //Check for an OpenSSL constant rather than using extension_loaded, which is sometimes disabled
                if (!$sslext) {
                    throw new phpmailerException($this-&gt;lang('extension_missing').'openssl', self::STOP_CRITICAL);
                }
            }
            $host = $hostinfo[3];
            $port = $this-&gt;Port;
            $tport = (integer)$hostinfo[4];
            if ($tport &gt; 0 and $tport &lt; 65536) {
                $port = $tport;
            }
            if ($this-&gt;smtp-&gt;connect($prefix . $host, $port, $this-&gt;Timeout, $options)) {
                try {
                    if ($this-&gt;Helo) {
                        $hello = $this-&gt;Helo;
                    } else {
                        $hello = $this-&gt;serverHostname();
                    }
                    $this-&gt;smtp-&gt;hello($hello);
                    //Automatically enable TLS encryption if:
                    // * it's not disabled
                    // * we have openssl extension
                    // * we are not already using SSL
                    // * the server offers STARTTLS
                    if ($this-&gt;SMTPAutoTLS and $sslext and $secure != 'ssl' and $this-&gt;smtp-&gt;getServerExt('STARTTLS')) {
                        $tls = true;
                    }
                    if ($tls) {
                        if (!$this-&gt;smtp-&gt;startTLS()) {
                            throw new phpmailerException($this-&gt;lang('connect_host'));
                        }
                        // We must resend HELO after tls negotiation
                        $this-&gt;smtp-&gt;hello($hello);
                    }
                    if ($this-&gt;SMTPAuth) {
                        if (!$this-&gt;smtp-&gt;authenticate(
                            $this-&gt;Username,
                            $this-&gt;Password,
                            $this-&gt;AuthType,
                            $this-&gt;Realm,
                            $this-&gt;Workstation,
                            $this-&gt;oauth
                        )
                        ) {
                            throw new phpmailerException($this-&gt;lang('authenticate'));
                        }
                    }
                    return true;
                } catch (phpmailerException $exc) {
                    $lastexception = $exc;
                    $this-&gt;edebug($exc-&gt;getMessage());
                    // We must have connected, but then failed TLS or Auth, so close connection nicely
                    $this-&gt;smtp-&gt;quit();
                }
            }
        }
        // If we get here, all connection attempts have failed, so close connection hard
        $this-&gt;smtp-&gt;close();
        // As we've caught all exceptions, just report whatever the last one was
        if ($this-&gt;exceptions and !is_null($lastexception)) {
            throw $lastexception;
        }
        return false;
    }
}
</phpmailer@synchromedia.co.uk></codeworxtech@users.sourceforge.net></jimjag@gmail.com></body></html>