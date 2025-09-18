<!--?php
/**
 * PHPMailer - PHP email creation and transport class.
 * PHP Version 5
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
 * PHPMailer - PHP email creation and transport class.
 * @package PHPMailer
 * @author Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @author Jim Jagielski (jimjag) <jimjag@gmail.com>
 * @author Andy Prevost (codeworxtech) <codeworxtech@users.sourceforge.net>
 * @author Brent R. Matzelle (original founder)
 */
class PHPMailer
{
    /**
     * The PHPMailer Version number.
     * @var string
     */
    public $Version = '5.2.14';

    /**
     * Email priority.
     * Options: null (default), 1 = High, 3 = Normal, 5 = low.
     * When null, the header is not set at all.
     * @var integer
     */
    public $Priority = null;

    /**
     * The character set of the message.
     * @var string
     */
    public $CharSet = 'iso-8859-1';

    /**
     * The MIME Content-type of the message.
     * @var string
     */
    public $ContentType = 'text/plain';

    /**
     * The message encoding.
     * Options: "8bit", "7bit", "binary", "base64", and "quoted-printable".
     * @var string
     */
    public $Encoding = '8bit';

    /**
     * Holds the most recent mailer error message.
     * @var string
     */
    public $ErrorInfo = '';

    /**
     * The From email address for the message.
     * @var string
     */
    public $From = 'root@localhost';

    /**
     * The From name of the message.
     * @var string
     */
    public $FromName = 'Root User';

    /**
     * The Sender email (Return-Path) of the message.
     * If not empty, will be sent via -f to sendmail or as 'MAIL FROM' in smtp mode.
     * @var string
     */
    public $Sender = '';

    /**
     * The Return-Path of the message.
     * If empty, it will be set to either From or Sender.
     * @var string
     * @deprecated Email senders should never set a return-path header;
     * it's the receiver's job (RFC5321 section 4.4), so this no longer does anything.
     * @link https://tools.ietf.org/html/rfc5321#section-4.4 RFC5321 reference
     */
    public $ReturnPath = '';

    /**
     * The Subject of the message.
     * @var string
     */
    public $Subject = '';

    /**
     * An HTML or plain text message body.
     * If HTML then call isHTML(true).
     * @var string
     */
    public $Body = '';

    /**
     * The plain-text message body.
     * This body can be read by mail clients that do not have HTML email
     * capability such as mutt &amp; Eudora.
     * Clients that can read HTML will view the normal Body.
     * @var string
     */
    public $AltBody = '';

    /**
     * An iCal message part body.
     * Only supported in simple alt or alt_inline message types
     * To generate iCal events, use the bundled extras/EasyPeasyICS.php class or iCalcreator
     * @link http://sprain.ch/blog/downloads/php-class-easypeasyics-create-ical-files-with-php/
     * @link http://kigkonsult.se/iCalcreator/
     * @var string
     */
    public $Ical = '';

    /**
     * The complete compiled MIME message body.
     * @access protected
     * @var string
     */
    protected $MIMEBody = '';

    /**
     * The complete compiled MIME message headers.
     * @var string
     * @access protected
     */
    protected $MIMEHeader = '';

    /**
     * Extra headers that createHeader() doesn't fold in.
     * @var string
     * @access protected
     */
    protected $mailHeader = '';

    /**
     * Word-wrap the message body to this number of chars.
     * Set to 0 to not wrap. A useful value here is 78, for RFC2822 section 2.1.1 compliance.
     * @var integer
     */
    public $WordWrap = 0;

    /**
     * Which method to use to send mail.
     * Options: "mail", "sendmail", or "smtp".
     * @var string
     */
    public $Mailer = 'mail';

    /**
     * The path to the sendmail program.
     * @var string
     */
    public $Sendmail = '/usr/sbin/sendmail';

    /**
     * Whether mail() uses a fully sendmail-compatible MTA.
     * One which supports sendmail's "-oi -f" options.
     * @var boolean
     */
    public $UseSendmailOptions = true;

    /**
     * Path to PHPMailer plugins.
     * Useful if the SMTP class is not in the PHP include path.
     * @var string
     * @deprecated Should not be needed now there is an autoloader.
     */
    public $PluginDir = '';

    /**
     * The email address that a reading confirmation should be sent to, also known as read receipt.
     * @var string
     */
    public $ConfirmReadingTo = '';

    /**
     * The hostname to use in the Message-ID header and as default HELO string.
     * If empty, PHPMailer attempts to find one with, in order,
     * $_SERVER['SERVER_NAME'], gethostname(), php_uname('n'), or the value
     * 'localhost.localdomain'.
     * @var string
     */
    public $Hostname = '';

    /**
     * An ID to be used in the Message-ID header.
     * If empty, a unique id will be generated.
     * @var string
     */
    public $MessageID = '';

    /**
     * The message Date to be used in the Date header.
     * If empty, the current date will be added.
     * @var string
     */
    public $MessageDate = '';

    /**
     * SMTP hosts.
     * Either a single hostname or multiple semicolon-delimited hostnames.
     * You can also specify a different port
     * for each host by using this format: [hostname:port]
     * (e.g. "smtp1.example.com:25;smtp2.example.com").
     * You can also specify encryption type, for example:
     * (e.g. "tls://smtp1.example.com:587;ssl://smtp2.example.com:465").
     * Hosts will be tried in order.
     * @var string
     */
    public $Host = 'localhost';

    /**
     * The default SMTP server port.
     * @var integer
     * @TODO Why is this needed when the SMTP class takes care of it?
     */
    public $Port = 25;

    /**
     * The SMTP HELO of the message.
     * Default is $Hostname. If $Hostname is empty, PHPMailer attempts to find
     * one with the same method described above for $Hostname.
     * @var string
     * @see PHPMailer::$Hostname
     */
    public $Helo = '';

    /**
     * What kind of encryption to use on the SMTP connection.
     * Options: '', 'ssl' or 'tls'
     * @var string
     */
    public $SMTPSecure = '';

    /**
     * Whether to enable TLS encryption automatically if a server supports it,
     * even if `SMTPSecure` is not set to 'tls'.
     * Be aware that in PHP &gt;= 5.6 this requires that the server's certificates are valid.
     * @var boolean
     */
    public $SMTPAutoTLS = true;

    /**
     * Whether to use SMTP authentication.
     * Uses the Username and Password properties.
     * @var boolean
     * @see PHPMailer::$Username
     * @see PHPMailer::$Password
     */
    public $SMTPAuth = false;

    /**
     * Options array passed to stream_context_create when connecting via SMTP.
     * @var array
     */
    public $SMTPOptions = array();

    /**
     * SMTP username.
     * @var string
     */
    public $Username = '';

    /**
     * SMTP password.
     * @var string
     */
    public $Password = '';

    /**
     * SMTP auth type.
     * Options are LOGIN (default), PLAIN, NTLM, CRAM-MD5
     * @var string
     */
    public $AuthType = '';

    /**
     * SMTP realm.
     * Used for NTLM auth
     * @var string
     */
    public $Realm = '';

    /**
     * SMTP workstation.
     * Used for NTLM auth
     * @var string
     */
    public $Workstation = '';

    /**
     * The SMTP server timeout in seconds.
     * Default of 5 minutes (300sec) is from RFC2821 section 4.5.3.2
     * @var integer
     */
    public $Timeout = 300;

    /**
     * SMTP class debug output mode.
     * Debug output level.
     * Options:
     * * `0` No output
     * * `1` Commands
     * * `2` Data and commands
     * * `3` As 2 plus connection status
     * * `4` Low-level data output
     * @var integer
     * @see SMTP::$do_debug
     */
    public $SMTPDebug = 0;

    /**
     * How to handle debug output.
     * Options:
     * * `echo` Output plain-text as-is, appropriate for CLI
     * * `html` Output escaped, line breaks converted to `<br>`, appropriate for browser output
     * * `error_log` Output to error log as configured in php.ini
     *
     * Alternatively, you can provide a callable expecting two params: a message string and the debug level:
     * <code>
     * $mail-&gt;Debugoutput = function($str, $level) {echo "debug level $level; message: $str";};
     * </code>
     * @var string|callable
     * @see SMTP::$Debugoutput
     */
    public $Debugoutput = 'echo';

    /**
     * Whether to keep SMTP connection open after each message.
     * If this is set to true then to close the connection
     * requires an explicit call to smtpClose().
     * @var boolean
     */
    public $SMTPKeepAlive = false;

    /**
     * Whether to split multiple to addresses into multiple messages
     * or send them all in one message.
     * @var boolean
     */
    public $SingleTo = false;

    /**
     * Storage for addresses when SingleTo is enabled.
     * @var array
     * @TODO This should really not be public
     */
    public $SingleToArray = array();

    /**
     * Whether to generate VERP addresses on send.
     * Only applicable when sending via SMTP.
     * @link https://en.wikipedia.org/wiki/Variable_envelope_return_path
     * @link http://www.postfix.org/VERP_README.html Postfix VERP info
     * @var boolean
     */
    public $do_verp = false;

    /**
     * Whether to allow sending messages with an empty body.
     * @var boolean
     */
    public $AllowEmpty = false;

    /**
     * The default line ending.
     * @note The default remains "\n". We force CRLF where we know
     *        it must be used via self::CRLF.
     * @var string
     */
    public $LE = "\n";

    /**
     * DKIM selector.
     * @var string
     */
    public $DKIM_selector = '';

    /**
     * DKIM Identity.
     * Usually the email address used as the source of the email
     * @var string
     */
    public $DKIM_identity = '';

    /**
     * DKIM passphrase.
     * Used if your key is encrypted.
     * @var string
     */
    public $DKIM_passphrase = '';

    /**
     * DKIM signing domain name.
     * @example 'example.com'
     * @var string
     */
    public $DKIM_domain = '';

    /**
     * DKIM private key file path.
     * @var string
     */
    public $DKIM_private = '';

    /**
     * Callback Action function name.
     *
     * The function that handles the result of the send email action.
     * It is called out by send() for each email sent.
     *
     * Value can be any php callable: http://www.php.net/is_callable
     *
     * Parameters:
     *   boolean $result        result of the send action
     *   string  $to            email address of the recipient
     *   string  $cc            cc email addresses
     *   string  $bcc           bcc email addresses
     *   string  $subject       the subject
     *   string  $body          the email body
     *   string  $from          email address of sender
     * @var string
     */
    public $action_function = '';

    /**
     * What to put in the X-Mailer header.
     * Options: An empty string for PHPMailer default, whitespace for none, or a string to use
     * @var string
     */
    public $XMailer = '';

    /**
     * An instance of the SMTP sender class.
     * @var SMTP
     * @access protected
     */
    protected $smtp = null;

    /**
     * The array of 'to' names and addresses.
     * @var array
     * @access protected
     */
    protected $to = array();

    /**
     * The array of 'cc' names and addresses.
     * @var array
     * @access protected
     */
    protected $cc = array();

    /**
     * The array of 'bcc' names and addresses.
     * @var array
     * @access protected
     */
    protected $bcc = array();

    /**
     * The array of reply-to names and addresses.
     * @var array
     * @access protected
     */
    protected $ReplyTo = array();

    /**
     * An array of all kinds of addresses.
     * Includes all of $to, $cc, $bcc
     * @var array
     * @access protected
     * @see PHPMailer::$to @see PHPMailer::$cc @see PHPMailer::$bcc
     */
    protected $all_recipients = array();

    /**
     * An array of names and addresses queued for validation.
     * In send(), valid and non duplicate entries are moved to $all_recipients
     * and one of $to, $cc, or $bcc.
     * This array is used only for addresses with IDN.
     * @var array
     * @access protected
     * @see PHPMailer::$to @see PHPMailer::$cc @see PHPMailer::$bcc
     * @see PHPMailer::$all_recipients
     */
    protected $RecipientsQueue = array();

    /**
     * An array of reply-to names and addresses queued for validation.
     * In send(), valid and non duplicate entries are moved to $ReplyTo.
     * This array is used only for addresses with IDN.
     * @var array
     * @access protected
     * @see PHPMailer::$ReplyTo
     */
    protected $ReplyToQueue = array();

    /**
     * The array of attachments.
     * @var array
     * @access protected
     */
    protected $attachment = array();

    /**
     * The array of custom headers.
     * @var array
     * @access protected
     */
    protected $CustomHeader = array();

    /**
     * The most recent Message-ID (including angular brackets).
     * @var string
     * @access protected
     */
    protected $lastMessageID = '';

    /**
     * The message's MIME type.
     * @var string
     * @access protected
     */
    protected $message_type = '';

    /**
     * The array of MIME boundary strings.
     * @var array
     * @access protected
     */
    protected $boundary = array();

    /**
     * The array of available languages.
     * @var array
     * @access protected
     */
    protected $language = array();

    /**
     * The number of errors encountered.
     * @var integer
     * @access protected
     */
    protected $error_count = 0;

    /**
     * The S/MIME certificate file path.
     * @var string
     * @access protected
     */
    protected $sign_cert_file = '';

    /**
     * The S/MIME key file path.
     * @var string
     * @access protected
     */
    protected $sign_key_file = '';

    /**
     * The optional S/MIME extra certificates ("CA Chain") file path.
     * @var string
     * @access protected
     */
    protected $sign_extracerts_file = '';

    /**
     * The S/MIME password for the key.
     * Used only if the key is encrypted.
     * @var string
     * @access protected
     */
    protected $sign_key_pass = '';

    /**
     * Whether to throw exceptions for errors.
     * @var boolean
     * @access protected
     */
    protected $exceptions = false;

    /**
     * Unique ID used for message ID and boundaries.
     * @var string
     * @access protected
     */
    protected $uniqueid = '';

    /**
     * Error severity: message only, continue processing.
     */
    const STOP_MESSAGE = 0;

    /**
     * Error severity: message, likely ok to continue processing.
     */
    const STOP_CONTINUE = 1;

    /**
     * Error severity: message, plus full stop, critical error reached.
     */
    const STOP_CRITICAL = 2;

    /**
     * SMTP RFC standard line ending.
     */
    const CRLF = "\r\n";

    /**
     * The maximum line length allowed by RFC 2822 section 2.1.1
     * @var integer
     */
    const MAX_LINE_LENGTH = 998;

    /**
     * Constructor.
     * @param boolean $exceptions Should we throw external exceptions?
     */
    public function __construct($exceptions = false)
    {
        $this-&gt;exceptions = (boolean)$exceptions;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        //Close any open SMTP connection nicely
        if ($this-&gt;Mailer == 'smtp') {
            $this-&gt;smtpClose();
        }
    }

    /**
     * Call mail() in a safe_mode-aware fashion.
     * Also, unless sendmail_path points to sendmail (or something that
     * claims to be sendmail), don't pass params (not a perfect fix,
     * but it will do)
     * @param string $to To
     * @param string $subject Subject
     * @param string $body Message Body
     * @param string $header Additional Header(s)
     * @param string $params Params
     * @access private
     * @return boolean
     */
    private function mailPassthru($to, $subject, $body, $header, $params)
    {
        //Check overloading of mail function to avoid double-encoding
        if (ini_get('mbstring.func_overload') &amp; 1) {
            $subject = $this-&gt;secureHeader($subject);
        } else {
            $subject = $this-&gt;encodeHeader($this-&gt;secureHeader($subject));
        }
        if (ini_get('safe_mode') || !($this-&gt;UseSendmailOptions)) {
            $result = @mail($to, $subject, $body, $header);
        } else {
            $result = @mail($to, $subject, $body, $header, $params);
        }
        return $result;
    }

    /**
     * Output debugging info via user-defined method.
     * Only generates output if SMTP debug output is enabled (@see SMTP::$do_debug).
     * @see PHPMailer::$Debugoutput
     * @see PHPMailer::$SMTPDebug
     * @param string $str
     */
    protected function edebug($str)
    {
        if ($this-&gt;SMTPDebug &lt;= 0) {
            return;
        }
        //Avoid clash with built-in function names
        if (!in_array($this-&gt;Debugoutput, array('error_log', 'html', 'echo')) and is_callable($this-&gt;Debugoutput)) {
            call_user_func($this-&gt;Debugoutput, $str, $this-&gt;SMTPDebug);
            return;
        }
        switch ($this-&gt;Debugoutput) {
            case 'error_log':
                //Don't output, just log
                error_log($str);
                break;
            case 'html':
                //Cleans up output a bit for a better looking, HTML-safe output
                echo htmlentities(
                    preg_replace('/[\r\n]+/', '', $str),
                    ENT_QUOTES,
                    'UTF-8'
                )
                . "<br>\n";
                break;
            case 'echo':
            default:
                //Normalize line breaks
                $str = preg_replace('/(\r\n|\r|\n)/ms', "\n", $str);
                echo gmdate('Y-m-d H:i:s') . "\t" . str_replace(
                    "\n",
                    "\n                   \t                  ",
                    trim($str)
                ) . "\n";
        }
    }

    /**
     * Sets message type to HTML or plain.
     * @param boolean $isHtml True for HTML mode.
     * @return void
     */
    public function isHTML($isHtml = true)
    {
        if ($isHtml) {
            $this-&gt;ContentType = 'text/html';
        } else {
            $this-&gt;ContentType = 'text/plain';
        }
    }

    /**
     * Send messages using SMTP.
     * @return void
     */
    public function isSMTP()
    {
        $this-&gt;Mailer = 'smtp';
    }

    /**
     * Send messages using PHP's mail() function.
     * @return void
     */
    public function isMail()
    {
        $this-&gt;Mailer = 'mail';
    }

    /**
     * Send messages using $Sendmail.
     * @return void
     */
    public function isSendmail()
    {
        $ini_sendmail_path = ini_get('sendmail_path');

        if (!stristr($ini_sendmail_path, 'sendmail')) {
            $this-&gt;Sendmail = '/usr/sbin/sendmail';
        } else {
            $this-&gt;Sendmail = $ini_sendmail_path;
        }
        $this-&gt;Mailer = 'sendmail';
    }

    /**
     * Send messages using qmail.
     * @return void
     */
    public function isQmail()
    {
        $ini_sendmail_path = ini_get('sendmail_path');

        if (!stristr($ini_sendmail_path, 'qmail')) {
            $this-&gt;Sendmail = '/var/qmail/bin/qmail-inject';
        } else {
            $this-&gt;Sendmail = $ini_sendmail_path;
        }
        $this-&gt;Mailer = 'qmail';
    }

    /**
     * Add a "To" address.
     * @param string $address The email address to send to
     * @param string $name
     * @return boolean true on success, false if address already used or invalid in some way
     */
    public function addAddress($address, $name = '')
    {
        return $this-&gt;addOrEnqueueAnAddress('to', $address, $name);
    }

    /**
     * Add a "CC" address.
     * @note: This function works with the SMTP mailer on win32, not with the "mail" mailer.
     * @param string $address The email address to send to
     * @param string $name
     * @return boolean true on success, false if address already used or invalid in some way
     */
    public function addCC($address, $name = '')
    {
        return $this-&gt;addOrEnqueueAnAddress('cc', $address, $name);
    }

    /**
     * Add a "BCC" address.
     * @note: This function works with the SMTP mailer on win32, not with the "mail" mailer.
     * @param string $address The email address to send to
     * @param string $name
     * @return boolean true on success, false if address already used or invalid in some way
     */
    public function addBCC($address, $name = '')
    {
        return $this-&gt;addOrEnqueueAnAddress('bcc', $address, $name);
    }

    /**
     * Add a "Reply-To" address.
     * @param string $address The email address to reply to
     * @param string $name
     * @return boolean true on success, false if address already used or invalid in some way
     */
    public function addReplyTo($address, $name = '')
    {
        return $this-&gt;addOrEnqueueAnAddress('Reply-To', $address, $name);
    }

    /**
     * Add an address to one of the recipient arrays or to the ReplyTo array. Because PHPMailer
     * can't validate addresses with an IDN without knowing the PHPMailer::$CharSet (that can still
     * be modified after calling this function), addition of such addresses is delayed until send().
     * Addresses that have been added already return false, but do not throw exceptions.
     * @param string $kind One of 'to', 'cc', 'bcc', or 'ReplyTo'
     * @param string $address The email address to send, resp. to reply to
     * @param string $name
     * @throws phpmailerException
     * @return boolean true on success, false if address already used or invalid in some way
     * @access protected
     */
    protected function addOrEnqueueAnAddress($kind, $address, $name)
    {
        $address = trim($address);
        $name = trim(preg_replace('/[\r\n]+/', '', $name)); //Strip breaks and trim
        if (($pos = strrpos($address, '@')) === false) {
            // At-sign is misssing.
            $error_message = $this-&gt;lang('invalid_address') . " (addAnAddress $kind): $address";
            $this-&gt;setError($error_message);
            $this-&gt;edebug($error_message);
            if ($this-&gt;exceptions) {
                throw new phpmailerException($error_message);
            }
            return false;
        }
        $params = array($kind, $address, $name);
        // Enqueue addresses with IDN until we know the PHPMailer::$CharSet.
        if ($this-&gt;has8bitChars(substr($address, ++$pos)) and $this-&gt;idnSupported()) {
            if ($kind != 'Reply-To') {
                if (!array_key_exists($address, $this-&gt;RecipientsQueue)) {
                    $this-&gt;RecipientsQueue[$address] = $params;
                    return true;
                }
            } else {
                if (!array_key_exists($address, $this-&gt;ReplyToQueue)) {
                    $this-&gt;ReplyToQueue[$address] = $params;
                    return true;
                }
            }
            return false;
        }
        // Immediately add standard addresses without IDN.
        return call_user_func_array(array($this, 'addAnAddress'), $params);
    }

    /**
     * Add an address to one of the recipient arrays or to the ReplyTo array.
     * Addresses that have been added already return false, but do not throw exceptions.
     * @param string $kind One of 'to', 'cc', 'bcc', or 'ReplyTo'
     * @param string $address The email address to send, resp. to reply to
     * @param string $name
     * @throws phpmailerException
     * @return boolean true on success, false if address already used or invalid in some way
     * @access protected
     */
    protected function addAnAddress($kind, $address, $name = '')
    {
        if (!in_array($kind, array('to', 'cc', 'bcc', 'Reply-To'))) {
            $error_message = $this-&gt;lang('Invalid recipient kind: ') . $kind;
            $this-&gt;setError($error_message);
            $this-&gt;edebug($error_message);
            if ($this-&gt;exceptions) {
                throw new phpmailerException($error_message);
            }
            return false;
        }
        if (!$this-&gt;validateAddress($address)) {
            $error_message = $this-&gt;lang('invalid_address') . " (addAnAddress $kind): $address";
            $this-&gt;setError($error_message);
            $this-&gt;edebug($error_message);
            if ($this-&gt;exceptions) {
                throw new phpmailerException($error_message);
            }
            return false;
        }
        if ($kind != 'Reply-To') {
            if (!array_key_exists(strtolower($address), $this-&gt;all_recipients)) {
                array_push($this-&gt;$kind, array($address, $name));
                $this-&gt;all_recipients[strtolower($address)] = true;
                return true;
            }
        } else {
            if (!array_key_exists(strtolower($address), $this-&gt;ReplyTo)) {
                $this-&gt;ReplyTo[strtolower($address)] = array($address, $name);
                return true;
            }
        }
        return false;
    }

    /**
     * Parse and validate a string containing one or more RFC822-style comma-separated email addresses
     * of the form "display name <address>" into an array of name/address pairs.
     * Uses the imap_rfc822_parse_adrlist function if the IMAP extension is available.
     * Note that quotes in the name part are removed.
     * @param string $addrstr The address list string
     * @param bool $useimap Whether to use the IMAP extension to parse the list
     * @return array
     * @link http://www.andrew.cmu.edu/user/agreen1/testing/mrbs/web/Mail/RFC822.php A more careful implementation
     */
    public function parseAddresses($addrstr, $useimap = true)
    {
        $addresses = array();
        if ($useimap and function_exists('imap_rfc822_parse_adrlist')) {
            //Use this built-in parser if it's available
            $list = imap_rfc822_parse_adrlist($addrstr, '');
            foreach ($list as $address) {
                if ($address-&gt;host != '.SYNTAX-ERROR.') {
                    if ($this-&gt;validateAddress($address-&gt;mailbox . '@' . $address-&gt;host)) {
                        $addresses[] = array(
                            'name' =&gt; (property_exists($address, 'personal') ? $address-&gt;personal : ''),
                            'address' =&gt; $address-&gt;mailbox . '@' . $address-&gt;host
                        );
                    }
                }
            }
        } else {
            //Use this simpler parser
            $list = explode(',', $addrstr);
            foreach ($list as $address) {
                $address = trim($address);
                //Is there a separate name part?
                if (strpos($address, '&lt;') === false) {
                    //No separate name, just use the whole thing
                    if ($this-&gt;validateAddress($address)) {
                        $addresses[] = array(
                            'name' =&gt; '',
                            'address' =&gt; $address
                        );
                    }
                } else {
                    list($name, $email) = explode('&lt;', $address);
                    $email = trim(str_replace('&gt;', '', $email));
                    if ($this-&gt;validateAddress($email)) {
                        $addresses[] = array(
                            'name' =&gt; trim(str_replace(array('"', "'"), '', $name)),
                            'address' =&gt; $email
                        );
                    }
                }
            }
        }
        return $addresses;
    }

    /**
     * Set the From and FromName properties.
     * @param string $address
     * @param string $name
     * @param boolean $auto Whether to also set the Sender address, defaults to true
     * @throws phpmailerException
     * @return boolean
     */
    public function setFrom($address, $name = '', $auto = true)
    {
        $address = trim($address);
        $name = trim(preg_replace('/[\r\n]+/', '', $name)); //Strip breaks and trim
        // Don't validate now addresses with IDN. Will be done in send().
        if (($pos = strrpos($address, '@')) === false or
            (!$this-&gt;has8bitChars(substr($address, ++$pos)) or !$this-&gt;idnSupported()) and
            !$this-&gt;validateAddress($address)) {
            $error_message = $this-&gt;lang('invalid_address') . " (setFrom) $address";
            $this-&gt;setError($error_message);
            $this-&gt;edebug($error_message);
            if ($this-&gt;exceptions) {
                throw new phpmailerException($error_message);
            }
            return false;
        }
        $this-&gt;From = $address;
        $this-&gt;FromName = $name;
        if ($auto) {
            if (empty($this-&gt;Sender)) {
                $this-&gt;Sender = $address;
            }
        }
        return true;
    }

    /**
     * Return the Message-ID header of the last email.
     * Technically this is the value from the last time the headers were created,
     * but it's also the message ID of the last sent message except in
     * pathological cases.
     * @return string
     */
    public function getLastMessageID()
    {
        return $this-&gt;lastMessageID;
    }

    /**
     * Check that a string looks like an email address.
     * @param string $address The email address to check
     * @param string $patternselect A selector for the validation pattern to use :
     * * `auto` Pick best pattern automatically;
     * * `pcre8` Use the squiloople.com pattern, requires PCRE &gt; 8.0, PHP &gt;= 5.3.2, 5.2.14;
     * * `pcre` Use old PCRE implementation;
     * * `php` Use PHP built-in FILTER_VALIDATE_EMAIL;
     * * `html5` Use the pattern given by the HTML5 spec for 'email' type form input elements.
     * * `noregex` Don't use a regex: super fast, really dumb.
     * @return boolean
     * @static
     * @access public
     */
    public static function validateAddress($address, $patternselect = 'auto')
    {
        //Reject line breaks in addresses; it's valid RFC5322, but not RFC5321
        if (strpos($address, "\n") !== false or strpos($address, "\r") !== false) {
            return false;
        }
        if (!$patternselect or $patternselect == 'auto') {
            //Check this constant first so it works when extension_loaded() is disabled by safe mode
            //Constant was added in PHP 5.2.4
            if (defined('PCRE_VERSION')) {
                //This pattern can get stuck in a recursive loop in PCRE &lt;= 8.0.2
                if (version_compare(PCRE_VERSION, '8.0.3') &gt;= 0) {
                    $patternselect = 'pcre8';
                } else {
                    $patternselect = 'pcre';
                }
            } elseif (function_exists('extension_loaded') and extension_loaded('pcre')) {
                //Fall back to older PCRE
                $patternselect = 'pcre';
            } else {
                //Filter_var appeared in PHP 5.2.0 and does not require the PCRE extension
                if (version_compare(PHP_VERSION, '5.2.0') &gt;= 0) {
                    $patternselect = 'php';
                } else {
                    $patternselect = 'noregex';
                }
            }
        }
        switch ($patternselect) {
            case 'pcre8':
                /**
                 * Uses the same RFC5322 regex on which FILTER_VALIDATE_EMAIL is based, but allows dotless domains.
                 * @link http://squiloople.com/2009/12/20/email-address-validation/
                 * @copyright 2009-2010 Michael Rushton
                 * Feel free to use and redistribute this code. But please keep this copyright notice.
                 */
                return (boolean)preg_match(
                    '/^(?!(?&gt;(?1)"?(?&gt;\\\[ -~]|[^"])"?(?1)){255,})(?!(?&gt;(?1)"?(?&gt;\\\[ -~]|[^"])"?(?1)){65,}@)' .
                    '((?&gt;(?&gt;(?&gt;((?&gt;(?&gt;(?&gt;\x0D\x0A)?[\t ])+|(?&gt;[\t ]*\x0D\x0A)?[\t ]+)?)(\((?&gt;(?2)' .
                    '(?&gt;[\x01-\x08\x0B\x0C\x0E-\'*-\[\]-\x7F]|\\\[\x00-\x7F]|(?3)))*(?2)\)))+(?2))|(?2))?)' .
                    '([!#-\'*+\/-9=?^-~-]+|"(?&gt;(?2)(?&gt;[\x01-\x08\x0B\x0C\x0E-!#-\[\]-\x7F]|\\\[\x00-\x7F]))*' .
                    '(?2)")(?&gt;(?1)\.(?1)(?4))*(?1)@(?!(?1)[a-z0-9-]{64,})(?1)(?&gt;([a-z0-9](?&gt;[a-z0-9-]*[a-z0-9])?)' .
                    '(?&gt;(?1)\.(?!(?1)[a-z0-9-]{64,})(?1)(?5)){0,126}|\[(?:(?&gt;IPv6:(?&gt;([a-f0-9]{1,4})(?&gt;:(?6)){7}' .
                    '|(?!(?:.*[a-f0-9][:\]]){8,})((?6)(?&gt;:(?6)){0,6})?::(?7)?))|(?&gt;(?&gt;IPv6:(?&gt;(?6)(?&gt;:(?6)){5}:' .
                    '|(?!(?:.*[a-f0-9]:){6,})(?8)?::(?&gt;((?6)(?&gt;:(?6)){0,4}):)?))?(25[0-5]|2[0-4][0-9]|1[0-9]{2}' .
                    '|[1-9]?[0-9])(?&gt;\.(?9)){3}))\])(?1)$/isD',
                    $address
                );
            case 'pcre':
                //An older regex that doesn't need a recent PCRE
                return (boolean)preg_match(
                    '/^(?!(?&gt;"?(?&gt;\\\[ -~]|[^"])"?){255,})(?!(?&gt;"?(?&gt;\\\[ -~]|[^"])"?){65,}@)(?&gt;' .
                    '[!#-\'*+\/-9=?^-~-]+|"(?&gt;(?&gt;[\x01-\x08\x0B\x0C\x0E-!#-\[\]-\x7F]|\\\[\x00-\xFF]))*")' .
                    '(?&gt;\.(?&gt;[!#-\'*+\/-9=?^-~-]+|"(?&gt;(?&gt;[\x01-\x08\x0B\x0C\x0E-!#-\[\]-\x7F]|\\\[\x00-\xFF]))*"))*' .
                    '@(?&gt;(?![a-z0-9-]{64,})(?&gt;[a-z0-9](?&gt;[a-z0-9-]*[a-z0-9])?)(?&gt;\.(?![a-z0-9-]{64,})' .
                    '(?&gt;[a-z0-9](?&gt;[a-z0-9-]*[a-z0-9])?)){0,126}|\[(?:(?&gt;IPv6:(?&gt;(?&gt;[a-f0-9]{1,4})(?&gt;:' .
                    '[a-f0-9]{1,4}){7}|(?!(?:.*[a-f0-9][:\]]){8,})(?&gt;[a-f0-9]{1,4}(?&gt;:[a-f0-9]{1,4}){0,6})?' .
                    '::(?&gt;[a-f0-9]{1,4}(?&gt;:[a-f0-9]{1,4}){0,6})?))|(?&gt;(?&gt;IPv6:(?&gt;[a-f0-9]{1,4}(?&gt;:' .
                    '[a-f0-9]{1,4}){5}:|(?!(?:.*[a-f0-9]:){6,})(?&gt;[a-f0-9]{1,4}(?&gt;:[a-f0-9]{1,4}){0,4})?' .
                    '::(?&gt;(?:[a-f0-9]{1,4}(?&gt;:[a-f0-9]{1,4}){0,4}):)?))?(?&gt;25[0-5]|2[0-4][0-9]|1[0-9]{2}' .
                    '|[1-9]?[0-9])(?&gt;\.(?&gt;25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])){3}))\])$/isD',
                    $address
                );
            case 'html5':
                /**
                 * This is the pattern used in the HTML5 spec for validation of 'email' type form input elements.
                 * @link http://www.whatwg.org/specs/web-apps/current-work/#e-mail-state-(type=email)
                 */
                return (boolean)preg_match(
                    '/^[a-zA-Z0-9.!#$%&amp;\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}' .
                    '[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/sD',
                    $address
                );
            case 'noregex':
                //No PCRE! Do something _very_ approximate!
                //Check the address is 3 chars or longer and contains an @ that's not the first or last char
                return (strlen($address) &gt;= 3
                    and strpos($address, '@') &gt;= 1
                    and strpos($address, '@') != strlen($address) - 1);
            case 'php':
            default:
                return (boolean)filter_var($address, FILTER_VALIDATE_EMAIL);
        }
    }

    /**
     * Tells whether IDNs (Internationalized Domain Names) are supported or not. This requires the
     * "intl" and "mbstring" PHP extensions.
     * @return bool "true" if required functions for IDN support are present
     */
    public function idnSupported()
    {
        // @TODO: Write our own "idn_to_ascii" function for PHP &lt;= 5.2.
        return function_exists('idn_to_ascii') and function_exists('mb_convert_encoding');
    }

    /**
     * Converts IDN in given email address to its ASCII form, also known as punycode, if possible.
     * Important: Address must be passed in same encoding as currently set in PHPMailer::$CharSet.
     * This function silently returns unmodified address if:
     * - No conversion is necessary (i.e. domain name is not an IDN, or is already in ASCII form)
     * - Conversion to punycode is impossible (e.g. required PHP functions are not available)
     *   or fails for any reason (e.g. domain has characters not allowed in an IDN)
     * @see PHPMailer::$CharSet
     * @param string $address The email address to convert
     * @return string The encoded address in ASCII form
     */
    public function punyencodeAddress($address)
    {
        // Verify we have required functions, CharSet, and at-sign.
        if ($this-&gt;idnSupported() and
            !empty($this-&gt;CharSet) and
            ($pos = strrpos($address, '@')) !== false) {
            $domain = substr($address, ++$pos);
            // Verify CharSet string is a valid one, and domain properly encoded in this CharSet.
            if ($this-&gt;has8bitChars($domain) and @mb_check_encoding($domain, $this-&gt;CharSet)) {
                $domain = mb_convert_encoding($domain, 'UTF-8', $this-&gt;CharSet);
                if (($punycode = defined('INTL_IDNA_VARIANT_UTS46') ?
                    idn_to_ascii($domain, 0, INTL_IDNA_VARIANT_UTS46) :
                    idn_to_ascii($domain)) !== false) {
                    return substr($address, 0, $pos) . $punycode;
                }
            }
        }
        return $address;
    }

    /**
     * Create a message and send it.
     * Uses the sending method specified by $Mailer.
     * @throws phpmailerException
     * @return boolean false on error - See the ErrorInfo property for details of the error.
     */
    public function send()
    {
        try {
            if (!$this-&gt;preSend()) {
                return false;
            }
            return $this-&gt;postSend();
        } catch (phpmailerException $exc) {
            $this-&gt;mailHeader = '';
            $this-&gt;setError($exc-&gt;getMessage());
            if ($this-&gt;exceptions) {
                throw $exc;
            }
            return false;
        }
    }

    /**
     * Prepare a message for sending.
     * @throws phpmailerException
     * @return boolean
     */
    public function preSend()
    {
        try {
            $this-&gt;error_count = 0; // Reset errors
            $this-&gt;mailHeader = '';

            // Dequeue recipient and Reply-To addresses with IDN
            foreach (array_merge($this-&gt;RecipientsQueue, $this-&gt;ReplyToQueue) as $params) {
                $params[1] = $this-&gt;punyencodeAddress($params[1]);
                call_user_func_array(array($this, 'addAnAddress'), $params);
            }
            if ((count($this-&gt;to) + count($this-&gt;cc) + count($this-&gt;bcc)) &lt; 1) {
                throw new phpmailerException($this-&gt;lang('provide_address'), self::STOP_CRITICAL);
            }

            // Validate From, Sender, and ConfirmReadingTo addresses
            foreach (array('From', 'Sender', 'ConfirmReadingTo') as $address_kind) {
                $this-&gt;$address_kind = trim($this-&gt;$address_kind);
                if (empty($this-&gt;$address_kind)) {
                    continue;
                }
                $this-&gt;$address_kind = $this-&gt;punyencodeAddress($this-&gt;$address_kind);
                if (!$this-&gt;validateAddress($this-&gt;$address_kind)) {
                    $error_message = $this-&gt;lang('invalid_address') . ' (punyEncode) ' . $this-&gt;$address_kind;
                    $this-&gt;setError($error_message);
                    $this-&gt;edebug($error_message);
                    if ($this-&gt;exceptions) {
                        throw new phpmailerException($error_message);
                    }
                    return false;
                }
            }

            // Set whether the message is multipart/alternative
            if ($this-&gt;alternativeExists()) {
                $this-&gt;ContentType = 'multipart/alternative';
            }

            $this-&gt;setMessageType();
            // Refuse to send an empty message unless we are specifically allowing it
            if (!$this-&gt;AllowEmpty and empty($this-&gt;Body)) {
                throw new phpmailerException($this-&gt;lang('empty_message'), self::STOP_CRITICAL);
            }

            // Create body before headers in case body makes changes to headers (e.g. altering transfer encoding)
            $this-&gt;MIMEHeader = '';
            $this-&gt;MIMEBody = $this-&gt;createBody();
            // createBody may have added some headers, so retain them
            $tempheaders = $this-&gt;MIMEHeader;
            $this-&gt;MIMEHeader = $this-&gt;createHeader();
            $this-&gt;MIMEHeader .= $tempheaders;

            // To capture the complete message when using mail(), create
            // an extra header list which createHeader() doesn't fold in
            if ($this-&gt;Mailer == 'mail') {
                if (count($this-&gt;to) &gt; 0) {
                    $this-&gt;mailHeader .= $this-&gt;addrAppend('To', $this-&gt;to);
                } else {
                    $this-&gt;mailHeader .= $this-&gt;headerLine('To', 'undisclosed-recipients:;');
                }
                $this-&gt;mailHeader .= $this-&gt;headerLine(
                    'Subject',
                    $this-&gt;encodeHeader($this-&gt;secureHeader(trim($this-&gt;Subject)))
                );
            }

            // Sign with DKIM if enabled
            if (!empty($this-&gt;DKIM_domain)
                &amp;&amp; !empty($this-&gt;DKIM_private)
                &amp;&amp; !empty($this-&gt;DKIM_selector)
                &amp;&amp; file_exists($this-&gt;DKIM_private)) {
                $header_dkim = $this-&gt;DKIM_Add(
                    $this-&gt;MIMEHeader . $this-&gt;mailHeader,
                    $this-&gt;encodeHeader($this-&gt;secureHeader($this-&gt;Subject)),
                    $this-&gt;MIMEBody
                );
                $this-&gt;MIMEHeader = rtrim($this-&gt;MIMEHeader, "\r\n ") . self::CRLF .
                    str_replace("\r\n", "\n", $header_dkim) . self::CRLF;
            }
            return true;
        } catch (phpmailerException $exc) {
            $this-&gt;setError($exc-&gt;getMessage());
            if ($this-&gt;exceptions) {
                throw $exc;
            }
            return false;
        }
    }

    /**
     * Actually send a message.
     * Send the email via the selected mechanism
     * @throws phpmailerException
     * @return boolean
     */
    public function postSend()
    {
        try {
            // Choose the mailer and send through it
            switch ($this-&gt;Mailer) {
                case 'sendmail':
                case 'qmail':
                    return $this-&gt;sendmailSend($this-&gt;MIMEHeader, $this-&gt;MIMEBody);
                case 'smtp':
                    return $this-&gt;smtpSend($this-&gt;MIMEHeader, $this-&gt;MIMEBody);
                case 'mail':
                    return $this-&gt;mailSend($this-&gt;MIMEHeader, $this-&gt;MIMEBody);
                default:
                    $sendMethod = $this-&gt;Mailer.'Send';
                    if (method_exists($this, $sendMethod)) {
                        return $this-&gt;$sendMethod($this-&gt;MIMEHeader, $this-&gt;MIMEBody);
                    }

                    return $this-&gt;mailSend($this-&gt;MIMEHeader, $this-&gt;MIMEBody);
            }
        } catch (phpmailerException $exc) {
            $this-&gt;setError($exc-&gt;getMessage());
            $this-&gt;edebug($exc-&gt;getMessage());
            if ($this-&gt;exceptions) {
                throw $exc;
            }
        }
        return false;
    }

    /**
     * Send mail using the $Sendmail program.
     * @param string $header The message headers
     * @param string $body The message body
     * @see PHPMailer::$Sendmail
     * @throws phpmailerException
     * @access protected
     * @return boolean
     */
    protected function sendmailSend($header, $body)
    {
        if ($this-&gt;Sender != '') {
            if ($this-&gt;Mailer == 'qmail') {
                $sendmail = sprintf('%s -f%s', escapeshellcmd($this-&gt;Sendmail), escapeshellarg($this-&gt;Sender));
            } else {
                $sendmail = sprintf('%s -oi -f%s -t', escapeshellcmd($this-&gt;Sendmail), escapeshellarg($this-&gt;Sender));
            }
        } else {
            if ($this-&gt;Mailer == 'qmail') {
                $sendmail = sprintf('%s', escapeshellcmd($this-&gt;Sendmail));
            } else {
                $sendmail = sprintf('%s -oi -t', escapeshellcmd($this-&gt;Sendmail));
            }
        }
        if ($this-&gt;SingleTo) {
            foreach ($this-&gt;SingleToArray as $toAddr) {
                if (!@$mail = popen($sendmail, 'w')) {
                    throw new phpmailerException($this-&gt;lang('execute') . $this-&gt;Sendmail, self::STOP_CRITICAL);
                }
                fputs($mail, 'To: ' . $toAddr . "\n");
                fputs($mail, $header);
                fputs($mail, $body);
                $result = pclose($mail);
                $this-&gt;doCallback(
                    ($result == 0),
                    array($toAddr),
                    $this-&gt;cc,
                    $this-&gt;bcc,
                    $this-&gt;Subject,
                    $body,
                    $this-&gt;From
                );
                if ($result != 0) {
                    throw new phpmailerException($this-&gt;lang('execute') . $this-&gt;Sendmail, self::STOP_CRITICAL);
                }
            }
        } else {
            if (!@$mail = popen($sendmail, 'w')) {
                throw new phpmailerException($this-&gt;lang('execute') . $this-&gt;Sendmail, self::STOP_CRITICAL);
            }
            fputs($mail, $header);
            fputs($mail, $body);
            $result = pclose($mail);
            $this-&gt;doCallback(
                ($result == 0),
                $this-&gt;to,
                $this-&gt;cc,
                $this-&gt;bcc,
                $this-&gt;Subject,
                $body,
                $this-&gt;From
            );
            if ($result != 0) {
                throw new phpmailerException($this-&gt;lang('execute') . $this-&gt;Sendmail, self::STOP_CRITICAL);
            }
        }
        return true;
    }

    /**
     * Send mail using the PHP mail() function.
     * @param string $header The message headers
     * @param string $body The message body
     * @link http://www.php.net/manual/en/book.mail.php
     * @throws phpmailerException
     * @access protected
     * @return boolean
     */
    protected function mailSend($header, $body)
    {
        $toArr = array();
        foreach ($this-&gt;to as $toaddr) {
            $toArr[] = $this-&gt;addrFormat($toaddr);
        }
        $to = implode(', ', $toArr);

        if (empty($this-&gt;Sender)) {
            $params = ' ';
        } else {
            $params = sprintf('-f%s', $this-&gt;Sender);
        }
        if ($this-&gt;Sender != '' and !ini_get('safe_mode')) {
            $old_from = ini_get('sendmail_from');
            ini_set('sendmail_from', $this-&gt;Sender);
        }
        $result = false;
        if ($this-&gt;SingleTo &amp;&amp; count($toArr) &gt; 1) {
            foreach ($toArr as $toAddr) {
                $result = $this-&gt;mailPassthru($toAddr, $this-&gt;Subject, $body, $header, $params);
                $this-&gt;doCallback($result, array($toAddr), $this-&gt;cc, $this-&gt;bcc, $this-&gt;Subject, $body, $this-&gt;From);
            }
        } else {
            $result = $this-&gt;mailPassthru($to, $this-&gt;Subject, $body, $header, $params);
            $this-&gt;doCallback($result, $this-&gt;to, $this-&gt;cc, $this-&gt;bcc, $this-&gt;Subject, $body, $this-&gt;From);
        }
        if (isset($old_from)) {
            ini_set('sendmail_from', $old_from);
        }
        if (!$result) {
            throw new phpmailerException($this-&gt;lang('instantiate'), self::STOP_CRITICAL);
        }
        return true;
    }

    /**
     * Get an instance to use for SMTP operations.
     * Override this function to load your own SMTP implementation
     * @return SMTP
     */
    public function getSMTPInstance()
    {
        if (!is_object($this-&gt;smtp)) {
            $this-&gt;smtp = new SMTP;
        }
        return $this-&gt;smtp;
    }

    /**
     * Send mail via SMTP.
     * Returns false if there is a bad MAIL FROM, RCPT, or DATA input.
     * Uses the PHPMailerSMTP class by default.
     * @see PHPMailer::getSMTPInstance() to use a different class.
     * @param string $header The message headers
     * @param string $body The message body
     * @throws phpmailerException
     * @uses SMTP
     * @access protected
     * @return boolean
     */
    protected function smtpSend($header, $body)
    {
        $bad_rcpt = array();
        if (!$this-&gt;smtpConnect($this-&gt;SMTPOptions)) {
            throw new phpmailerException($this-&gt;lang('smtp_connect_failed'), self::STOP_CRITICAL);
        }
        if ('' == $this-&gt;Sender) {
            $smtp_from = $this-&gt;From;
        } else {
            $smtp_from = $this-&gt;Sender;
        }
        if (!$this-&gt;smtp-&gt;mail($smtp_from)) {
            $this-&gt;setError($this-&gt;lang('from_failed') . $smtp_from . ' : ' . implode(',', $this-&gt;smtp-&gt;getError()));
            throw new phpmailerException($this-&gt;ErrorInfo, self::STOP_CRITICAL);
        }

        // Attempt to send to all recipients
        foreach (array($this-&gt;to, $this-&gt;cc, $this-&gt;bcc) as $togroup) {
            foreach ($togroup as $to) {
                if (!$this-&gt;smtp-&gt;recipient($to[0])) {
                    $error = $this-&gt;smtp-&gt;getError();
                    $bad_rcpt[] = array('to' =&gt; $to[0], 'error' =&gt; $error['detail']);
                    $isSent = false;
                } else {
                    $isSent = true;
                }
                $this-&gt;doCallback($isSent, array($to[0]), array(), array(), $this-&gt;Subject, $body, $this-&gt;From);
            }
        }

        // Only send the DATA command if we have viable recipients
        if ((count($this-&gt;all_recipients) &gt; count($bad_rcpt)) and !$this-&gt;smtp-&gt;data($header . $body)) {
            throw new phpmailerException($this-&gt;lang('data_not_accepted'), self::STOP_CRITICAL);
        }
        if ($this-&gt;SMTPKeepAlive) {
            $this-&gt;smtp-&gt;reset();
        } else {
            $this-&gt;smtp-&gt;quit();
            $this-&gt;smtp-&gt;close();
        }
        //Create error message for any bad addresses
        if (count($bad_rcpt) &gt; 0) {
            $errstr = '';
            foreach ($bad_rcpt as $bad) {
                $errstr .= $bad['to'] . ': ' . $bad['error'];
            }
            throw new phpmailerException(
                $this-&gt;lang('recipients_failed') . $errstr,
                self::STOP_CONTINUE
            );
        }
        return true;
    }

    /**
     * Initiate a connection to an SMTP server.
     * Returns false if the operation failed.
     * @param array $options An array of options compatible with stream_context_create()
     * @uses SMTP
     * @access public
     * @throws phpmailerException
     * @return boolean
     */
    public function smtpConnect($options = array())
    {
        if (is_null($this-&gt;smtp)) {
            $this-&gt;smtp = $this-&gt;getSMTPInstance();
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
                            $this-&gt;Workstation
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

    /**
     * Close the active SMTP session if one exists.
     * @return void
     */
    public function smtpClose()
    {
        if ($this-&gt;smtp !== null) {
            if ($this-&gt;smtp-&gt;connected()) {
                $this-&gt;smtp-&gt;quit();
                $this-&gt;smtp-&gt;close();
            }
        }
    }

    /**
     * Set the language for error messages.
     * Returns false if it cannot load the language file.
     * The default language is English.
     * @param string $langcode ISO 639-1 2-character language code (e.g. French is "fr")
     * @param string $lang_path Path to the language file directory, with trailing separator (slash)
     * @return boolean
     * @access public
     */
    public function setLanguage($langcode = 'en', $lang_path = '')
    {
        // Define full set of translatable strings in English
        $PHPMAILER_LANG = array(
            'authenticate' =&gt; 'SMTP Error: Could not authenticate.',
            'connect_host' =&gt; 'SMTP Error: Could not connect to SMTP host.',
            'data_not_accepted' =&gt; 'SMTP Error: data not accepted.',
            'empty_message' =&gt; 'Message body empty',
            'encoding' =&gt; 'Unknown encoding: ',
            'execute' =&gt; 'Could not execute: ',
            'file_access' =&gt; 'Could not access file: ',
            'file_open' =&gt; 'File Error: Could not open file: ',
            'from_failed' =&gt; 'The following From address failed: ',
            'instantiate' =&gt; 'Could not instantiate mail function.',
            'invalid_address' =&gt; 'Invalid address: ',
            'mailer_not_supported' =&gt; ' mailer is not supported.',
            'provide_address' =&gt; 'You must provide at least one recipient email address.',
            'recipients_failed' =&gt; 'SMTP Error: The following recipients failed: ',
            'signing' =&gt; 'Signing Error: ',
            'smtp_connect_failed' =&gt; 'SMTP connect() failed.',
            'smtp_error' =&gt; 'SMTP server error: ',
            'variable_set' =&gt; 'Cannot set or reset variable: ',
            'extension_missing' =&gt; 'Extension missing: '
        );
        if (empty($lang_path)) {
            // Calculate an absolute path so it can work if CWD is not here
            $lang_path = dirname(__FILE__). DIRECTORY_SEPARATOR . 'language'. DIRECTORY_SEPARATOR;
        }
        $foundlang = true;
        $lang_file = $lang_path . 'phpmailer.lang-' . $langcode . '.php';
        // There is no English translation file
        if ($langcode != 'en') {
            // Make sure language file path is readable
            if (!is_readable($lang_file)) {
                $foundlang = false;
            } else {
                // Overwrite language-specific strings.
                // This way we'll never have missing translation keys.
                $foundlang = include $lang_file;
            }
        }
        $this-&gt;language = $PHPMAILER_LANG;
        return (boolean)$foundlang; // Returns false if language not found
    }

    /**
     * Get the array of strings for the current language.
     * @return array
     */
    public function getTranslations()
    {
        return $this-&gt;language;
    }

    /**
     * Create recipient headers.
     * @access public
     * @param string $type
     * @param array $addr An array of recipient,
     * where each recipient is a 2-element indexed array with element 0 containing an address
     * and element 1 containing a name, like:
     * array(array('joe@example.com', 'Joe User'), array('zoe@example.com', 'Zoe User'))
     * @return string
     */
    public function addrAppend($type, $addr)
    {
        $addresses = array();
        foreach ($addr as $address) {
            $addresses[] = $this-&gt;addrFormat($address);
        }
        return $type . ': ' . implode(', ', $addresses) . $this-&gt;LE;
    }

    /**
     * Format an address for use in a message header.
     * @access public
     * @param array $addr A 2-element indexed array, element 0 containing an address, element 1 containing a name
     *      like array('joe@example.com', 'Joe User')
     * @return string
     */
    public function addrFormat($addr)
    {
        if (empty($addr[1])) { // No name provided
            return $this-&gt;secureHeader($addr[0]);
        } else {
            return $this-&gt;encodeHeader($this-&gt;secureHeader($addr[1]), 'phrase') . ' &lt;' . $this-&gt;secureHeader(
                $addr[0]
            ) . '&gt;';
        }
    }

    /**
     * Word-wrap message.
     * For use with mailers that do not automatically perform wrapping
     * and for quoted-printable encoded messages.
     * Original written by philippe.
     * @param string $message The message to wrap
     * @param integer $length The line length to wrap to
     * @param boolean $qp_mode Whether to run in Quoted-Printable mode
     * @access public
     * @return string
     */
    public function wrapText($message, $length, $qp_mode = false)
    {
        if ($qp_mode) {
            $soft_break = sprintf(' =%s', $this-&gt;LE);
        } else {
            $soft_break = $this-&gt;LE;
        }
        // If utf-8 encoding is used, we will need to make sure we don't
        // split multibyte characters when we wrap
        $is_utf8 = (strtolower($this-&gt;CharSet) == 'utf-8');
        $lelen = strlen($this-&gt;LE);
        $crlflen = strlen(self::CRLF);

        $message = $this-&gt;fixEOL($message);
        //Remove a trailing line break
        if (substr($message, -$lelen) == $this-&gt;LE) {
            $message = substr($message, 0, -$lelen);
        }

        //Split message into lines
        $lines = explode($this-&gt;LE, $message);
        //Message will be rebuilt in here
        $message = '';
        foreach ($lines as $line) {
            $words = explode(' ', $line);
            $buf = '';
            $firstword = true;
            foreach ($words as $word) {
                if ($qp_mode and (strlen($word) &gt; $length)) {
                    $space_left = $length - strlen($buf) - $crlflen;
                    if (!$firstword) {
                        if ($space_left &gt; 20) {
                            $len = $space_left;
                            if ($is_utf8) {
                                $len = $this-&gt;utf8CharBoundary($word, $len);
                            } elseif (substr($word, $len - 1, 1) == '=') {
                                $len--;
                            } elseif (substr($word, $len - 2, 1) == '=') {
                                $len -= 2;
                            }
                            $part = substr($word, 0, $len);
                            $word = substr($word, $len);
                            $buf .= ' ' . $part;
                            $message .= $buf . sprintf('=%s', self::CRLF);
                        } else {
                            $message .= $buf . $soft_break;
                        }
                        $buf = '';
                    }
                    while (strlen($word) &gt; 0) {
                        if ($length &lt;= 0) {
                            break;
                        }
                        $len = $length;
                        if ($is_utf8) {
                            $len = $this-&gt;utf8CharBoundary($word, $len);
                        } elseif (substr($word, $len - 1, 1) == '=') {
                            $len--;
                        } elseif (substr($word, $len - 2, 1) == '=') {
                            $len -= 2;
                        }
                        $part = substr($word, 0, $len);
                        $word = substr($word, $len);

                        if (strlen($word) &gt; 0) {
                            $message .= $part . sprintf('=%s', self::CRLF);
                        } else {
                            $buf = $part;
                        }
                    }
                } else {
                    $buf_o = $buf;
                    if (!$firstword) {
                        $buf .= ' ';
                    }
                    $buf .= $word;

                    if (strlen($buf) &gt; $length and $buf_o != '') {
                        $message .= $buf_o . $soft_break;
                        $buf = $word;
                    }
                }
                $firstword = false;
            }
            $message .= $buf . self::CRLF;
        }

        return $message;
    }

    /**
     * Find the last character boundary prior to $maxLength in a utf-8
     * quoted-printable encoded string.
     * Original written by Colin Brown.
     * @access public
     * @param string $encodedText utf-8 QP text
     * @param integer $maxLength Find the last character boundary prior to this length
     * @return integer
     */
    public function utf8CharBoundary($encodedText, $maxLength)
    {
        $foundSplitPos = false;
        $lookBack = 3;
        while (!$foundSplitPos) {
            $lastChunk = substr($encodedText, $maxLength - $lookBack, $lookBack);
            $encodedCharPos = strpos($lastChunk, '=');
            if (false !== $encodedCharPos) {
                // Found start of encoded character byte within $lookBack block.
                // Check the encoded byte value (the 2 chars after the '=')
                $hex = substr($encodedText, $maxLength - $lookBack + $encodedCharPos + 1, 2);
                $dec = hexdec($hex);
                if ($dec &lt; 128) {
                    // Single byte character.
                    // If the encoded char was found at pos 0, it will fit
                    // otherwise reduce maxLength to start of the encoded char
                    if ($encodedCharPos &gt; 0) {
                        $maxLength = $maxLength - ($lookBack - $encodedCharPos);
                    }
                    $foundSplitPos = true;
                } elseif ($dec &gt;= 192) {
                    // First byte of a multi byte character
                    // Reduce maxLength to split at start of character
                    $maxLength = $maxLength - ($lookBack - $encodedCharPos);
                    $foundSplitPos = true;
                } elseif ($dec &lt; 192) {
                    // Middle byte of a multi byte character, look further back
                    $lookBack += 3;
                }
            } else {
                // No encoded character found
                $foundSplitPos = true;
            }
        }
        return $maxLength;
    }

    /**
     * Apply word wrapping to the message body.
     * Wraps the message body to the number of chars set in the WordWrap property.
     * You should only do this to plain-text bodies as wrapping HTML tags may break them.
     * This is called automatically by createBody(), so you don't need to call it yourself.
     * @access public
     * @return void
     */
    public function setWordWrap()
    {
        if ($this-&gt;WordWrap &lt; 1) {
            return;
        }

        switch ($this-&gt;message_type) {
            case 'alt':
            case 'alt_inline':
            case 'alt_attach':
            case 'alt_inline_attach':
                $this-&gt;AltBody = $this-&gt;wrapText($this-&gt;AltBody, $this-&gt;WordWrap);
                break;
            default:
                $this-&gt;Body = $this-&gt;wrapText($this-&gt;Body, $this-&gt;WordWrap);
                break;
        }
    }

    /**
     * Assemble message headers.
     * @access public
     * @return string The assembled headers
     */
    public function createHeader()
    {
        $result = '';

        if ($this-&gt;MessageDate == '') {
            $this-&gt;MessageDate = self::rfcDate();
        }
        $result .= $this-&gt;headerLine('Date', $this-&gt;MessageDate);

        // To be created automatically by mail()
        if ($this-&gt;SingleTo) {
            if ($this-&gt;Mailer != 'mail') {
                foreach ($this-&gt;to as $toaddr) {
                    $this-&gt;SingleToArray[] = $this-&gt;addrFormat($toaddr);
                }
            }
        } else {
            if (count($this-&gt;to) &gt; 0) {
                if ($this-&gt;Mailer != 'mail') {
                    $result .= $this-&gt;addrAppend('To', $this-&gt;to);
                }
            } elseif (count($this-&gt;cc) == 0) {
                $result .= $this-&gt;headerLine('To', 'undisclosed-recipients:;');
            }
        }

        $result .= $this-&gt;addrAppend('From', array(array(trim($this-&gt;From), $this-&gt;FromName)));

        // sendmail and mail() extract Cc from the header before sending
        if (count($this-&gt;cc) &gt; 0) {
            $result .= $this-&gt;addrAppend('Cc', $this-&gt;cc);
        }

        // sendmail and mail() extract Bcc from the header before sending
        if ((
                $this-&gt;Mailer == 'sendmail' or $this-&gt;Mailer == 'qmail' or $this-&gt;Mailer == 'mail'
            )
            and count($this-&gt;bcc) &gt; 0
        ) {
            $result .= $this-&gt;addrAppend('Bcc', $this-&gt;bcc);
        }

        if (count($this-&gt;ReplyTo) &gt; 0) {
            $result .= $this-&gt;addrAppend('Reply-To', $this-&gt;ReplyTo);
        }

        // mail() sets the subject itself
        if ($this-&gt;Mailer != 'mail') {
            $result .= $this-&gt;headerLine('Subject', $this-&gt;encodeHeader($this-&gt;secureHeader($this-&gt;Subject)));
        }

        if ('' != $this-&gt;MessageID and preg_match('/^&lt;.*@.*&gt;$/', $this-&gt;MessageID)) {
            $this-&gt;lastMessageID = $this-&gt;MessageID;
        } else {
            $this-&gt;lastMessageID = sprintf('&lt;%s@%s&gt;', $this-&gt;uniqueid, $this-&gt;serverHostname());
        }
        $result .= $this-&gt;headerLine('Message-ID', $this-&gt;lastMessageID);
        if (!is_null($this-&gt;Priority)) {
            $result .= $this-&gt;headerLine('X-Priority', $this-&gt;Priority);
        }
        if ($this-&gt;XMailer == '') {
            $result .= $this-&gt;headerLine(
                'X-Mailer',
                'PHPMailer ' . $this-&gt;Version . ' (https://github.com/PHPMailer/PHPMailer)'
            );
        } else {
            $myXmailer = trim($this-&gt;XMailer);
            if ($myXmailer) {
                $result .= $this-&gt;headerLine('X-Mailer', $myXmailer);
            }
        }

        if ($this-&gt;ConfirmReadingTo != '') {
            $result .= $this-&gt;headerLine('Disposition-Notification-To', '&lt;' . $this-&gt;ConfirmReadingTo . '&gt;');
        }

        // Add custom headers
        foreach ($this-&gt;CustomHeader as $header) {
            $result .= $this-&gt;headerLine(
                trim($header[0]),
                $this-&gt;encodeHeader(trim($header[1]))
            );
        }
        if (!$this-&gt;sign_key_file) {
            $result .= $this-&gt;headerLine('MIME-Version', '1.0');
            $result .= $this-&gt;getMailMIME();
        }

        return $result;
    }

    /**
     * Get the message MIME type headers.
     * @access public
     * @return string
     */
    public function getMailMIME()
    {
        $result = '';
        $ismultipart = true;
        switch ($this-&gt;message_type) {
            case 'inline':
                $result .= $this-&gt;headerLine('Content-Type', 'multipart/related;');
                $result .= $this-&gt;textLine("\tboundary=\"" . $this-&gt;boundary[1] . '"');
                break;
            case 'attach':
            case 'inline_attach':
            case 'alt_attach':
            case 'alt_inline_attach':
                $result .= $this-&gt;headerLine('Content-Type', 'multipart/mixed;');
                $result .= $this-&gt;textLine("\tboundary=\"" . $this-&gt;boundary[1] . '"');
                break;
            case 'alt':
            case 'alt_inline':
                $result .= $this-&gt;headerLine('Content-Type', 'multipart/alternative;');
                $result .= $this-&gt;textLine("\tboundary=\"" . $this-&gt;boundary[1] . '"');
                break;
            default:
                // Catches case 'plain': and case '':
                $result .= $this-&gt;textLine('Content-Type: ' . $this-&gt;ContentType . '; charset=' . $this-&gt;CharSet);
                $ismultipart = false;
                break;
        }
        // RFC1341 part 5 says 7bit is assumed if not specified
        if ($this-&gt;Encoding != '7bit') {
            // RFC 2045 section 6.4 says multipart MIME parts may only use 7bit, 8bit or binary CTE
            if ($ismultipart) {
                if ($this-&gt;Encoding == '8bit') {
                    $result .= $this-&gt;headerLine('Content-Transfer-Encoding', '8bit');
                }
                // The only remaining alternatives are quoted-printable and base64, which are both 7bit compatible
            } else {
                $result .= $this-&gt;headerLine('Content-Transfer-Encoding', $this-&gt;Encoding);
            }
        }

        if ($this-&gt;Mailer != 'mail') {
            $result .= $this-&gt;LE;
        }

        return $result;
    }

    /**
     * Returns the whole MIME message.
     * Includes complete headers and body.
     * Only valid post preSend().
     * @see PHPMailer::preSend()
     * @access public
     * @return string
     */
    public function getSentMIMEMessage()
    {
        return rtrim($this-&gt;MIMEHeader . $this-&gt;mailHeader, "\n\r") . self::CRLF . self::CRLF . $this-&gt;MIMEBody;
    }

    /**
     * Assemble the message body.
     * Returns an empty string on failure.
     * @access public
     * @throws phpmailerException
     * @return string The assembled message body
     */
    public function createBody()
    {
        $body = '';
        //Create unique IDs and preset boundaries
        $this-&gt;uniqueid = md5(uniqid(time()));
        $this-&gt;boundary[1] = 'b1_' . $this-&gt;uniqueid;
        $this-&gt;boundary[2] = 'b2_' . $this-&gt;uniqueid;
        $this-&gt;boundary[3] = 'b3_' . $this-&gt;uniqueid;

        if ($this-&gt;sign_key_file) {
            $body .= $this-&gt;getMailMIME() . $this-&gt;LE;
        }

        $this-&gt;setWordWrap();

        $bodyEncoding = $this-&gt;Encoding;
        $bodyCharSet = $this-&gt;CharSet;
        //Can we do a 7-bit downgrade?
        if ($bodyEncoding == '8bit' and !$this-&gt;has8bitChars($this-&gt;Body)) {
            $bodyEncoding = '7bit';
            $bodyCharSet = 'us-ascii';
        }
        //If lines are too long, and we're not already using an encoding that will shorten them,
        //change to quoted-printable transfer encoding
        if ('base64' != $this-&gt;Encoding and self::hasLineLongerThanMax($this-&gt;Body)) {
            $this-&gt;Encoding = 'quoted-printable';
            $bodyEncoding = 'quoted-printable';
        }

        $altBodyEncoding = $this-&gt;Encoding;
        $altBodyCharSet = $this-&gt;CharSet;
        //Can we do a 7-bit downgrade?
        if ($altBodyEncoding == '8bit' and !$this-&gt;has8bitChars($this-&gt;AltBody)) {
            $altBodyEncoding = '7bit';
            $altBodyCharSet = 'us-ascii';
        }
        //If lines are too long, and we're not already using an encoding that will shorten them,
        //change to quoted-printable transfer encoding
        if ('base64' != $altBodyEncoding and self::hasLineLongerThanMax($this-&gt;AltBody)) {
            $altBodyEncoding = 'quoted-printable';
        }
        //Use this as a preamble in all multipart message types
        $mimepre = "This is a multi-part message in MIME format." . $this-&gt;LE . $this-&gt;LE;
        switch ($this-&gt;message_type) {
            case 'inline':
                $body .= $mimepre;
                $body .= $this-&gt;getBoundary($this-&gt;boundary[1], $bodyCharSet, '', $bodyEncoding);
                $body .= $this-&gt;encodeString($this-&gt;Body, $bodyEncoding);
                $body .= $this-&gt;LE . $this-&gt;LE;
                $body .= $this-&gt;attachAll('inline', $this-&gt;boundary[1]);
                break;
            case 'attach':
                $body .= $mimepre;
                $body .= $this-&gt;getBoundary($this-&gt;boundary[1], $bodyCharSet, '', $bodyEncoding);
                $body .= $this-&gt;encodeString($this-&gt;Body, $bodyEncoding);
                $body .= $this-&gt;LE . $this-&gt;LE;
                $body .= $this-&gt;attachAll('attachment', $this-&gt;boundary[1]);
                break;
            case 'inline_attach':
                $body .= $mimepre;
                $body .= $this-&gt;textLine('--' . $this-&gt;boundary[1]);
                $body .= $this-&gt;headerLine('Content-Type', 'multipart/related;');
                $body .= $this-&gt;textLine("\tboundary=\"" . $this-&gt;boundary[2] . '"');
                $body .= $this-&gt;LE;
                $body .= $this-&gt;getBoundary($this-&gt;boundary[2], $bodyCharSet, '', $bodyEncoding);
                $body .= $this-&gt;encodeString($this-&gt;Body, $bodyEncoding);
                $body .= $this-&gt;LE . $this-&gt;LE;
                $body .= $this-&gt;attachAll('inline', $this-&gt;boundary[2]);
                $body .= $this-&gt;LE;
                $body .= $this-&gt;attachAll('attachment', $this-&gt;boundary[1]);
                break;
            case 'alt':
                $body .= $mimepre;
                $body .= $this-&gt;getBoundary($this-&gt;boundary[1], $altBodyCharSet, 'text/plain', $altBodyEncoding);
                $body .= $this-&gt;encodeString($this-&gt;AltBody, $altBodyEncoding);
                $body .= $this-&gt;LE . $this-&gt;LE;
                $body .= $this-&gt;getBoundary($this-&gt;boundary[1], $bodyCharSet, 'text/html', $bodyEncoding);
                $body .= $this-&gt;encodeString($this-&gt;Body, $bodyEncoding);
                $body .= $this-&gt;LE . $this-&gt;LE;
                if (!empty($this-&gt;Ical)) {
                    $body .= $this-&gt;getBoundary($this-&gt;boundary[1], '', 'text/calendar; method=REQUEST', '');
                    $body .= $this-&gt;encodeString($this-&gt;Ical, $this-&gt;Encoding);
                    $body .= $this-&gt;LE . $this-&gt;LE;
                }
                $body .= $this-&gt;endBoundary($this-&gt;boundary[1]);
                break;
            case 'alt_inline':
                $body .= $mimepre;
                $body .= $this-&gt;getBoundary($this-&gt;boundary[1], $altBodyCharSet, 'text/plain', $altBodyEncoding);
                $body .= $this-&gt;encodeString($this-&gt;AltBody, $altBodyEncoding);
                $body .= $this-&gt;LE . $this-&gt;LE;
                $body .= $this-&gt;textLine('--' . $this-&gt;boundary[1]);
                $body .= $this-&gt;headerLine('Content-Type', 'multipart/related;');
                $body .= $this-&gt;textLine("\tboundary=\"" . $this-&gt;boundary[2] . '"');
                $body .= $this-&gt;LE;
                $body .= $this-&gt;getBoundary($this-&gt;boundary[2], $bodyCharSet, 'text/html', $bodyEncoding);
                $body .= $this-&gt;encodeString($this-&gt;Body, $bodyEncoding);
                $body .= $this-&gt;LE . $this-&gt;LE;
                $body .= $this-&gt;attachAll('inline', $this-&gt;boundary[2]);
                $body .= $this-&gt;LE;
                $body .= $this-&gt;endBoundary($this-&gt;boundary[1]);
                break;
            case 'alt_attach':
                $body .= $mimepre;
                $body .= $this-&gt;textLine('--' . $this-&gt;boundary[1]);
                $body .= $this-&gt;headerLine('Content-Type', 'multipart/alternative;');
                $body .= $this-&gt;textLine("\tboundary=\"" . $this-&gt;boundary[2] . '"');
                $body .= $this-&gt;LE;
                $body .= $this-&gt;getBoundary($this-&gt;boundary[2], $altBodyCharSet, 'text/plain', $altBodyEncoding);
                $body .= $this-&gt;encodeString($this-&gt;AltBody, $altBodyEncoding);
                $body .= $this-&gt;LE . $this-&gt;LE;
                $body .= $this-&gt;getBoundary($this-&gt;boundary[2], $bodyCharSet, 'text/html', $bodyEncoding);
                $body .= $this-&gt;encodeString($this-&gt;Body, $bodyEncoding);
                $body .= $this-&gt;LE . $this-&gt;LE;
                $body .= $this-&gt;endBoundary($this-&gt;boundary[2]);
                $body .= $this-&gt;LE;
                $body .= $this-&gt;attachAll('attachment', $this-&gt;boundary[1]);
                break;
            case 'alt_inline_attach':
                $body .= $mimepre;
                $body .= $this-&gt;textLine('--' . $this-&gt;boundary[1]);
                $body .= $this-&gt;headerLine('Content-Type', 'multipart/alternative;');
                $body .= $this-&gt;textLine("\tboundary=\"" . $this-&gt;boundary[2] . '"');
                $body .= $this-&gt;LE;
                $body .= $this-&gt;getBoundary($this-&gt;boundary[2], $altBodyCharSet, 'text/plain', $altBodyEncoding);
                $body .= $this-&gt;encodeString($this-&gt;AltBody, $altBodyEncoding);
                $body .= $this-&gt;LE . $this-&gt;LE;
                $body .= $this-&gt;textLine('--' . $this-&gt;boundary[2]);
                $body .= $this-&gt;headerLine('Content-Type', 'multipart/related;');
                $body .= $this-&gt;textLine("\tboundary=\"" . $this-&gt;boundary[3] . '"');
                $body .= $this-&gt;LE;
                $body .= $this-&gt;getBoundary($this-&gt;boundary[3], $bodyCharSet, 'text/html', $bodyEncoding);
                $body .= $this-&gt;encodeString($this-&gt;Body, $bodyEncoding);
                $body .= $this-&gt;LE . $this-&gt;LE;
                $body .= $this-&gt;attachAll('inline', $this-&gt;boundary[3]);
                $body .= $this-&gt;LE;
                $body .= $this-&gt;endBoundary($this-&gt;boundary[2]);
                $body .= $this-&gt;LE;
                $body .= $this-&gt;attachAll('attachment', $this-&gt;boundary[1]);
                break;
            default:
                // catch case 'plain' and case ''
                $body .= $this-&gt;encodeString($this-&gt;Body, $bodyEncoding);
                break;
        }

        if ($this-&gt;isError()) {
            $body = '';
        } elseif ($this-&gt;sign_key_file) {
            try {
                if (!defined('PKCS7_TEXT')) {
                    throw new phpmailerException($this-&gt;lang('extension_missing') . 'openssl');
                }
                // @TODO would be nice to use php://temp streams here, but need to wrap for PHP &lt; 5.1
                $file = tempnam(sys_get_temp_dir(), 'mail');
                if (false === file_put_contents($file, $body)) {
                    throw new phpmailerException($this-&gt;lang('signing') . ' Could not write temp file');
                }
                $signed = tempnam(sys_get_temp_dir(), 'signed');
                //Workaround for PHP bug https://bugs.php.net/bug.php?id=69197
                if (empty($this-&gt;sign_extracerts_file)) {
                    $sign = @openssl_pkcs7_sign(
                        $file,
                        $signed,
                        'file://' . realpath($this-&gt;sign_cert_file),
                        array('file://' . realpath($this-&gt;sign_key_file), $this-&gt;sign_key_pass),
                        null
                    );
                } else {
                    $sign = @openssl_pkcs7_sign(
                        $file,
                        $signed,
                        'file://' . realpath($this-&gt;sign_cert_file),
                        array('file://' . realpath($this-&gt;sign_key_file), $this-&gt;sign_key_pass),
                        null,
                        PKCS7_DETACHED,
                        $this-&gt;sign_extracerts_file
                    );
                }
                if ($sign) {
                    @unlink($file);
                    $body = file_get_contents($signed);
                    @unlink($signed);
                    //The message returned by openssl contains both headers and body, so need to split them up
                    $parts = explode("\n\n", $body, 2);
                    $this-&gt;MIMEHeader .= $parts[0] . $this-&gt;LE . $this-&gt;LE;
                    $body = $parts[1];
                } else {
                    @unlink($file);
                    @unlink($signed);
                    throw new phpmailerException($this-&gt;lang('signing') . openssl_error_string());
                }
            } catch (phpmailerException $exc) {
                $body = '';
                if ($this-&gt;exceptions) {
                    throw $exc;
                }
            }
        }
        return $body;
    }

    /**
     * Return the start of a message boundary.
     * @access protected
     * @param string $boundary
     * @param string $charSet
     * @param string $contentType
     * @param string $encoding
     * @return string
     */
    protected function getBoundary($boundary, $charSet, $contentType, $encoding)
    {
        $result = '';
        if ($charSet == '') {
            $charSet = $this-&gt;CharSet;
        }
        if ($contentType == '') {
            $contentType = $this-&gt;ContentType;
        }
        if ($encoding == '') {
            $encoding = $this-&gt;Encoding;
        }
        $result .= $this-&gt;textLine('--' . $boundary);
        $result .= sprintf('Content-Type: %s; charset=%s', $contentType, $charSet);
        $result .= $this-&gt;LE;
        // RFC1341 part 5 says 7bit is assumed if not specified
        if ($encoding != '7bit') {
            $result .= $this-&gt;headerLine('Content-Transfer-Encoding', $encoding);
        }
        $result .= $this-&gt;LE;

        return $result;
    }

    /**
     * Return the end of a message boundary.
     * @access protected
     * @param string $boundary
     * @return string
     */
    protected function endBoundary($boundary)
    {
        return $this-&gt;LE . '--' . $boundary . '--' . $this-&gt;LE;
    }

    /**
     * Set the message type.
     * PHPMailer only supports some preset message types,
     * not arbitrary MIME structures.
     * @access protected
     * @return void
     */
    protected function setMessageType()
    {
        $type = array();
        if ($this-&gt;alternativeExists()) {
            $type[] = 'alt';
        }
        if ($this-&gt;inlineImageExists()) {
            $type[] = 'inline';
        }
        if ($this-&gt;attachmentExists()) {
            $type[] = 'attach';
        }
        $this-&gt;message_type = implode('_', $type);
        if ($this-&gt;message_type == '') {
            $this-&gt;message_type = 'plain';
        }
    }

    /**
     * Format a header line.
     * @access public
     * @param string $name
     * @param string $value
     * @return string
     */
    public function headerLine($name, $value)
    {
        return $name . ': ' . $value . $this-&gt;LE;
    }

    /**
     * Return a formatted mail line.
     * @access public
     * @param string $value
     * @return string
     */
    public function textLine($value)
    {
        return $value . $this-&gt;LE;
    }

    /**
     * Add an attachment from a path on the filesystem.
     * Returns false if the file could not be found or read.
     * @param string $path Path to the attachment.
     * @param string $name Overrides the attachment name.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File extension (MIME) type.
     * @param string $disposition Disposition to use
     * @throws phpmailerException
     * @return boolean
     */
    public function addAttachment($path, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment')
    {
        try {
            if (!@is_file($path)) {
                throw new phpmailerException($this-&gt;lang('file_access') . $path, self::STOP_CONTINUE);
            }

            // If a MIME type is not specified, try to work it out from the file name
            if ($type == '') {
                $type = self::filenameToType($path);
            }

            $filename = basename($path);
            if ($name == '') {
                $name = $filename;
            }

            $this-&gt;attachment[] = array(
                0 =&gt; $path,
                1 =&gt; $filename,
                2 =&gt; $name,
                3 =&gt; $encoding,
                4 =&gt; $type,
                5 =&gt; false, // isStringAttachment
                6 =&gt; $disposition,
                7 =&gt; 0
            );

        } catch (phpmailerException $exc) {
            $this-&gt;setError($exc-&gt;getMessage());
            $this-&gt;edebug($exc-&gt;getMessage());
            if ($this-&gt;exceptions) {
                throw $exc;
            }
            return false;
        }
        return true;
    }

    /**
     * Return the array of attachments.
     * @return array
     */
    public function getAttachments()
    {
        return $this-&gt;attachment;
    }

    /**
     * Attach all file, string, and binary attachments to the message.
     * Returns an empty string on failure.
     * @access protected
     * @param string $disposition_type
     * @param string $boundary
     * @return string
     */
    protected function attachAll($disposition_type, $boundary)
    {
        // Return text of body
        $mime = array();
        $cidUniq = array();
        $incl = array();

        // Add all attachments
        foreach ($this-&gt;attachment as $attachment) {
            // Check if it is a valid disposition_filter
            if ($attachment[6] == $disposition_type) {
                // Check for string attachment
                $string = '';
                $path = '';
                $bString = $attachment[5];
                if ($bString) {
                    $string = $attachment[0];
                } else {
                    $path = $attachment[0];
                }

                $inclhash = md5(serialize($attachment));
                if (in_array($inclhash, $incl)) {
                    continue;
                }
                $incl[] = $inclhash;
                $name = $attachment[2];
                $encoding = $attachment[3];
                $type = $attachment[4];
                $disposition = $attachment[6];
                $cid = $attachment[7];
                if ($disposition == 'inline' &amp;&amp; array_key_exists($cid, $cidUniq)) {
                    continue;
                }
                $cidUniq[$cid] = true;

                $mime[] = sprintf('--%s%s', $boundary, $this-&gt;LE);
                //Only include a filename property if we have one
                if (!empty($name)) {
                    $mime[] = sprintf(
                        'Content-Type: %s; name="%s"%s',
                        $type,
                        $this-&gt;encodeHeader($this-&gt;secureHeader($name)),
                        $this-&gt;LE
                    );
                } else {
                    $mime[] = sprintf(
                        'Content-Type: %s%s',
                        $type,
                        $this-&gt;LE
                    );
                }
                // RFC1341 part 5 says 7bit is assumed if not specified
                if ($encoding != '7bit') {
                    $mime[] = sprintf('Content-Transfer-Encoding: %s%s', $encoding, $this-&gt;LE);
                }

                if ($disposition == 'inline') {
                    $mime[] = sprintf('Content-ID: &lt;%s&gt;%s', $cid, $this-&gt;LE);
                }

                // If a filename contains any of these chars, it should be quoted,
                // but not otherwise: RFC2183 &amp; RFC2045 5.1
                // Fixes a warning in IETF's msglint MIME checker
                // Allow for bypassing the Content-Disposition header totally
                if (!(empty($disposition))) {
                    $encoded_name = $this-&gt;encodeHeader($this-&gt;secureHeader($name));
                    if (preg_match('/[ \(\)&lt;&gt;@,;:\\"\/\[\]\?=]/', $encoded_name)) {
                        $mime[] = sprintf(
                            'Content-Disposition: %s; filename="%s"%s',
                            $disposition,
                            $encoded_name,
                            $this-&gt;LE . $this-&gt;LE
                        );
                    } else {
                        if (!empty($encoded_name)) {
                            $mime[] = sprintf(
                                'Content-Disposition: %s; filename=%s%s',
                                $disposition,
                                $encoded_name,
                                $this-&gt;LE . $this-&gt;LE
                            );
                        } else {
                            $mime[] = sprintf(
                                'Content-Disposition: %s%s',
                                $disposition,
                                $this-&gt;LE . $this-&gt;LE
                            );
                        }
                    }
                } else {
                    $mime[] = $this-&gt;LE;
                }

                // Encode as string attachment
                if ($bString) {
                    $mime[] = $this-&gt;encodeString($string, $encoding);
                    if ($this-&gt;isError()) {
                        return '';
                    }
                    $mime[] = $this-&gt;LE . $this-&gt;LE;
                } else {
                    $mime[] = $this-&gt;encodeFile($path, $encoding);
                    if ($this-&gt;isError()) {
                        return '';
                    }
                    $mime[] = $this-&gt;LE . $this-&gt;LE;
                }
            }
        }

        $mime[] = sprintf('--%s--%s', $boundary, $this-&gt;LE);

        return implode('', $mime);
    }

    /**
     * Encode a file attachment in requested format.
     * Returns an empty string on failure.
     * @param string $path The full path to the file
     * @param string $encoding The encoding to use; one of 'base64', '7bit', '8bit', 'binary', 'quoted-printable'
     * @throws phpmailerException
     * @access protected
     * @return string
     */
    protected function encodeFile($path, $encoding = 'base64')
    {
        try {
            if (!is_readable($path)) {
                throw new phpmailerException($this-&gt;lang('file_open') . $path, self::STOP_CONTINUE);
            }
            $magic_quotes = get_magic_quotes_runtime();
            if ($magic_quotes) {
                if (version_compare(PHP_VERSION, '5.3.0', '&lt;')) {
                    set_magic_quotes_runtime(false);
                } else {
                    //Doesn't exist in PHP 5.4, but we don't need to check because
                    //get_magic_quotes_runtime always returns false in 5.4+
                    //so it will never get here
                    ini_set('magic_quotes_runtime', false);
                }
            }
            $file_buffer = file_get_contents($path);
            $file_buffer = $this-&gt;encodeString($file_buffer, $encoding);
            if ($magic_quotes) {
                if (version_compare(PHP_VERSION, '5.3.0', '&lt;')) {
                    set_magic_quotes_runtime($magic_quotes);
                } else {
                    ini_set('magic_quotes_runtime', $magic_quotes);
                }
            }
            return $file_buffer;
        } catch (Exception $exc) {
            $this-&gt;setError($exc-&gt;getMessage());
            return '';
        }
    }

    /**
     * Encode a string in requested format.
     * Returns an empty string on failure.
     * @param string $str The text to encode
     * @param string $encoding The encoding to use; one of 'base64', '7bit', '8bit', 'binary', 'quoted-printable'
     * @access public
     * @return string
     */
    public function encodeString($str, $encoding = 'base64')
    {
        $encoded = '';
        switch (strtolower($encoding)) {
            case 'base64':
                $encoded = chunk_split(base64_encode($str), 76, $this-&gt;LE);
                break;
            case '7bit':
            case '8bit':
                $encoded = $this-&gt;fixEOL($str);
                // Make sure it ends with a line break
                if (substr($encoded, -(strlen($this-&gt;LE))) != $this-&gt;LE) {
                    $encoded .= $this-&gt;LE;
                }
                break;
            case 'binary':
                $encoded = $str;
                break;
            case 'quoted-printable':
                $encoded = $this-&gt;encodeQP($str);
                break;
            default:
                $this-&gt;setError($this-&gt;lang('encoding') . $encoding);
                break;
        }
        return $encoded;
    }

    /**
     * Encode a header string optimally.
     * Picks shortest of Q, B, quoted-printable or none.
     * @access public
     * @param string $str
     * @param string $position
     * @return string
     */
    public function encodeHeader($str, $position = 'text')
    {
        $matchcount = 0;
        switch (strtolower($position)) {
            case 'phrase':
                if (!preg_match('/[\200-\377]/', $str)) {
                    // Can't use addslashes as we don't know the value of magic_quotes_sybase
                    $encoded = addcslashes($str, "\0..\37\177\\\"");
                    if (($str == $encoded) &amp;&amp; !preg_match('/[^A-Za-z0-9!#$%&amp;\'*+\/=?^_`{|}~ -]/', $str)) {
                        return ($encoded);
                    } else {
                        return ("\"$encoded\"");
                    }
                }
                $matchcount = preg_match_all('/[^\040\041\043-\133\135-\176]/', $str, $matches);
                break;
            /** @noinspection PhpMissingBreakStatementInspection */
            case 'comment':
                $matchcount = preg_match_all('/[()"]/', $str, $matches);
                // Intentional fall-through
            case 'text':
            default:
                $matchcount += preg_match_all('/[\000-\010\013\014\016-\037\177-\377]/', $str, $matches);
                break;
        }

        //There are no chars that need encoding
        if ($matchcount == 0) {
            return ($str);
        }

        $maxlen = 75 - 7 - strlen($this-&gt;CharSet);
        // Try to select the encoding which should produce the shortest output
        if ($matchcount &gt; strlen($str) / 3) {
            // More than a third of the content will need encoding, so B encoding will be most efficient
            $encoding = 'B';
            if (function_exists('mb_strlen') &amp;&amp; $this-&gt;hasMultiBytes($str)) {
                // Use a custom function which correctly encodes and wraps long
                // multibyte strings without breaking lines within a character
                $encoded = $this-&gt;base64EncodeWrapMB($str, "\n");
            } else {
                $encoded = base64_encode($str);
                $maxlen -= $maxlen % 4;
                $encoded = trim(chunk_split($encoded, $maxlen, "\n"));
            }
        } else {
            $encoding = 'Q';
            $encoded = $this-&gt;encodeQ($str, $position);
            $encoded = $this-&gt;wrapText($encoded, $maxlen, true);
            $encoded = str_replace('=' . self::CRLF, "\n", trim($encoded));
        }

        $encoded = preg_replace('/^(.*)$/m', ' =?' . $this-&gt;CharSet . "?$encoding?\\1?=", $encoded);
        $encoded = trim(str_replace("\n", $this-&gt;LE, $encoded));

        return $encoded;
    }

    /**
     * Check if a string contains multi-byte characters.
     * @access public
     * @param string $str multi-byte text to wrap encode
     * @return boolean
     */
    public function hasMultiBytes($str)
    {
        if (function_exists('mb_strlen')) {
            return (strlen($str) &gt; mb_strlen($str, $this-&gt;CharSet));
        } else { // Assume no multibytes (we can't handle without mbstring functions anyway)
            return false;
        }
    }

    /**
     * Does a string contain any 8-bit chars (in any charset)?
     * @param string $text
     * @return boolean
     */
    public function has8bitChars($text)
    {
        return (boolean)preg_match('/[\x80-\xFF]/', $text);
    }

    /**
     * Encode and wrap long multibyte strings for mail headers
     * without breaking lines within a character.
     * Adapted from a function by paravoid
     * @link http://www.php.net/manual/en/function.mb-encode-mimeheader.php#60283
     * @access public
     * @param string $str multi-byte text to wrap encode
     * @param string $linebreak string to use as linefeed/end-of-line
     * @return string
     */
    public function base64EncodeWrapMB($str, $linebreak = null)
    {
        $start = '=?' . $this-&gt;CharSet . '?B?';
        $end = '?=';
        $encoded = '';
        if ($linebreak === null) {
            $linebreak = $this-&gt;LE;
        }

        $mb_length = mb_strlen($str, $this-&gt;CharSet);
        // Each line must have length &lt;= 75, including $start and $end
        $length = 75 - strlen($start) - strlen($end);
        // Average multi-byte ratio
        $ratio = $mb_length / strlen($str);
        // Base64 has a 4:3 ratio
        $avgLength = floor($length * $ratio * .75);

        for ($i = 0; $i &lt; $mb_length; $i += $offset) {
            $lookBack = 0;
            do {
                $offset = $avgLength - $lookBack;
                $chunk = mb_substr($str, $i, $offset, $this-&gt;CharSet);
                $chunk = base64_encode($chunk);
                $lookBack++;
            } while (strlen($chunk) &gt; $length);
            $encoded .= $chunk . $linebreak;
        }

        // Chomp the last linefeed
        $encoded = substr($encoded, 0, -strlen($linebreak));
        return $encoded;
    }

    /**
     * Encode a string in quoted-printable format.
     * According to RFC2045 section 6.7.
     * @access public
     * @param string $string The text to encode
     * @param integer $line_max Number of chars allowed on a line before wrapping
     * @return string
     * @link http://www.php.net/manual/en/function.quoted-printable-decode.php#89417 Adapted from this comment
     */
    public function encodeQP($string, $line_max = 76)
    {
        // Use native function if it's available (&gt;= PHP5.3)
        if (function_exists('quoted_printable_encode')) {
            return quoted_printable_encode($string);
        }
        // Fall back to a pure PHP implementation
        $string = str_replace(
            array('%20', '%0D%0A.', '%0D%0A', '%'),
            array(' ', "\r\n=2E", "\r\n", '='),
            rawurlencode($string)
        );
        return preg_replace('/[^\r\n]{' . ($line_max - 3) . '}[^=\r\n]{2}/', "$0=\r\n", $string);
    }

    /**
     * Backward compatibility wrapper for an old QP encoding function that was removed.
     * @see PHPMailer::encodeQP()
     * @access public
     * @param string $string
     * @param integer $line_max
     * @param boolean $space_conv
     * @return string
     * @deprecated Use encodeQP instead.
     */
    public function encodeQPphp(
        $string,
        $line_max = 76,
        /** @noinspection PhpUnusedParameterInspection */ $space_conv = false
    ) {
        return $this-&gt;encodeQP($string, $line_max);
    }

    /**
     * Encode a string using Q encoding.
     * @link http://tools.ietf.org/html/rfc2047
     * @param string $str the text to encode
     * @param string $position Where the text is going to be used, see the RFC for what that means
     * @access public
     * @return string
     */
    public function encodeQ($str, $position = 'text')
    {
        // There should not be any EOL in the string
        $pattern = '';
        $encoded = str_replace(array("\r", "\n"), '', $str);
        switch (strtolower($position)) {
            case 'phrase':
                // RFC 2047 section 5.3
                $pattern = '^A-Za-z0-9!*+\/ -';
                break;
            /** @noinspection PhpMissingBreakStatementInspection */
            case 'comment':
                // RFC 2047 section 5.2
                $pattern = '\(\)"';
                // intentional fall-through
                // for this reason we build the $pattern without including delimiters and []
            case 'text':
            default:
                // RFC 2047 section 5.1
                // Replace every high ascii, control, =, ? and _ characters
                $pattern = '\000-\011\013\014\016-\037\075\077\137\177-\377' . $pattern;
                break;
        }
        $matches = array();
        if (preg_match_all("/[{$pattern}]/", $encoded, $matches)) {
            // If the string contains an '=', make sure it's the first thing we replace
            // so as to avoid double-encoding
            $eqkey = array_search('=', $matches[0]);
            if (false !== $eqkey) {
                unset($matches[0][$eqkey]);
                array_unshift($matches[0], '=');
            }
            foreach (array_unique($matches[0]) as $char) {
                $encoded = str_replace($char, '=' . sprintf('%02X', ord($char)), $encoded);
            }
        }
        // Replace every spaces to _ (more readable than =20)
        return str_replace(' ', '_', $encoded);
    }

    /**
     * Add a string or binary attachment (non-filesystem).
     * This method can be used to attach ascii or binary data,
     * such as a BLOB record from a database.
     * @param string $string String attachment data.
     * @param string $filename Name of the attachment.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File extension (MIME) type.
     * @param string $disposition Disposition to use
     * @return void
     */
    public function addStringAttachment(
        $string,
        $filename,
        $encoding = 'base64',
        $type = '',
        $disposition = 'attachment'
    ) {
        // If a MIME type is not specified, try to work it out from the file name
        if ($type == '') {
            $type = self::filenameToType($filename);
        }
        // Append to $attachment array
        $this-&gt;attachment[] = array(
            0 =&gt; $string,
            1 =&gt; $filename,
            2 =&gt; basename($filename),
            3 =&gt; $encoding,
            4 =&gt; $type,
            5 =&gt; true, // isStringAttachment
            6 =&gt; $disposition,
            7 =&gt; 0
        );
    }

    /**
     * Add an embedded (inline) attachment from a file.
     * This can include images, sounds, and just about any other document type.
     * These differ from 'regular' attachments in that they are intended to be
     * displayed inline with the message, not just attached for download.
     * This is used in HTML messages that embed the images
     * the HTML refers to using the $cid value.
     * @param string $path Path to the attachment.
     * @param string $cid Content ID of the attachment; Use this to reference
     *        the content when using an embedded image in HTML.
     * @param string $name Overrides the attachment name.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File MIME type.
     * @param string $disposition Disposition to use
     * @return boolean True on successfully adding an attachment
     */
    public function addEmbeddedImage($path, $cid, $name = '', $encoding = 'base64', $type = '', $disposition = 'inline')
    {
        if (!@is_file($path)) {
            $this-&gt;setError($this-&gt;lang('file_access') . $path);
            return false;
        }

        // If a MIME type is not specified, try to work it out from the file name
        if ($type == '') {
            $type = self::filenameToType($path);
        }

        $filename = basename($path);
        if ($name == '') {
            $name = $filename;
        }

        // Append to $attachment array
        $this-&gt;attachment[] = array(
            0 =&gt; $path,
            1 =&gt; $filename,
            2 =&gt; $name,
            3 =&gt; $encoding,
            4 =&gt; $type,
            5 =&gt; false, // isStringAttachment
            6 =&gt; $disposition,
            7 =&gt; $cid
        );
        return true;
    }

    /**
     * Add an embedded stringified attachment.
     * This can include images, sounds, and just about any other document type.
     * Be sure to set the $type to an image type for images:
     * JPEG images use 'image/jpeg', GIF uses 'image/gif', PNG uses 'image/png'.
     * @param string $string The attachment binary data.
     * @param string $cid Content ID of the attachment; Use this to reference
     *        the content when using an embedded image in HTML.
     * @param string $name
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type MIME type.
     * @param string $disposition Disposition to use
     * @return boolean True on successfully adding an attachment
     */
    public function addStringEmbeddedImage(
        $string,
        $cid,
        $name = '',
        $encoding = 'base64',
        $type = '',
        $disposition = 'inline'
    ) {
        // If a MIME type is not specified, try to work it out from the name
        if ($type == '' and !empty($name)) {
            $type = self::filenameToType($name);
        }

        // Append to $attachment array
        $this-&gt;attachment[] = array(
            0 =&gt; $string,
            1 =&gt; $name,
            2 =&gt; $name,
            3 =&gt; $encoding,
            4 =&gt; $type,
            5 =&gt; true, // isStringAttachment
            6 =&gt; $disposition,
            7 =&gt; $cid
        );
        return true;
    }

    /**
     * Check if an inline attachment is present.
     * @access public
     * @return boolean
     */
    public function inlineImageExists()
    {
        foreach ($this-&gt;attachment as $attachment) {
            if ($attachment[6] == 'inline') {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if an attachment (non-inline) is present.
     * @return boolean
     */
    public function attachmentExists()
    {
        foreach ($this-&gt;attachment as $attachment) {
            if ($attachment[6] == 'attachment') {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if this message has an alternative body set.
     * @return boolean
     */
    public function alternativeExists()
    {
        return !empty($this-&gt;AltBody);
    }

    /**
     * Clear queued addresses of given kind.
     * @access protected
     * @param string $kind 'to', 'cc', or 'bcc'
     * @return void
     */
    public function clearQueuedAddresses($kind)
    {
        $RecipientsQueue = $this-&gt;RecipientsQueue;
        foreach ($RecipientsQueue as $address =&gt; $params) {
            if ($params[0] == $kind) {
                unset($this-&gt;RecipientsQueue[$address]);
            }
        }
    }

    /**
     * Clear all To recipients.
     * @return void
     */
    public function clearAddresses()
    {
        foreach ($this-&gt;to as $to) {
            unset($this-&gt;all_recipients[strtolower($to[0])]);
        }
        $this-&gt;to = array();
        $this-&gt;clearQueuedAddresses('to');
    }

    /**
     * Clear all CC recipients.
     * @return void
     */
    public function clearCCs()
    {
        foreach ($this-&gt;cc as $cc) {
            unset($this-&gt;all_recipients[strtolower($cc[0])]);
        }
        $this-&gt;cc = array();
        $this-&gt;clearQueuedAddresses('cc');
    }

    /**
     * Clear all BCC recipients.
     * @return void
     */
    public function clearBCCs()
    {
        foreach ($this-&gt;bcc as $bcc) {
            unset($this-&gt;all_recipients[strtolower($bcc[0])]);
        }
        $this-&gt;bcc = array();
        $this-&gt;clearQueuedAddresses('bcc');
    }

    /**
     * Clear all ReplyTo recipients.
     * @return void
     */
    public function clearReplyTos()
    {
        $this-&gt;ReplyTo = array();
        $this-&gt;ReplyToQueue = array();
    }

    /**
     * Clear all recipient types.
     * @return void
     */
    public function clearAllRecipients()
    {
        $this-&gt;to = array();
        $this-&gt;cc = array();
        $this-&gt;bcc = array();
        $this-&gt;all_recipients = array();
        $this-&gt;RecipientsQueue = array();
    }

    /**
     * Clear all filesystem, string, and binary attachments.
     * @return void
     */
    public function clearAttachments()
    {
        $this-&gt;attachment = array();
    }

    /**
     * Clear all custom headers.
     * @return void
     */
    public function clearCustomHeaders()
    {
        $this-&gt;CustomHeader = array();
    }

    /**
     * Add an error message to the error container.
     * @access protected
     * @param string $msg
     * @return void
     */
    protected function setError($msg)
    {
        $this-&gt;error_count++;
        if ($this-&gt;Mailer == 'smtp' and !is_null($this-&gt;smtp)) {
            $lasterror = $this-&gt;smtp-&gt;getError();
            if (!empty($lasterror['error'])) {
                $msg .= $this-&gt;lang('smtp_error') . $lasterror['error'];
                if (!empty($lasterror['detail'])) {
                    $msg .= ' Detail: '. $lasterror['detail'];
                }
                if (!empty($lasterror['smtp_code'])) {
                    $msg .= ' SMTP code: ' . $lasterror['smtp_code'];
                }
                if (!empty($lasterror['smtp_code_ex'])) {
                    $msg .= ' Additional SMTP info: ' . $lasterror['smtp_code_ex'];
                }
            }
        }
        $this-&gt;ErrorInfo = $msg;
    }

    /**
     * Return an RFC 822 formatted date.
     * @access public
     * @return string
     * @static
     */
    public static function rfcDate()
    {
        // Set the time zone to whatever the default is to avoid 500 errors
        // Will default to UTC if it's not set properly in php.ini
        date_default_timezone_set(@date_default_timezone_get());
        return date('D, j M Y H:i:s O');
    }

    /**
     * Get the server hostname.
     * Returns 'localhost.localdomain' if unknown.
     * @access protected
     * @return string
     */
    protected function serverHostname()
    {
        $result = 'localhost.localdomain';
        if (!empty($this-&gt;Hostname)) {
            $result = $this-&gt;Hostname;
        } elseif (isset($_SERVER) and array_key_exists('SERVER_NAME', $_SERVER) and !empty($_SERVER['SERVER_NAME'])) {
            $result = $_SERVER['SERVER_NAME'];
        } elseif (function_exists('gethostname') &amp;&amp; gethostname() !== false) {
            $result = gethostname();
        } elseif (php_uname('n') !== false) {
            $result = php_uname('n');
        }
        return $result;
    }

    /**
     * Get an error message in the current language.
     * @access protected
     * @param string $key
     * @return string
     */
    protected function lang($key)
    {
        if (count($this-&gt;language) &lt; 1) {
            $this-&gt;setLanguage('en'); // set the default language
        }

        if (array_key_exists($key, $this-&gt;language)) {
            if ($key == 'smtp_connect_failed') {
                //Include a link to troubleshooting docs on SMTP connection failure
                //this is by far the biggest cause of support questions
                //but it's usually not PHPMailer's fault.
                return $this-&gt;language[$key] . ' https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting';
            }
            return $this-&gt;language[$key];
        } else {
            //Return the key as a fallback
            return $key;
        }
    }

    /**
     * Check if an error occurred.
     * @access public
     * @return boolean True if an error did occur.
     */
    public function isError()
    {
        return ($this-&gt;error_count &gt; 0);
    }

    /**
     * Ensure consistent line endings in a string.
     * Changes every end of line from CRLF, CR or LF to $this-&gt;LE.
     * @access public
     * @param string $str String to fixEOL
     * @return string
     */
    public function fixEOL($str)
    {
        // Normalise to \n
        $nstr = str_replace(array("\r\n", "\r"), "\n", $str);
        // Now convert LE as needed
        if ($this-&gt;LE !== "\n") {
            $nstr = str_replace("\n", $this-&gt;LE, $nstr);
        }
        return $nstr;
    }

    /**
     * Add a custom header.
     * $name value can be overloaded to contain
     * both header name and value (name:value)
     * @access public
     * @param string $name Custom header name
     * @param string $value Header value
     * @return void
     */
    public function addCustomHeader($name, $value = null)
    {
        if ($value === null) {
            // Value passed in as name:value
            $this-&gt;CustomHeader[] = explode(':', $name, 2);
        } else {
            $this-&gt;CustomHeader[] = array($name, $value);
        }
    }

    /**
     * Returns all custom headers.
     * @return array
     */
    public function getCustomHeaders()
    {
        return $this-&gt;CustomHeader;
    }

    /**
     * Create a message from an HTML string.
     * Automatically makes modifications for inline images and backgrounds
     * and creates a plain-text version by converting the HTML.
     * Overwrites any existing values in $this-&gt;Body and $this-&gt;AltBody
     * @access public
     * @param string $message HTML message string
     * @param string $basedir baseline directory for path
     * @param boolean|callable $advanced Whether to use the internal HTML to text converter
     *    or your own custom converter @see PHPMailer::html2text()
     * @return string $message
     */
    public function msgHTML($message, $basedir = '', $advanced = false)
    {
        preg_match_all('/(src|background)=["\'](.*)["\']/Ui', $message, $images);
        if (array_key_exists(2, $images)) {
            foreach ($images[2] as $imgindex =&gt; $url) {
                // Convert data URIs into embedded images
                if (preg_match('#^data:(image[^;,]*)(;base64)?,#', $url, $match)) {
                    $data = substr($url, strpos($url, ','));
                    if ($match[2]) {
                        $data = base64_decode($data);
                    } else {
                        $data = rawurldecode($data);
                    }
                    $cid = md5($url) . '@phpmailer.0'; // RFC2392 S 2
                    if ($this-&gt;addStringEmbeddedImage($data, $cid, 'embed' . $imgindex, 'base64', $match[1])) {
                        $message = str_replace(
                            $images[0][$imgindex],
                            $images[1][$imgindex] . '="cid:' . $cid . '"',
                            $message
                        );
                    }
                } elseif (substr($url, 0, 4) !== 'cid:' &amp;&amp; !preg_match('#^[A-z]+://#', $url)) {
                    // Do not change urls for absolute images (thanks to corvuscorax)
                    // Do not change urls that are already inline images
                    $filename = basename($url);
                    $directory = dirname($url);
                    if ($directory == '.') {
                        $directory = '';
                    }
                    $cid = md5($url) . '@phpmailer.0'; // RFC2392 S 2
                    if (strlen($basedir) &gt; 1 &amp;&amp; substr($basedir, -1) != '/') {
                        $basedir .= '/';
                    }
                    if (strlen($directory) &gt; 1 &amp;&amp; substr($directory, -1) != '/') {
                        $directory .= '/';
                    }
                    if ($this-&gt;addEmbeddedImage(
                        $basedir . $directory . $filename,
                        $cid,
                        $filename,
                        'base64',
                        self::_mime_types((string)self::mb_pathinfo($filename, PATHINFO_EXTENSION))
                    )
                    ) {
                        $message = preg_replace(
                            '/' . $images[1][$imgindex] . '=["\']' . preg_quote($url, '/') . '["\']/Ui',
                            $images[1][$imgindex] . '="cid:' . $cid . '"',
                            $message
                        );
                    }
                }
            }
        }
        $this-&gt;isHTML(true);
        // Convert all message body line breaks to CRLF, makes quoted-printable encoding work much better
        $this-&gt;Body = $this-&gt;normalizeBreaks($message);
        $this-&gt;AltBody = $this-&gt;normalizeBreaks($this-&gt;html2text($message, $advanced));
        if (!$this-&gt;alternativeExists()) {
            $this-&gt;AltBody = 'To view this email message, open it in a program that understands HTML!' .
                self::CRLF . self::CRLF;
        }
        return $this-&gt;Body;
    }

    /**
     * Convert an HTML string into plain text.
     * This is used by msgHTML().
     * Note - older versions of this function used a bundled advanced converter
     * which was been removed for license reasons in #232
     * Example usage:
     * <code>
     * // Use default conversion
     * $plain = $mail-&gt;html2text($html);
     * // Use your own custom converter
     * $plain = $mail-&gt;html2text($html, function($html) {
     *     $converter = new MyHtml2text($html);
     *     return $converter-&gt;get_text();
     * });
     * </code>
     * @param string $html The HTML text to convert
     * @param boolean|callable $advanced Any boolean value to use the internal converter,
     *   or provide your own callable for custom conversion.
     * @return string
     */
    public function html2text($html, $advanced = false)
    {
        if (is_callable($advanced)) {
            return call_user_func($advanced, $html);
        }
        return html_entity_decode(
            trim(strip_tags(preg_replace('/&lt;(head|title|style|script)[^&gt;]*&gt;.*?&lt;\/\\1&gt;/si', '', $html))),
            ENT_QUOTES,
            $this-&gt;CharSet
        );
    }

    /**
     * Get the MIME type for a file extension.
     * @param string $ext File extension
     * @access public
     * @return string MIME type of file.
     * @static
     */
    public static function _mime_types($ext = '')
    {
        $mimes = array(
            'xl'    =&gt; 'application/excel',
            'js'    =&gt; 'application/javascript',
            'hqx'   =&gt; 'application/mac-binhex40',
            'cpt'   =&gt; 'application/mac-compactpro',
            'bin'   =&gt; 'application/macbinary',
            'doc'   =&gt; 'application/msword',
            'word'  =&gt; 'application/msword',
            'xlsx'  =&gt; 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xltx'  =&gt; 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
            'potx'  =&gt; 'application/vnd.openxmlformats-officedocument.presentationml.template',
            'ppsx'  =&gt; 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
            'pptx'  =&gt; 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'sldx'  =&gt; 'application/vnd.openxmlformats-officedocument.presentationml.slide',
            'docx'  =&gt; 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'dotx'  =&gt; 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
            'xlam'  =&gt; 'application/vnd.ms-excel.addin.macroEnabled.12',
            'xlsb'  =&gt; 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
            'class' =&gt; 'application/octet-stream',
            'dll'   =&gt; 'application/octet-stream',
            'dms'   =&gt; 'application/octet-stream',
            'exe'   =&gt; 'application/octet-stream',
            'lha'   =&gt; 'application/octet-stream',
            'lzh'   =&gt; 'application/octet-stream',
            'psd'   =&gt; 'application/octet-stream',
            'sea'   =&gt; 'application/octet-stream',
            'so'    =&gt; 'application/octet-stream',
            'oda'   =&gt; 'application/oda',
            'pdf'   =&gt; 'application/pdf',
            'ai'    =&gt; 'application/postscript',
            'eps'   =&gt; 'application/postscript',
            'ps'    =&gt; 'application/postscript',
            'smi'   =&gt; 'application/smil',
            'smil'  =&gt; 'application/smil',
            'mif'   =&gt; 'application/vnd.mif',
            'xls'   =&gt; 'application/vnd.ms-excel',
            'ppt'   =&gt; 'application/vnd.ms-powerpoint',
            'wbxml' =&gt; 'application/vnd.wap.wbxml',
            'wmlc'  =&gt; 'application/vnd.wap.wmlc',
            'dcr'   =&gt; 'application/x-director',
            'dir'   =&gt; 'application/x-director',
            'dxr'   =&gt; 'application/x-director',
            'dvi'   =&gt; 'application/x-dvi',
            'gtar'  =&gt; 'application/x-gtar',
            'php3'  =&gt; 'application/x-httpd-php',
            'php4'  =&gt; 'application/x-httpd-php',
            'php'   =&gt; 'application/x-httpd-php',
            'phtml' =&gt; 'application/x-httpd-php',
            'phps'  =&gt; 'application/x-httpd-php-source',
            'swf'   =&gt; 'application/x-shockwave-flash',
            'sit'   =&gt; 'application/x-stuffit',
            'tar'   =&gt; 'application/x-tar',
            'tgz'   =&gt; 'application/x-tar',
            'xht'   =&gt; 'application/xhtml+xml',
            'xhtml' =&gt; 'application/xhtml+xml',
            'zip'   =&gt; 'application/zip',
            'mid'   =&gt; 'audio/midi',
            'midi'  =&gt; 'audio/midi',
            'mp2'   =&gt; 'audio/mpeg',
            'mp3'   =&gt; 'audio/mpeg',
            'mpga'  =&gt; 'audio/mpeg',
            'aif'   =&gt; 'audio/x-aiff',
            'aifc'  =&gt; 'audio/x-aiff',
            'aiff'  =&gt; 'audio/x-aiff',
            'ram'   =&gt; 'audio/x-pn-realaudio',
            'rm'    =&gt; 'audio/x-pn-realaudio',
            'rpm'   =&gt; 'audio/x-pn-realaudio-plugin',
            'ra'    =&gt; 'audio/x-realaudio',
            'wav'   =&gt; 'audio/x-wav',
            'bmp'   =&gt; 'image/bmp',
            'gif'   =&gt; 'image/gif',
            'jpeg'  =&gt; 'image/jpeg',
            'jpe'   =&gt; 'image/jpeg',
            'jpg'   =&gt; 'image/jpeg',
            'png'   =&gt; 'image/png',
            'tiff'  =&gt; 'image/tiff',
            'tif'   =&gt; 'image/tiff',
            'eml'   =&gt; 'message/rfc822',
            'css'   =&gt; 'text/css',
            'html'  =&gt; 'text/html',
            'htm'   =&gt; 'text/html',
            'shtml' =&gt; 'text/html',
            'log'   =&gt; 'text/plain',
            'text'  =&gt; 'text/plain',
            'txt'   =&gt; 'text/plain',
            'rtx'   =&gt; 'text/richtext',
            'rtf'   =&gt; 'text/rtf',
            'vcf'   =&gt; 'text/vcard',
            'vcard' =&gt; 'text/vcard',
            'xml'   =&gt; 'text/xml',
            'xsl'   =&gt; 'text/xml',
            'mpeg'  =&gt; 'video/mpeg',
            'mpe'   =&gt; 'video/mpeg',
            'mpg'   =&gt; 'video/mpeg',
            'mov'   =&gt; 'video/quicktime',
            'qt'    =&gt; 'video/quicktime',
            'rv'    =&gt; 'video/vnd.rn-realvideo',
            'avi'   =&gt; 'video/x-msvideo',
            'movie' =&gt; 'video/x-sgi-movie'
        );
        if (array_key_exists(strtolower($ext), $mimes)) {
            return $mimes[strtolower($ext)];
        }
        return 'application/octet-stream';
    }

    /**
     * Map a file name to a MIME type.
     * Defaults to 'application/octet-stream', i.e.. arbitrary binary data.
     * @param string $filename A file name or full path, does not need to exist as a file
     * @return string
     * @static
     */
    public static function filenameToType($filename)
    {
        // In case the path is a URL, strip any query string before getting extension
        $qpos = strpos($filename, '?');
        if (false !== $qpos) {
            $filename = substr($filename, 0, $qpos);
        }
        $pathinfo = self::mb_pathinfo($filename);
        return self::_mime_types($pathinfo['extension']);
    }

    /**
     * Multi-byte-safe pathinfo replacement.
     * Drop-in replacement for pathinfo(), but multibyte-safe, cross-platform-safe, old-version-safe.
     * Works similarly to the one in PHP &gt;= 5.2.0
     * @link http://www.php.net/manual/en/function.pathinfo.php#107461
     * @param string $path A filename or path, does not need to exist as a file
     * @param integer|string $options Either a PATHINFO_* constant,
     *      or a string name to return only the specified piece, allows 'filename' to work on PHP &lt; 5.2
     * @return string|array
     * @static
     */
    public static function mb_pathinfo($path, $options = null)
    {
        $ret = array('dirname' =&gt; '', 'basename' =&gt; '', 'extension' =&gt; '', 'filename' =&gt; '');
        $pathinfo = array();
        if (preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $path, $pathinfo)) {
            if (array_key_exists(1, $pathinfo)) {
                $ret['dirname'] = $pathinfo[1];
            }
            if (array_key_exists(2, $pathinfo)) {
                $ret['basename'] = $pathinfo[2];
            }
            if (array_key_exists(5, $pathinfo)) {
                $ret['extension'] = $pathinfo[5];
            }
            if (array_key_exists(3, $pathinfo)) {
                $ret['filename'] = $pathinfo[3];
            }
        }
        switch ($options) {
            case PATHINFO_DIRNAME:
            case 'dirname':
                return $ret['dirname'];
            case PATHINFO_BASENAME:
            case 'basename':
                return $ret['basename'];
            case PATHINFO_EXTENSION:
            case 'extension':
                return $ret['extension'];
            case PATHINFO_FILENAME:
            case 'filename':
                return $ret['filename'];
            default:
                return $ret;
        }
    }

    /**
     * Set or reset instance properties.
     * You should avoid this function - it's more verbose, less efficient, more error-prone and
     * harder to debug than setting properties directly.
     * Usage Example:
     * `$mail-&gt;set('SMTPSecure', 'tls');`
     *   is the same as:
     * `$mail-&gt;SMTPSecure = 'tls';`
     * @access public
     * @param string $name The property name to set
     * @param mixed $value The value to set the property to
     * @return boolean
     * @TODO Should this not be using the __set() magic function?
     */
    public function set($name, $value = '')
    {
        if (property_exists($this, $name)) {
            $this-&gt;$name = $value;
            return true;
        } else {
            $this-&gt;setError($this-&gt;lang('variable_set') . $name);
            return false;
        }
    }

    /**
     * Strip newlines to prevent header injection.
     * @access public
     * @param string $str
     * @return string
     */
    public function secureHeader($str)
    {
        return trim(str_replace(array("\r", "\n"), '', $str));
    }

    /**
     * Normalize line breaks in a string.
     * Converts UNIX LF, Mac CR and Windows CRLF line breaks into a single line break format.
     * Defaults to CRLF (for message bodies) and preserves consecutive breaks.
     * @param string $text
     * @param string $breaktype What kind of line break to use, defaults to CRLF
     * @return string
     * @access public
     * @static
     */
    public static function normalizeBreaks($text, $breaktype = "\r\n")
    {
        return preg_replace('/(\r\n|\r|\n)/ms', $breaktype, $text);
    }

    /**
     * Set the public and private key files and password for S/MIME signing.
     * @access public
     * @param string $cert_filename
     * @param string $key_filename
     * @param string $key_pass Password for private key
     * @param string $extracerts_filename Optional path to chain certificate
     */
    public function sign($cert_filename, $key_filename, $key_pass, $extracerts_filename = '')
    {
        $this-&gt;sign_cert_file = $cert_filename;
        $this-&gt;sign_key_file = $key_filename;
        $this-&gt;sign_key_pass = $key_pass;
        $this-&gt;sign_extracerts_file = $extracerts_filename;
    }

    /**
     * Quoted-Printable-encode a DKIM header.
     * @access public
     * @param string $txt
     * @return string
     */
    public function DKIM_QP($txt)
    {
        $line = '';
        for ($i = 0; $i &lt; strlen($txt); $i++) {
            $ord = ord($txt[$i]);
            if (((0x21 &lt;= $ord) &amp;&amp; ($ord &lt;= 0x3A)) || $ord == 0x3C || ((0x3E &lt;= $ord) &amp;&amp; ($ord &lt;= 0x7E))) {
                $line .= $txt[$i];
            } else {
                $line .= '=' . sprintf('%02X', $ord);
            }
        }
        return $line;
    }

    /**
     * Generate a DKIM signature.
     * @access public
     * @param string $signHeader
     * @throws phpmailerException
     * @return string
     */
    public function DKIM_Sign($signHeader)
    {
        if (!defined('PKCS7_TEXT')) {
            if ($this-&gt;exceptions) {
                throw new phpmailerException($this-&gt;lang('extension_missing') . 'openssl');
            }
            return '';
        }
        $privKeyStr = file_get_contents($this-&gt;DKIM_private);
        if ($this-&gt;DKIM_passphrase != '') {
            $privKey = openssl_pkey_get_private($privKeyStr, $this-&gt;DKIM_passphrase);
        } else {
            $privKey = $privKeyStr;
        }
        if (openssl_sign($signHeader, $signature, $privKey)) {
            return base64_encode($signature);
        }
        return '';
    }

    /**
     * Generate a DKIM canonicalization header.
     * @access public
     * @param string $signHeader Header
     * @return string
     */
    public function DKIM_HeaderC($signHeader)
    {
        $signHeader = preg_replace('/\r\n\s+/', ' ', $signHeader);
        $lines = explode("\r\n", $signHeader);
        foreach ($lines as $key =&gt; $line) {
            list($heading, $value) = explode(':', $line, 2);
            $heading = strtolower($heading);
            $value = preg_replace('/\s+/', ' ', $value); // Compress useless spaces
            $lines[$key] = $heading . ':' . trim($value); // Don't forget to remove WSP around the value
        }
        $signHeader = implode("\r\n", $lines);
        return $signHeader;
    }

    /**
     * Generate a DKIM canonicalization body.
     * @access public
     * @param string $body Message Body
     * @return string
     */
    public function DKIM_BodyC($body)
    {
        if ($body == '') {
            return "\r\n";
        }
        // stabilize line endings
        $body = str_replace("\r\n", "\n", $body);
        $body = str_replace("\n", "\r\n", $body);
        // END stabilize line endings
        while (substr($body, strlen($body) - 4, 4) == "\r\n\r\n") {
            $body = substr($body, 0, strlen($body) - 2);
        }
        return $body;
    }

    /**
     * Create the DKIM header and body in a new message header.
     * @access public
     * @param string $headers_line Header lines
     * @param string $subject Subject
     * @param string $body Body
     * @return string
     */
    public function DKIM_Add($headers_line, $subject, $body)
    {
        $DKIMsignatureType = 'rsa-sha1'; // Signature &amp; hash algorithms
        $DKIMcanonicalization = 'relaxed/simple'; // Canonicalization of header/body
        $DKIMquery = 'dns/txt'; // Query method
        $DKIMtime = time(); // Signature Timestamp = seconds since 00:00:00 - Jan 1, 1970 (UTC time zone)
        $subject_header = "Subject: $subject";
        $headers = explode($this-&gt;LE, $headers_line);
        $from_header = '';
        $to_header = '';
        $current = '';
        foreach ($headers as $header) {
            if (strpos($header, 'From:') === 0) {
                $from_header = $header;
                $current = 'from_header';
            } elseif (strpos($header, 'To:') === 0) {
                $to_header = $header;
                $current = 'to_header';
            } else {
                if (!empty($$current) &amp;&amp; strpos($header, ' =?') === 0) {
                    $$current .= $header;
                } else {
                    $current = '';
                }
            }
        }
        $from = str_replace('|', '=7C', $this-&gt;DKIM_QP($from_header));
        $to = str_replace('|', '=7C', $this-&gt;DKIM_QP($to_header));
        $subject = str_replace(
            '|',
            '=7C',
            $this-&gt;DKIM_QP($subject_header)
        ); // Copied header fields (dkim-quoted-printable)
        $body = $this-&gt;DKIM_BodyC($body);
        $DKIMlen = strlen($body); // Length of body
        $DKIMb64 = base64_encode(pack('H*', sha1($body))); // Base64 of packed binary SHA-1 hash of body
        if ('' == $this-&gt;DKIM_identity) {
            $ident = '';
        } else {
            $ident = ' i=' . $this-&gt;DKIM_identity . ';';
        }
        $dkimhdrs = 'DKIM-Signature: v=1; a=' .
            $DKIMsignatureType . '; q=' .
            $DKIMquery . '; l=' .
            $DKIMlen . '; s=' .
            $this-&gt;DKIM_selector .
            ";\r\n" .
            "\tt=" . $DKIMtime . '; c=' . $DKIMcanonicalization . ";\r\n" .
            "\th=From:To:Subject;\r\n" .
            "\td=" . $this-&gt;DKIM_domain . ';' . $ident . "\r\n" .
            "\tz=$from\r\n" .
            "\t|$to\r\n" .
            "\t|$subject;\r\n" .
            "\tbh=" . $DKIMb64 . ";\r\n" .
            "\tb=";
        $toSign = $this-&gt;DKIM_HeaderC(
            $from_header . "\r\n" .
            $to_header . "\r\n" .
            $subject_header . "\r\n" .
            $dkimhdrs
        );
        $signed = $this-&gt;DKIM_Sign($toSign);
        return $dkimhdrs . $signed . "\r\n";
    }

    /**
     * Detect if a string contains a line longer than the maximum line length allowed.
     * @param string $str
     * @return boolean
     * @static
     */
    public static function hasLineLongerThanMax($str)
    {
        //+2 to include CRLF line break for a 1000 total
        return (boolean)preg_match('/^(.{'.(self::MAX_LINE_LENGTH + 2).',})/m', $str);
    }

    /**
     * Allows for public read access to 'to' property.
     * @note: Before the send() call, queued addresses (i.e. with IDN) are not yet included.
     * @access public
     * @return array
     */
    public function getToAddresses()
    {
        return $this-&gt;to;
    }

    /**
     * Allows for public read access to 'cc' property.
     * @note: Before the send() call, queued addresses (i.e. with IDN) are not yet included.
     * @access public
     * @return array
     */
    public function getCcAddresses()
    {
        return $this-&gt;cc;
    }

    /**
     * Allows for public read access to 'bcc' property.
     * @note: Before the send() call, queued addresses (i.e. with IDN) are not yet included.
     * @access public
     * @return array
     */
    public function getBccAddresses()
    {
        return $this-&gt;bcc;
    }

    /**
     * Allows for public read access to 'ReplyTo' property.
     * @note: Before the send() call, queued addresses (i.e. with IDN) are not yet included.
     * @access public
     * @return array
     */
    public function getReplyToAddresses()
    {
        return $this-&gt;ReplyTo;
    }

    /**
     * Allows for public read access to 'all_recipients' property.
     * @note: Before the send() call, queued addresses (i.e. with IDN) are not yet included.
     * @access public
     * @return array
     */
    public function getAllRecipientAddresses()
    {
        return $this-&gt;all_recipients;
    }

    /**
     * Perform a callback.
     * @param boolean $isSent
     * @param array $to
     * @param array $cc
     * @param array $bcc
     * @param string $subject
     * @param string $body
     * @param string $from
     */
    protected function doCallback($isSent, $to, $cc, $bcc, $subject, $body, $from)
    {
        if (!empty($this-&gt;action_function) &amp;&amp; is_callable($this-&gt;action_function)) {
            $params = array($isSent, $to, $cc, $bcc, $subject, $body, $from);
            call_user_func_array($this-&gt;action_function, $params);
        }
    }
}

/**
 * PHPMailer exception handler
 * @package PHPMailer
 */
class phpmailerException extends Exception
{
    /**
     * Prettify error message output
     * @return string
     */
    public function errorMessage()
    {
        $errorMsg = '<strong>' . $this-&gt;getMessage() . "</strong><br>\n";
        return $errorMsg;
    }
}
</address></codeworxtech@users.sourceforge.net></jimjag@gmail.com></phpmailer@synchromedia.co.uk></codeworxtech@users.sourceforge.net></jimjag@gmail.com></body></html>