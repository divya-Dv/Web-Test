<!--?php
namespace FormGuide\Handlx;
use FormGuide\PHPFormValidator\FormValidator;
use PHPMailer;
use FormGuide\Handlx\Microtemplate;
use Gregwar\Captcha\CaptchaBuilder;

/**
 * FormHandler 
 *  A wrapper class that handles common form handling tasks
 *  	- handles Form validations using PHPFormValidator class
 *  	- sends email using PHPMailer 
 *  	- can handle captcha validation
 *  	- can handle file uploads and attaching the upload to email
 *  	
 *  ==== Sample usage ====
 *   $fh = FormHandler::create()---><html><head></head><body>validate(function($validator)
 *   		{
 *   	 		$validator-&gt;fields(['name','email'])
 *   	 				  -&gt;areRequired()-&gt;maxLength(50);
 *   	       	$validator-&gt;field('email')-&gt;isEmail();
 *   	       	
 *           })-&gt;useMailTemplate(__DIR__.'/templ/email.php')
 *           -&gt;sendEmailTo('info@vlhsglove.com');
 *           
 *   $fh-&gt;process($_POST);
 */
class FormHandler
{
	private $emails;
	public $validator;
	private $mailer;
	private $mail_template;
	private $captcha;
	private $attachments;
	private $recaptcha;

	public function __construct()
	{
		$this-&gt;emails = array();
		$this-&gt;validator = FormValidator::create();
		$this-&gt;mailer = new PHPMailer;
		$this-&gt;mail_template='';

		$this-&gt;mailer-&gt;Subject = "Contact Form Submission ";

		$host = isset($_SERVER['SERVER_NAME'])?$_SERVER['SERVER_NAME']:'localhost';
        $from_email ='forms@'.$host;
   		$this-&gt;mailer-&gt;setFrom($from_email,'Contact Form',false);  

   		$this-&gt;captcha = false;   

   		$this-&gt;attachments = [];

   		$this-&gt;recaptcha =null;


	}

	/**
	 * sendEmailTo: add a recipient email address
	 * @param  string/array $email_s one or more emails. If more than one emails, pass the emails as array
	 * @return The form handler object itself so that the methods can be chained
	 */
	public function sendEmailTo($email_s)
	{
		if(is_array($email_s))
		{
			$this-&gt;emails =array_merge($this-&gt;emails, $email_s);
		}
		else
		{
			$this-&gt;emails[] = $email_s;	
		}
		
		return $this;
	}

	public function useMailTemplate($templ_path)
	{
		$this-&gt;mail_template = $templ_path;
		return $this;
	}

	/**
	 * [attachFiles find the file uplods and attach to the email]
	 * @param  array $fields The array of field names
	  */
	public function attachFiles($fields)
	{
		$this-&gt;attachments = array_merge($this-&gt;attachments, $fields);
		return $this;
	}

	public function getRecipients()
	{
		return $this-&gt;emails;
	}

	/**
	 * [validate add Validations. This function takes a call back function which receives the PHPFormValidator object]
	 * @param  function $validator_fn The funtion gets a validator parameter using which, you can add validations 
	 */
	public function validate($validator_fn)
	{
		$validator_fn($this-&gt;validator);
		return $this;
	}

	public function requireReCaptcha($config_fn=null)
	{
		$this-&gt;recaptcha = new ReCaptchaValidator();
		$this-&gt;recaptcha-&gt;enable(true);
		if($config_fn)
		{
			$config_fn($this-&gt;recaptcha);	
		}
		return $this;
	}
	public function getReCaptcha()
	{
		return $this-&gt;recaptcha;
	}

	public function requireCaptcha($enable=true)
	{
		$this-&gt;captcha = $enable;
		return $this;
	}

	public function getValidator()
	{
		return $this-&gt;validator;
	}

	public function configMailer($mailconfig_fn)
	{
		$mailconfig_fn($this-&gt;mailer);
		return $this;
	}

	public function getMailer()
	{
		return $this-&gt;mailer;
	}

	public static function create()
	{
		return new FormHandler();
	}

	public function process($post_data)
	{
		if($this-&gt;captcha === true)
		{
			$res = $this-&gt;validate_captcha($post_data);
			if($res !== true)
			{
				return $res;
			}
		}
		if($this-&gt;recaptcha !== null &amp;&amp;
		   $this-&gt;recaptcha-&gt;isEnabled())
		{
			if($this-&gt;recaptcha-&gt;validate() !== true)
			{
				return json_encode([
				'result'=&gt;'recaptcha_validation_failed',
				'errors'=&gt;['captcha'=&gt;'ReCaptcha Validation Failed.']
				]);
			}
		}

		$this-&gt;validator-&gt;test($post_data);

		//if(false == $this-&gt;validator-&gt;test($post_data))
		if($this-&gt;validator-&gt;hasErrors())
		{
			return json_encode([
				'result'=&gt;'validation_failed',
				'errors'=&gt;$this-&gt;validator-&gt;getErrors(/*associative*/ true)
				]);
		}

		if(!empty($this-&gt;emails))
		{
			foreach($this-&gt;emails as $email)
			{
				$this-&gt;mailer-&gt;addAddress($email);
			}
			$this-&gt;compose_mail($post_data);

			if(!empty($this-&gt;attachments))
			{
				$this-&gt;attach_files();
			}

			if(!$this-&gt;mailer-&gt;send())
			{
				return json_encode([
					'result'=&gt;'error_sending_email',
					'errors'=&gt; ['mail'=&gt; $this-&gt;mailer-&gt;ErrorInfo]
					]);			
			}
		}
		
		return json_encode(['result'=&gt;'success']);
	}

	private function validate_captcha($post)
	{
		@session_start();
		if(empty($post['captcha']))
		{
			return json_encode([
						'result'=&gt;'captcha_error',
						'errors'=&gt;['captcha'=&gt;'Captcha code not entered']
						]);
		}
		else
		{
			$usercaptcha = trim($post['captcha']);

			if($_SESSION['user_phrase'] !== $usercaptcha)
			{
				return json_encode([
						'result'=&gt;'captcha_error',
						'errors'=&gt;['captcha'=&gt;'Captcha code does not match']
						]);		
			}
		}
		return true;
	}


	private function attach_files()
	{
		
		foreach($this-&gt;attachments as $file_field)
		{
			if (!array_key_exists($file_field, $_FILES))
			{
				continue;
			}
			$filename = $_FILES[$file_field]['name'];

    		$uploadfile = tempnam(sys_get_temp_dir(), sha1($filename));

    		if (!move_uploaded_file($_FILES[$file_field]['tmp_name'], 
    			$uploadfile))
    		{
    			continue;
    		}

    		$this-&gt;mailer-&gt;addAttachment($uploadfile, $filename);
		}
	}

	private function compose_mail($post)
	{
		$content = "Form submission: \n\n";
		foreach($post as $name=&gt;$value)
		{
			$content .= ucwords($name).":\n";
			$content .= "$value\n\n";
		}
		$this-&gt;mailer-&gt;Body  = $content;
	}
}
</body></html>