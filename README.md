# Phower Html

Some helper classes to generate HTML tags from PHP.

## Instalation

This package uses [Composer](https://getcomposer.org/) tool for auto-loading and dependency management.
From your project root folder just run:

    composer require phower/html

## Dependencies

This package depends on features from the following packages:

*   [phower/escaper](https://github.com/phower/escaper) - to have the generated HTML properly escaped against potential XSS attacks.

## Usage

By definition any HTML tag must have a name and may also have attributes and a value. 
Tag value can be text, another tag or a mix of both.

To generate a new HTML tag with an anchor link to the Phower website using Phower\Html just do:

    <?php
    use Phower\Html\Tag;
    $a = new Tag('a', 'Phower', ['href' => 'http://phower.com']);
    echo $a;

The output of the code above is a properly escaped HTML tag:

    <a href="http&#x3A;&#x2F;&#x2F;phower.com">Phower</a>

An extra helper method can also achieve the same result:

    <?php
    use Phower\Html\Tag;
    echo Tag::a('Phower', 'http://phower.com');