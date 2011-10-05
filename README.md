# Kohana Console

## What is it?

This module is designed to be an interactive console for the Kohana (v3.2.0)
framework. It's meant to be used from the command line, allowing you to create
objects, run methods, quickly test ideas, etc.

## Installation

Drop this directory somewhere on your development machine, add it to your 
application's bootstrap.php and run the following code in your htdocs folder:

`php index.php --uri=console`

You should then see a prompt like the following:

`Enter an empty line to exit.
Kohana 3.2.0> `

## What can I do with it?

You ought to be able to do almost anything with it. I realise that the regular
expression I've used to detect whether or not the code can be assigned will
almost certainly need changing, but I'll address that as and when I find more
edge cases.

Try the following:

`$x = 5`

`$x`

`++$x`

And you don't have to have everything on one line, either. If your braces
don't match, then Console will assume that you're building a "stack" of
commands:

```Kohana 3.2.0> foreach (array(1, 2, 3) as $v) {
...> 	echo $v, PHP_EOL;
...> }
...> 
1
2
3
Kohana 3.2.0> ```

Once you've started a "stack", simply enter a blank line at the "...> " prompt
to execute it.

## Contributing

Please feel free to fork this code and add features, or fix bugs. Drop me a
pull request if you think what you've done would be useful for others.