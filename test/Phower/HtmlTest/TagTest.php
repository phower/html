<?php

namespace Phower\HtmlTest;

use PHPUnit_Framework_TestCase;
use Phower\Html\Tag;
use Phower\Escaper\Escaper;

class TagTest extends PHPUnit_Framework_TestCase
{

    public function testTagImplementsTagInterface()
    {
        $this->assertInstanceOf('Phower\Html\TagInterface', new Tag('html'));
    }

    public function testConstructNameArgumentMustBeAString()
    {
        $this->setExpectedException('InvalidArgumentException');
        $tag = new Tag(123);
    }

    public function testConstructNameArgumentCantBeAnEmptyString()
    {
        $this->setExpectedException('InvalidArgumentException');
        $tag = new Tag('');
    }

    public function testConstructValueArgumentMustBeNullAStringAnInstanceOfTagInterfaceOrAnArray()
    {
        $this->assertInstanceOf('Phower\Html\TagInterface', new Tag('html', null));
        $this->assertInstanceOf('Phower\Html\TagInterface', new Tag('html', 'just a string'));
        $this->assertInstanceOf('Phower\Html\TagInterface', new Tag('html', new Tag('head')));
        $this->assertInstanceOf('Phower\Html\TagInterface', new Tag('html', [
            new Tag('head'), new Tag('body'), 
        ]));

        $this->setExpectedException('InvalidArgumentException');
        $tag = new Tag('html', 123);
    }

    public function testConstructAcceptsAttributesArgumentAsAnArray()
    {
        $tag = new Tag('html', 'just a string', ['id' => 'page']);
        $this->assertInstanceOf('Phower\Html\TagInterface', $tag);
    }

    public function testConstructAcceptsEscapeArgumentAsABoolean()
    {
        $tag = new Tag('html', 'just a string', ['id' => 'page'], false);
        $this->assertInstanceOf('Phower\Html\TagInterface', $tag);
    }

    public function testConstructAcceptsEscaperArgumentAsAnInstanceOfEscaperInterface()
    {
        $escaper = new Escaper();
        $this->assertInstanceOf('Phower\Escaper\EscaperInterface', $escaper);
        $tag = new Tag('html', 'just a string', ['id' => 'page'], false);
        $this->assertInstanceOf('Phower\Html\TagInterface', $tag);
    }

    public function testGetNameMethodReturnsTagName()
    {
        $tag = new Tag('html', 'just a string', ['id' => 'page'], false);
        $this->assertEquals('html', $tag->getName());
    }

    public function testSetValueMethodChangesTagValue()
    {
        $tag = new Tag('html', 'just a string', ['id' => 'page'], false);
        $this->assertEquals('just a string', $tag->getValue());

        $tag->setValue('a new string');
        $this->assertEquals('a new string', $tag->getValue());
    }

    public function testGetValueMethodReturnsTagValue()
    {
        $tag = new Tag('html', 'just a string', ['id' => 'page'], false);
        $this->assertEquals('just a string', $tag->getValue());

        $tag->setValue('a new string');
        $this->assertEquals('a new string', $tag->getValue());
    }

    public function testSetValueMethodRequiresValueArgumentToBeNullAStringOrAnInstanceOfTagInterface()
    {
        $tag = new Tag('a');

        $value = null;
        $tag->setValue($value);
        $this->assertEquals($value, $tag->getValue());

        $value = 'a string';
        $tag->setValue($value);
        $this->assertEquals($value, $tag->getValue());

        $value = new Tag('span');
        $tag->setValue($value);
        $this->assertEquals($value, $tag->getValue());

        $value = [new Tag('span'), 'just text'];
        $tag->setValue($value);
        $this->assertEquals($value, $tag->getValue());

        $this->setExpectedException('InvalidArgumentException');
        $value = 123;
        $tag->setValue($value);
    }

    public function testHasAttributeMethodChecksThatAnAttributeAlreadyExists()
    {
        $tag = new Tag('a', 'link', ['href' => '/']);
        $this->assertTrue($tag->hasAttribute('href'));

        // edge case
        $tag = new Tag('a', 'link', ['href' => null]);
        $this->assertTrue($tag->hasAttribute('href'));
    }

    public function testSetAttributesMethodChangesAllTagAttributes()
    {
        $tag = new Tag('a');
        $attributes = ['id' => 'my-id'];
        $tag->setAttributes($attributes);
        $this->assertEquals($attributes, $tag->getAttributes());
    }

    public function testGetAttributesMethodReturnsAllAttributes()
    {
        $tag = new Tag('a');
        $attributes = ['id' => 'my-id'];
        $tag->setAttributes($attributes);
        $this->assertEquals($attributes, $tag->getAttributes());
    }

    public function testAddAttributeMethodAddsANewAttribute()
    {
        $tag = new Tag('a');
        $this->assertEquals([], $tag->getAttributes());

        $tag->addAttribute('id', 'my-id');
        $attributes = ['id' => 'my-id'];
        $this->assertEquals($attributes, $tag->getAttributes());
    }

    public function testAddAttributeMethodRefusesToAddAnAlreadyExistingAttribute()
    {
        $tag = new Tag('a');
        $this->assertEquals([], $tag->getAttributes());

        $tag->addAttribute('id', 'my-id');
        $attributes = ['id' => 'my-id'];
        $this->assertEquals($attributes, $tag->getAttributes());

        $this->setExpectedException('InvalidArgumentException');
        $tag->addAttribute('id', 'other-id');
    }

    public function testAddAttributesMethodAddsManyAttributesAtOnce()
    {
        $tag = new Tag('a');
        $this->assertEquals([], $tag->getAttributes());

        $attributes = [
            'id' => 'my-id',
            'class' => 'a-class',
            'style' => 'color: white',
        ];
        $tag->addAttributes($attributes);
        $this->assertEquals($attributes, $tag->getAttributes());
    }

    public function testGetAttributeMethodReturnASingleExistingAttribute()
    {
        $tag = new Tag('a');
        $attributes = [
            'id' => 'my-id',
            'class' => 'a-class',
            'style' => 'color: white',
        ];
        $tag->setAttributes($attributes);
        $this->assertEquals($attributes['class'], $tag->getAttribute('class'));

        $this->setExpectedException('InvalidArgumentException');
        $tag->getAttribute('not-there');
    }

    public function testSetAttributeMethodAddsOrChangesAnAttribute()
    {
        $tag = new Tag('a');
        $attributes = [
            'id' => 'my-id',
            'class' => 'a-class',
            'style' => 'color: white',
        ];
        $tag->setAttribute('class', 'new-class');
        $this->assertEquals('new-class', $tag->getAttribute('class'));

        $tag->setAttribute('order', 1);
        $this->assertEquals(1, $tag->getAttribute('order'));
    }

    public function testSetAttributeMethodRequiresAttributeNameTobeANonEmptyString()
    {
        $tag = new Tag('a');
        $this->setExpectedException('InvalidArgumentException');
        $tag->setAttribute('', 'nothing');
    }

    public function testEscapeMethodChangesAndReturnsEscapeOption()
    {
        $tag = new Tag('a');
        $this->assertTrue($tag->escape());
        $tag->escape(false);
        $this->assertFalse($tag->escape());
    }

    public function testSetEscaperMethodChangesEscaperInstance()
    {
        $tag = new Tag('a');
        $escaper = new Escaper('iso-8859-1');
        $tag->setEscaper($escaper);
        $this->assertEquals($escaper, $tag->getEscaper());
    }

    public function testGetEscaperMethodReturnsEscaperInstance()
    {
        $tag = new Tag('a');
        $escaper = new Escaper();
        $this->assertEquals($escaper, $tag->getEscaper());
    }

    public function testIsXhtmlMethodChangesAndReturnsXhtmlOption()
    {
        $tag = new Tag('a');
        $this->assertFalse($tag->isXhtml());
        $tag->isXhtml(true);
        $this->assertTrue($tag->isXhtml());
    }

    public function testGetClosingBracketMethodReturnsClosingBracketDependingOnXhtmlOption()
    {
        $tag = new Tag('a');
        $this->assertEquals('>', $tag->getClosingBracket());
        $tag->isXhtml(true);
        $this->assertEquals(' />', $tag->getClosingBracket());
    }

    public function testQuoteMethodReturnsSafeQuotedString()
    {
        $tag = new Tag('a', 'Phower', ['href' => 'http://phower.com', 'title' => 'Phower Website']);

        $expected = '"that\'s ok!"';
        $this->assertEquals($expected, $tag->quote('that\'s ok!'));

        $expected = '\'"that\'s ok!"\'';
        $this->assertEquals($expected, $tag->quote('"that\'s ok!"'));
    }

    public function testOpenTagMethodReturnsOpenTagHtmlStringWithAttributes()
    {
        $tag = new Tag('a', 'Phower', ['href' => 'http://phower.com', 'title' => 'Phower Website']);

        $expected = '<a href="http&#x3A;&#x2F;&#x2F;phower.com" title="Phower&#x20;Website">';
        $this->assertEquals($expected, $tag->openTag());

        $tag->setAttribute('class', ['class-a', 'class-b']);
        $expected = '<a href="http&#x3A;&#x2F;&#x2F;phower.com" title="Phower&#x20;Website" class="class-a&#x20;class-b">';
        $this->assertEquals($expected, $tag->openTag());
    }

    public function testCloseTagMethodReturnsCloseTagHtmlString()
    {
        $tag = new Tag('a', 'Phower', ['href' => 'http://phower.com', 'title' => 'Phower Website']);

        $expected = '</a>';
        $this->assertEquals($expected, $tag->closeTag());
    }

    public function testRenderMethodReturnsCompleteTagHtmlString()
    {
        $tag = new Tag('img', null, ['src' => 'picture.jpg']);
        $expected = '<img src="picture.jpg">';
        $this->assertEquals($expected, $tag->render());

        $tag = new Tag('a', 'Phower', ['href' => 'http://phower.com', 'title' => 'Phower Website']);
        $expected = '<a href="http&#x3A;&#x2F;&#x2F;phower.com" title="Phower&#x20;Website">Phower</a>';
        $this->assertEquals($expected, $tag->render());

        $tag = new Tag('a', new Tag('em', 'Phower'), ['href' => 'http://phower.com', 'title' => 'Phower Website']);
        $expected = '<a href="http&#x3A;&#x2F;&#x2F;phower.com" title="Phower&#x20;Website"><em>Phower</em></a>';
        $this->assertEquals($expected, $tag->render());
        
        $tag = new Tag('html', [
            new Tag('head', new Tag('title', 'Phower')),
            new Tag('body', ['Hello World!']),
        ]);
        $expected = '<html><head><title>Phower</title></head>'
                . '<body>Hello World!</body></html>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testToStringMethodOutputsCompleteTagHtmlString()
    {
        $tag = new Tag('img', null, ['src' => 'picture.jpg']);
        $expected = '<img src="picture.jpg">';
        $this->assertEquals($expected, sprintf($tag));

        $tag = new Tag('a', 'Phower', ['href' => 'http://phower.com', 'title' => 'Phower Website']);
        $expected = '<a href="http&#x3A;&#x2F;&#x2F;phower.com" title="Phower&#x20;Website">Phower</a>';
        $this->assertEquals($expected, sprintf($tag));

        $tag = new Tag('a', new Tag('em', 'Phower'), ['href' => 'http://phower.com', 'title' => 'Phower Website']);
        $expected = '<a href="http&#x3A;&#x2F;&#x2F;phower.com" title="Phower&#x20;Website"><em>Phower</em></a>';
        $this->assertEquals($expected, sprintf($tag));
    }

    public function testAMethodGeneratesANewATag()
    {
        $tag = Tag::a('Phower', 'http://phower.com', ['title' => 'Phower']);
        $this->assertEquals('a', $tag->getName());
        $expected = '<a href="http&#x3A;&#x2F;&#x2F;phower.com" title="Phower">Phower</a>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testBlockquoteMethodGeneratesANewBlockquoteTag()
    {
        $tag = Tag::blockquote('Phower is the power of PHP.', 'http://phower.com', ['id' => 'quote']);
        $this->assertEquals('blockquote', $tag->getName());
        $expected = '<blockquote cite="http&#x3A;&#x2F;&#x2F;phower.com" id="quote">Phower is the power of PHP.</blockquote>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testH1MethodGeneratesANewH1Tag()
    {
        $tag = Tag::h1('Phower is the power of PHP.', ['id' => 'heading']);
        $this->assertEquals('h1', $tag->getName());
        $expected = '<h1 id="heading">Phower is the power of PHP.</h1>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testH2MethodGeneratesANewH2Tag()
    {
        $tag = Tag::h2('Phower is the power of PHP.', ['id' => 'heading']);
        $this->assertEquals('h2', $tag->getName());
        $expected = '<h2 id="heading">Phower is the power of PHP.</h2>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testH3MethodGeneratesANewH3Tag()
    {
        $tag = Tag::h3('Phower is the power of PHP.', ['id' => 'heading']);
        $this->assertEquals('h3', $tag->getName());
        $expected = '<h3 id="heading">Phower is the power of PHP.</h3>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testH4MethodGeneratesANewH4Tag()
    {
        $tag = Tag::h4('Phower is the power of PHP.', ['id' => 'heading']);
        $this->assertEquals('h4', $tag->getName());
        $expected = '<h4 id="heading">Phower is the power of PHP.</h4>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testH5MethodGeneratesANewH5Tag()
    {
        $tag = Tag::h5('Phower is the power of PHP.', ['id' => 'heading']);
        $this->assertEquals('h5', $tag->getName());
        $expected = '<h5 id="heading">Phower is the power of PHP.</h5>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testH6MethodGeneratesANewH6Tag()
    {
        $tag = Tag::h6('Phower is the power of PHP.', ['id' => 'heading']);
        $this->assertEquals('h6', $tag->getName());
        $expected = '<h6 id="heading">Phower is the power of PHP.</h6>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testIframeMethodGeneratesANewIframeTag()
    {
        $tag = Tag::iframe('', 'http://phower.com', 'my-iframe', ['id' => 'my-iframe']);
        $this->assertEquals('iframe', $tag->getName());
        $expected = '<iframe src="http&#x3A;&#x2F;&#x2F;phower.com" name="my-iframe" id="my-iframe"></iframe>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testImgMethodGeneratesANewImgTag()
    {
        $tag = Tag::img('logo.png', 'logo', 200, 50, ['id' => 'logo']);
        $this->assertEquals('img', $tag->getName());
        $expected = '<img src="logo.png" alt="logo" width="200" height="50" id="logo">';
        $this->assertEquals($expected, $tag->render());
    }

}
