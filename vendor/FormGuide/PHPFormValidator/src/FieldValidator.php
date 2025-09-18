<!--?php
namespace FormGuide\PHPFormValidator;

class FieldValidator
{   
    private $field_name;
    private $validations;

    private $validator;

    public function __construct($field_name)
    {
        $this---><html><head></head><body>field_name = $field_name;
        $this-&gt;validations = array();
        
        $this-&gt;validator = Validators::create($field_name);

        $this-&gt;validator_map = include('ValidatorMap.php');
    }

    public function __call($function, $arguments)
    {
        if(isset($this-&gt;validator_map[$function]))
        {
            return $this-&gt;initValidator($function, $arguments);
        }
        else
        {
            trigger_error('Call to undefined method '.__CLASS__.'::'.$function.'()', E_USER_ERROR);         
        }
    
    }

    public function initValidator($function, $arguments)
    {
        $validator_type = $this-&gt;validator_map[$function];
        $message = null;
        $constraint = null;
        foreach($arguments as $arg)
        {
            if(is_array($arg))
            {
                if(isset($arg['message']))
                {
                    $message = $arg['message'];
                }
            }
            elseif(empty($constraint))
            {
                $constraint = $arg;
            }
        }

        $this-&gt;validations[$validator_type] = 
                array('value'=&gt;$constraint,'message'=&gt;$message  );

        return $this;       
    }
    

    public function test($post)
    {
        foreach($this-&gt;validations as $rule =&gt; $details)
        {
            $this-&gt;validator-&gt;$rule($post,$details);
        }
    }

    public function hasErrors()
    {
        return $this-&gt;validator-&gt;hasErrors();   
    }

    public function getError()
    {
        return $this-&gt;validator-&gt;getError();
    }
}
</body></html>