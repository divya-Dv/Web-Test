<!--?php

namespace FormGuide\PHPFormValidator;

class FormValidator
{
    private $fields;

    public function __construct()
    {
        $this---><html><head></head><body>fields = array();
    }

    public static function create()
    {
        return new FormValidator();
    }
    
    public function field($field_name)
    {
        if(isset($this-&gt;fields[$field_name]))
        {
            return $this-&gt;fields[$field_name];
        }

        $field = new FieldValidator($field_name);

        $this-&gt;fields[$field_name] = $field;

        return $field; 
    }

    public function fields($arr_fields)
    {
        $coll = new FieldValidatorCollection();

        foreach($arr_fields as $field)
        {
            $coll-&gt;fields[] = $this-&gt;field($field);
        }
        return $coll;
    }

    public function test($post)
    {
        foreach($this-&gt;fields as $field_name =&gt; $rule)
        {
            $rule-&gt;test($post); 
        }
        return $this-&gt;hasErrors()?false:true;
    }

    public function hasErrors()
    {
        foreach($this-&gt;fields as $rule)
        {
            if($rule-&gt;hasErrors())
            {
                return true;
            }
        }
        return false;
    }

    public function getErrors($associative = false)
    {
        $errors = array();
        foreach($this-&gt;fields as $field_name =&gt; $field)
        {
            $error = $field-&gt;getError();
            if(!empty($error))
            {
                $errors[$field_name] = $error;
            }
        }   

        if(false == $associative)
        {
            return array_values($errors);   
        }
        
        return $errors; 
    }

}</body></html>