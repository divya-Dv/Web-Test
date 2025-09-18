<!--?php
namespace FormGuide\PHPFormValidator;

class FieldValidatorCollection
{
    public $fields;
    private $validator;


    public function __construct()
    {
        $this---><html><head></head><body>fields = array();
        $this-&gt;validator_map = include('ValidatorMap.php');     
    }
    
    public function __call($function, $arguments)
    {
        if(isset($this-&gt;validator_map[$function]))
        {
            foreach($this-&gt;fields as $field)
            {
                $field-&gt;initValidator($function, $arguments);   
            }
            return $this;
        }
        else
        {
            trigger_error('Call to undefined method '.__CLASS__.'::'.$name.'()', E_USER_ERROR);             
        }
    }

    

}</body></html>