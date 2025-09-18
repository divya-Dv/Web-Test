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
 * PHPMailerOAuthGoogle - Wrapper for League OAuth2 Google provider.
 * @package PHPMailer
 * @author @sherryl4george
 * @author Marcus Bointon (@Synchro) <phpmailer@synchromedia.co.uk>
 * @link https://github.com/thephpleague/oauth2-client
 */
class PHPMailerOAuthGoogle
{
    private $oauthUserEmail = '';
    private $oauthRefreshToken = '';
    private $oauthClientId = '';
    private $oauthClientSecret = '';

    /**
     * @param string $UserEmail
     * @param string $ClientSecret
     * @param string $ClientId
     * @param string $RefreshToken
     */
    public function __construct(
        $UserEmail,
        $ClientSecret,
        $ClientId,
        $RefreshToken
    ) {
        $this-&gt;oauthClientId = $ClientId;
        $this-&gt;oauthClientSecret = $ClientSecret;
        $this-&gt;oauthRefreshToken = $RefreshToken;
        $this-&gt;oauthUserEmail = $UserEmail;
    }

    private function getProvider()
    {
        return new League\OAuth2\Client\Provider\Google([
            'clientId' =&gt; $this-&gt;oauthClientId,
            'clientSecret' =&gt; $this-&gt;oauthClientSecret
        ]);
    }

    private function getGrant()
    {
        return new \League\OAuth2\Client\Grant\RefreshToken();
    }

    private function getToken()
    {
        $provider = $this-&gt;getProvider();
        $grant = $this-&gt;getGrant();
        return $provider-&gt;getAccessToken($grant, ['refresh_token' =&gt; $this-&gt;oauthRefreshToken]);
    }

    public function getOauth64()
    {
        $token = $this-&gt;getToken();
        return base64_encode("user=" . $this-&gt;oauthUserEmail . "\001auth=Bearer " . $token . "\001\001");
    }
}
</phpmailer@synchromedia.co.uk></codeworxtech@users.sourceforge.net></jimjag@gmail.com></body></html>