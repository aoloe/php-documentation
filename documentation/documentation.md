<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Documentation: Documentation</title>
<link href='http://fonts.googleapis.com/css?family=Fira+Sans:300,400,300italic,400italic' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Fira+Mono' rel='stylesheet' type='text/css'>
<style>
    .documentation {
        font-family: "Fira Sans", "Source Sans Pro", Helvetica, Arial, sans-serif;
        font-weight: 400;
    }
    .documentation h1 {
        color: #f80;
    }
    .documentation h2 {
        color: #f80;
        font-size:1.125em;
        font-weight:normal;
    }
    .documentation p.signature {
        padding-top:0px;
        margin-top:13px;
        padding-bottom:0px;
        margin-bottom:0px;
        font: normal 0.875rem/1.5rem "Fira Mono", monospace;
    }
    .documentation p.signature span.modifier {
        color: #333;
    }
    .documentation p.signature  span.type {
        color: #693;
    }
    .documentation p.signature  span.name {
        color: #369;
    }
    .documentation p.description {
        padding-top:6px;
        margin-top:0px;
        padding-bottom:0px;
        margin-bottom:0px;
    }
</style>
</head>
<body>
<div style='font-family: "Fira Sans", "Source Sans Pro", Helvetica, Arial, sans-serif; font-weight: 400;'>
# <span style="color: #f80;">Documentation</span>
<p>This is a very simple implementation of a documentation tool for PHP classes.
</p>
<p>Namespace: Aoloe</p>
<p>Filename: /home/ale/docs/src/php-documentation/src/Documentation.php</p>
<h2>Methods</h2>
<p class="signature"><span class="modifier">public</span> <span class="type">void</span> <span class="name">parse</span>()</p>
<p class="signature"><span class="modifier">public</span> <span class="type">void</span> <span class="name">render</span>()</p>
<p class="signature"><span class="modifier">public</span> <span class="type">void</span> <span class="name">get_todo_json</span>()</p>
<p class="signature"><span class="modifier">public</span> <span class="type">void</span> <span class="name">get_json</span>()</p>
<p class="signature"><span class="modifier">private</span> <span class="type">void</span> <span class="name">value_to_string</span>(<span class="type"></span> <span class="name">value</span>)</p>
<p class="signature"><span class="modifier">private</span> <span class="type">void</span> <span class="name">get_comment_cleaned_up</span>(<span class="type"></span> <span class="name">comment</span>)</p>
<p class="signature"><span class="modifier">private</span> <span class="type">void</span> <span class="name">get_description_parsed</span>(<span class="type"></span> <span class="name">description</span>)</p>
<p class="signature"><span class="modifier">private</span> <span class="type">void</span> <span class="name">parse_method</span>()</p>
<p class="signature"><span class="modifier">private</span> <span class="type">void</span> <span class="name">parse_constant</span>()</p>
<p class="signature"><span class="modifier">private</span> <span class="type">void</span> <span class="name">parse_property</span>()</p>
<p class="signature"><span class="modifier">private</span> <span class="type">void</span> <span class="name">parse_class</span>()</p>
<h2>Todo</h2>
<ul>
<li>add links and anchors in the rendered version </li>
<li>description and todo can be markdown (class setting; hook for a filter?) </li>
<li>find out how to show default values for parameters that are arrays </li>
<li>add options: </li>
<li>ignore the non public properties </li>
<li>ignore the non public methods </li>
<li>do not to render the todos </li>
<li>create links to the github code for each method </li>
<li>create the whole class documentation as a markdown string (through a hook?
add spans with direct formatting for a nice inclusion in github's README?) </li>
<li>maybe parse the file's (methods?) source for // TODO:...
(http://stackoverflow.com/questions/7905841/get-source-code-of-user-defined-class-and-functions) </li>
<li>check https://github.com/peej/phpdoctor </li>
<li>find a way to initialize and document "structure" </li>
</ul>
</div>
</body>
</html>
