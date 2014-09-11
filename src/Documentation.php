<?php
namespace Aoloe;

/**
 * This is a very simple implemantation of a documentation tool for PHP classes.
 *
 * TODO:
 * - add links and anchors in the rendered version
 * - description and todo can be markdown (class setting; hook for a filter?)
 * - find out how to show default values for parameters that are arrays
 * - add options:
 *   - ignore the non public properties
 *   - ignore the non public methods
 *   - do not to render the todos
 * - create links to the github code for each method
 * - create the whole class documentation as a markdown string (through a hook?
 *   add spans with direct formatting for a nice inclusion in github's README?)
 * - maybe parse the file's (methods?) source for // TODO:...
 *   (http://stackoverflow.com/questions/7905841/get-source-code-of-user-defined-class-and-functions)
 * - check https://github.com/peej/phpdoctor
 * - find a way to initialize and document "structure"
 */
class Documentation {
    private $reflector = null;
    private $structure = array (
        'class' => array (
            'name' => '',
            'description' => '',
            'namespace' => '',
            'filename' => '',
        ),
        'constructor' => array (
            /*
            'description' => '',
            'parameter' => array (
                'name' => '',
                'default' => '',
                'type' => '',
            ),
            */
        ),
        'method' => array (
            /*
            array(
                'name' => '',
                'description' => '',
                'static' => false,
                'public' => true,
                'parameter' => array (
                    'name' => '',
                    'default' => '',
                    'type' => '',
                    'description' => '',
                ),
                'return' => array(
                    'type' => 'void',
                    'description' => '',
                ),
                'deprecate' => 'false',
            ),
            */
        ),
        'constant' => array (
            /*
            array (
                'name' => '',
                'value' => '',
            ),
            */
        ),
        'property' => array (
            /*
            array (
                'name' => '',
                'description' => '',
                'static' => false,
            ),
            */
        ),
        'todo' => array (
            /*
            array (
                'text' => '',
                'method' => '',
            ),
            */
        ),
    );

    public function __construct($class_name) {
        if (class_exists($class_name)) {
            $this->reflector = new \ReflectionClass($class_name);
        }
    }
    private function get_description_parsed($description) {
        $result = array (
            'description' => '',
            'parameter' => array(),
            'return' => array(
                'type' => 'void',
                'description' => '',
            ),
            'todo' => array(),
        );
        $section = 'description';
        foreach (explode("\n", $description) as $item) {
            // echo("<pre>item:\n".print_r($item, 1)."</pre>\n");
            $matches = array();
            preg_match('/^(@return|@param|@var|@todo|TODO:)(.*)/', $item, $matches);
            // echo("<pre>matches:\n".print_r($matches, 1)."</pre>\n");
            if (!empty($matches[1])) {
                switch ($matches[1]) {
                    case ('@return') :
                        $section = 'return';
                    break;
                    case ('@param') :
                        $section = 'parameter';
                    break;
                    case ('@var') :
                        $section = 'variable';
                    break;
                    case ('@todo') :
                    case ('TODO:') :
                        $section = 'todo';
                        $matches[2] = empty($matches[2]) ? '' : '- '.$matches[2];
                    break;
                }
                $item = empty($matches[2]) ? null : $matches[2];
            }
            switch ($section) {
                case ('description') :
                    $result['description'] .= (!empty($result['description']) && isset($item) ? "\n" : '').$item;
                break;
                case ('return') :
                    $result['return'] = array (
                        'type' => strtok(trim($matches[2]), ' '),
                        'description' => strtok(''),
                    );
                break;
                case ('parameter') :
                    $type = strtok(trim($matches[2]), ' ');
                    $name = ltrim(strtok(' '), '$');
                    // echo("<pre>name:\n".print_r($name, 1)."</pre>\n");
                    $result['parameter'][$name] = array (
                        'type' => $type,
                        'name' => $name,
                        'description' => strtok(''),
                    );
                    // echo("<pre>result[parameter]:\n".print_r($result['parameter'], 1)."</pre>\n");
                break;
                case ('variable') :
                    $result['parameter'] = array (
                        'type' => strtok(trim($matches[2]), ' '),
                        'description' => strtok(''),
                    );
                break;
                case ('todo') :
                    $section = 'todo';
                    if (isset($item)) {
                        if (empty($result['todo']) || substr(trim($item), 0, 1) == '-') {
                            $result['todo'][] = $item;
                        } else {
                            $result['todo'][count($result['todo']) - 1] .= "\n".$item;
                        }
                    }
                break;
            }
        }
        // echo("<pre>result:\n".print_r($result, 1)."</pre>\n");
        return $result;
    }
    private function parse_class() {
        $description = $this->get_comment_cleaned_up($this->reflector->getDocComment());
        $description = $this->get_description_parsed($description);
        $this->structure['class'] = array (
            'name' => $this->reflector->getShortName(),
            'description' => $description['description'],
            'namespace' => $this->reflector->getNamespaceName(),
            'filename' => $this->reflector->getFileName(),
        );
        foreach ($description['todo'] as $item) {
            $this->structure['todo'][] = array (
                'text' => $item,
                'method' => null,
            );
        }
    }

    private function parse_method() {
        foreach ($this->reflector->getMethods() as $reflector) {
            $description = $this->get_comment_cleaned_up($reflector->getDocComment());
            $description = $this->get_description_parsed($description);

            $parameter = array();
            foreach ($reflector->getParameters() as $item) {
                $parameter[$item->getName()] = array (
                    'name' => $item->getName(),
                    'default' => $item->isOptional() ? $item->getDefaultValue() : null,
                    'optional' => $item->isOptional(),
                    'byReference' => $item->isPassedByReference(),
                );
                // echo("<pre>description[parameter']:\n".print_r($description['parameter'], 1)."</pre>\n");
                $parameter[$item->getName()] += 
                    array_key_exists($item->getName(), $description['parameter']) ?
                    $description['parameter'][$item->getName()] :
                    array ('type' => '');
            }

            if ($reflector->isConstructor()) {
                $this->structure['constructor'] = array (
                    'description' => $description['description'],
                    'parameter' => $parameter,
                );
            } else {
                $this->structure['method'][$reflector->getName()] = array (
                    'name' => $reflector->getName(),
                    'description' => $description['description'],
                    'static' => $reflector->isStatic(),
                    'public' => $reflector->isPublic(),
                    'parameter' => $parameter,
                    'return' => empty($description['return']) ? 'void' : $description['return'],
                    'deprecate' => 'false',
                );
            }
        }
        foreach ($description['todo'] as $item) {
            $this->structure['todo'][] = array (
                'text' => $item,
                'method' => $reflector->getName(),
            );
        }
    }

    private function parse_constant() {
        foreach ($this->reflector->getConstants() as $key => $value) {
            $this->structure['constant'][$key] = array (
                'name' => $key,
                'value' => $value,
                'type' => gettype($value),
            );
        }
    }

    private function parse_property() {
        foreach ($this->reflector->getProperties(\ReflectionProperty::IS_PUBLIC) as $reflector) {
            $description = $this->get_comment_cleaned_up($reflector->getDocComment());
            $description = $this->get_description_parsed($description);
            echo("<pre>description:\n".print_r($description, 1)."</pre>\n");

            $this->structure['property'][$reflector->getName()] = array (
                'name' => $reflector->getName(),
                'description' => empty($description['parameter']) ? '' : $description['parameter']['description'],
                'type' => empty($description['parameter']) ? '' : $description['parameter']['type'],
                'static' => $reflector->isStatic(),
            );
        }
    }

    public function parse() {
        $this->parse_class();
        $this->parse_method();
        $this->parse_constant();
        $this->parse_property();
        // echo("<pre>structure:\n".print_r($this->structure, 1)."</pre>\n");
    }

    public function get_json() {
        return json_encode($this->structure);
    }

    public function get_todo_json() {
        return json_encode($this->structure['todo']);
    }

    public function render() {
        $result = "";
        $result .= "<h1>".$this->structure['class']['name']."</h1>\n";
        $result .= "<p>".$this->structure['class']['description']."</p>\n";
        $result .= "<p>Namespace: ".$this->structure['class']['namespace']."</p>\n";
        $result .= "<p>Filename: ".$this->structure['class']['filename']."</p>\n";
        if (!empty($this->structure['constant'])) {
            $result .= "<h2>Constants</h2>\n";
        }
        foreach ($this->structure['constant'] as $item) {
            $result .= "<p>".implode(" ", array (
                '<span class="modifier">const</span>',
                '<span class="type">'.$item['type'].'</span>',
                '<span class="name">'.$item['name'].'</span>',
                '=',
                '<span class="name">'.$item['value'].'</span>',
                )
            )."</p>\n";

        }

        if (!empty($this->structure['property'])) {
            $result .= "<h2>Properties</h2>\n";
        }
        foreach ($this->structure['property'] as $item) {
            $result .= '<p class="signature">'.implode(" ", array_filter(array (
                '<span class="modifier">public</span>',
                '<span class="type">'.$item['type'].'</span>',
                '<span class="name">'.$item['name'].'</span>',
                ))
            )."</p>\n";
            if ($item['description'] != '') {
                $result .= '<p class="description"><span class="description">'.$item['description']."</span></p>\n";
            }

        }

        if (!empty($this->structure['method'])) {
            $result .= "<h2>Methods</h2>\n";
        }

        // show the public methods first
        usort($this->structure['method'], function($a, $b) {return $b['public'];});

        foreach ($this->structure['method'] as $item) {
            // echo("<pre>item:\n".print_r($item, 1)."</pre>\n");
            $result .= '<p class="signature">'.
                implode(" ", array_filter(array(
                    '<span class="modifier">'.implode(' ', array_filter(array(
                        $item['public'] ? 'public' : 'private',
                        $item['static'] ? 'static' : ''
                    ))).'</span>',
                    '<span class="type">'.$item['return']['type'].'</span>',
                    '<span class="name">'.$item['name'].'</span>',
                ))).
                '('.implode(", ", array_filter(
                    array_map(function($item) { return
                            ($item['optional'] ? '[' : '').
                            implode(" ", array_filter(array (
                            '<span class="type">'.$item['type'].'</span>',
                            $item['byReference'] ? '&amp;' : ''.
                            '<span class="name">'.$item['name'].'</span>',
                            ($item['optional'] ? '= '.$this->value_to_string($item['default']) : '')
                            )))
                            .($item['optional'] ? ']' : '')
                    ;}, $item['parameter'])
                )).")</p>\n";
            if ($item['description'] != '') {
                $result .= '<p class="description"><span class="description">'.$item['description']."</span></p>\n";
            }
        }

        if (!empty($this->structure['todo'])) {
            $result .= "<h2>Todo</h2>\n";
            $result .= "<ul>\n";
            foreach ($this->structure['todo'] as $item) {
                $result .= "<li>".implode(" ", array (
                    ltrim($item['text'], '- '),
                    !empty($item['method']) ? '['.$item['method'].'()]' : ''
                ))."</li>\n";
            }
            $result .= "</ul>\n";
        }
        echo $result;
    }

    private function value_to_string($value) {
        $result = $value;
        if (is_null($value)) {
            $result = '<null>';
        } elseif (is_bool($value)) {
            $result = $value ? '<true>' : '<false>';
        } elseif (is_string($value)) {
            $result = '"'.$value.'"';
        } elseif (is_array($value)) {
            $result = '<array>';
        }
        return htmlentities($result);
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
