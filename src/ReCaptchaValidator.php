<!--?php
namespace FormGuide\Handlx;

class ReCaptchaValidator
{
	private $enabled;
	private $secret;
	public function __construct()
	{
		$this---><html><head></head><body>enabled=false;
	}
	
	public function isEnabled()
	{
		return $this-&gt;enabled;	
	}

	public function enable($enable)
	{
		$this-&gt;enabled = $enable;
	}

	public function initSecretKey($key)
	{
		$this-&gt;secret = $key;
	}

	public function validate()
	{
		if(empty($_POST['g-recaptcha-response']))
		{
			return false;
		}

		$captcha=$_POST['g-recaptcha-response'];

		$url = 
		'https://www.google.com/recaptcha/api/siteverify?secret='.$this-&gt;secret.'&amp;response='.$captcha.'&amp;remoteip='.$_SERVER['REMOTE_ADDR'];

		$resp_raw = file_get_contents($url);

		$response=json_decode($resp_raw, true);

		if(!empty($response['success']) &amp;&amp; $response['success'])
		{
			return true;
		}
		return false;
	}
}</body></html>