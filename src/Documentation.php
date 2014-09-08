<?php
namespace Aoloe;

/**
 * TODO:
 * - description can be markdown (class setting)
 * - recognize the @param in the method's doc
 * - find out how to show default values for parameters that are arrays
 * - recognize the @return (or catch the $result, where $result can be defined as the default return $value)
 * - separate the static methods
 * - eventually show the public fields
 * - create links to the github code for each method
 * - show the class constants
 * - create the whole class documentation as a markdown string
 * - collect the TODOs (both oneliners and bullets), remove them from the descriptions and list them
 *   all together (with a reference to the function where they were)
 * - option for output in json or markdown?
 * - maybe parse the file's source for // TODO:...
 *   (http://stackoverflow.com/questions/7905841/get-source-code-of-user-defined-class-and-functions)
 */
class Documentation {
    private $reflector = null;

    public function __construct($class_name) {
        if (class_exists($class_name)) {
            $this->reflector = new \ReflectionClass($class_name);
        }
    }
    public function render() {
        echo('<pre>'.$this->get_comment_cleaned_up($this->reflector->getDocComment()).'</pre>'."\n");
        $constructor = array();
        $public = array();
        $private = array();
        foreach ($this->reflector->getMethods() as $item) {
            $method = array (
                'name' => $item->getName(),
                'description' => $this->get_comment_cleaned_up($item->getDocComment()),
                'parameters' => $item->getParameters(),
            );
            // echo('<pre>'.print_r($method, 1).'</pre>');
            if ($item->isConstructor()) {
                $constructor = $method;
            } elseif ($item->isPublic()) {
                $public[] = $method;
            } else {
                $private[] = $method;
            }
        }
        if (!empty($constructor)) {
            $this->render_method($constructor);
        }
        foreach ($public as $item) {
            $this->render_method($item);
        }
        foreach ($private as $item) {
            $this->render_method($item);
        }
    }

    /**
     * TODO: transform this into a get_method_string()
     */
     
    private function render_method($method) {
        $parameters = array();
        foreach ($method['parameters'] as $item) {
            $parameters[] = $item->getName().($item->isOptional() ? ' = '.$this->value_to_string($item->getDefaultValue()) : '');
        }
        echo('<pre>'.$method['name'].'('.implode(', ', $parameters).')'."\n".$method['description'].'</pre>'."\n");
    }

    private function value_to_string($value) {
        $result = $value;
        if (is_null($value)) {
            $result = '<null>';
        } elseif (is_bool($value)) {
            $result = $value ? '<true>' : '<false>';
        } elseif (is_string($value)) {
            $result = '"'.$value.'"';
        }
        return $result;
    }

    private function get_comment_cleaned_up($comment) {
        $comment = trim($comment);
        if (substr($comment, 0, 3) == '/**') {
            $comment = substr($comment, 3);
        }
        if (substr($comment, -2) == '*/') {
            $comment = substr($comment, 0, strlen($comment) - 3);
        }
        $comment = trim($comment);
        $comment = implode("\n", array_map(
            function ($item) {
                $result = $item;
                if (substr(trim($item), 0, 1) === '*') {
                    $result = trim(substr(trim($item), 1));
                }
                return $result;
            },
            explode("\n", $comment)
        ));
        return $comment;
    }
}
