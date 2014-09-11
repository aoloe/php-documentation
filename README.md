# Documentation

Generate the documentation from a PHP class.

Parses a class -- must already be loaded in memory -- for all its constants, parameters and methods and documents them in a sane way.

You can get a clean HTML string or a json string.

No dependency. Less than 400 LOC.

## Usage

The simplest documentation file is:

~~~ .php
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
</head>
<body>
<?php
include('ClassToBeDocumented.php');
include('Documentation.php');

$documentation = new Aoloe\Documentation('ClassToBeDocumented');
$documentation->parse();
$documentation->render();
?>
</body>
</html>
~~~

That's simple:
- write the HTML "decoration",
- load the class to be documented (and the `Documentation` framework"),
- pass its name to the `Documentation` constructor,
- parse the documentation,
- render it.

This projects documentation is a _real world_ example, that adds the CSS styling.

## Features

- Uses the PHP built-in reflection classes to get each
  - constant,
  - (public) property, and
  - (public) method.
- The class and the methods can have doc comments ("JavaDoc") with:
  - a freeform introductory text,
  - the type of the (method) parameters (`@var`, `@param`),
  - the return value of methods (`@return`),
  - todo lists (`@todo`, `TODO`).
- Returns
  - a clean HTML text you can echon on a HTML page, or
  - a json list you can further process, or
  - the todo list.
- Does not differntiate between `protected` and `private`.

Basically, it adds a limited JavaDoc parsing to the PHP own reflection classes.

## Parsing

Doc comments can appear before:
- the class definition
- the class variables (parameters)
- the class methods

The following doc comments are recognized:

- `@var type [description]`
- `@parm type name [description]`
- `@return type [description]`
- `@todo description`
- `@todo`
  `- descrition`
- `TODO description`
- `TODO`
  `- descrition`

## Render

The default rendered returns an HTML chunk you can embed in your HTML page.

It uses the following CSS styles:

- `h2`
- `p.signature`
- `p.signature span.modifier`
- `p.signature  span.type`
- `p.signature  span.name`
- `p.description`

For a sample, check this project's documentation.

## Json output

You can get the parsed array as json string.

You can also get the list of the todo items as a separate json string.

## Use cases

- Use it as is for small projects that you want to have documented, but where you don't want to pull in a documentation framework that is several order of magnitude bigger than your own code
- Hack it to get a documentation that perfectly matches the needs for your project.

## Contribute

Contribution are welcome, most of all if they lead to a reduction of the number of lines of code.

There is a todo list at the beginning of the PHP file which contains a list of features planned.

You can also add features request and issues to the GitHub issue tracker. They will be considered, as long as they fit the goals of this project.
