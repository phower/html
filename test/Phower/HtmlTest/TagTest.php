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

    public function testAbbrMethodGeneratesANewAbbrTag()
    {
        $tag = Tag::abbr('WWW', 'World Wide Web');
        $this->assertEquals('abbr', $tag->getName());
        $expected = '<abbr title="World&#x20;Wide&#x20;Web">WWW</abbr>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testAddressMethodGeneratesANewAddressTag()
    {
        $tag = Tag::address('info@phower.com');
        $this->assertEquals('address', $tag->getName());
        $expected = '<address>info@phower.com</address>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testArticleMethodGeneratesANewArticleTag()
    {
        $tag = Tag::article([
            Tag::h1('Title'), Tag::p('Paragraph...')
        ]);
        $this->assertEquals('article', $tag->getName());
        $expected = '<article><h1>Title</h1><p>Paragraph...</p></article>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testAsideMethodGeneratesANewAsideTag()
    {
        $tag = Tag::aside([
            Tag::h1('Title'), Tag::p('Paragraph...')
        ]);
        $this->assertEquals('aside', $tag->getName());
        $expected = '<aside><h1>Title</h1><p>Paragraph...</p></aside>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testAudioMethodGeneratesANewAudioTag()
    {
        $tag = Tag::audio([
            new Tag('source', null, ['src' => 'sound.mp3', 'type' => 'audio/mpeg'])
        ], 'controls', 'loop', 'muted', 'auto');
        $this->assertEquals('audio', $tag->getName());
        $expected = '<audio controls="controls" loop="loop" muted="muted" preload="auto"><source src="sound.mp3" type="audio&#x2F;mpeg"></audio>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testBMethodGeneratesANewBTag()
    {
        $tag = Tag::b('Phower is the power of PHP.', ['id' => 'heading']);
        $this->assertEquals('b', $tag->getName());
        $expected = '<b id="heading">Phower is the power of PHP.</b>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testBaseMethodGeneratesANewBaseTag()
    {
        $tag = Tag::base('http://phower.com', '_blank');
        $this->assertEquals('base', $tag->getName());
        $expected = '<base href="http&#x3A;&#x2F;&#x2F;phower.com" target="_blank">';
        $this->assertEquals($expected, $tag->render());
    }

    public function testBlockquoteMethodGeneratesANewBlockquoteTag()
    {
        $tag = Tag::blockquote('Phower is the power of PHP.', 'http://phower.com', ['id' => 'quote']);
        $this->assertEquals('blockquote', $tag->getName());
        $expected = '<blockquote cite="http&#x3A;&#x2F;&#x2F;phower.com" id="quote">Phower is the power of PHP.</blockquote>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testBodyMethodGeneratesANewBodyTag()
    {
        $tag = Tag::body([Tag::h1('Hello World!')]);
        $this->assertEquals('body', $tag->getName());
        $expected = '<body><h1>Hello World!</h1></body>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testBrMethodGeneratesANewBrTag()
    {
        $tag = Tag::br();
        $this->assertEquals('br', $tag->getName());
        $expected = '<br>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testButtonMethodGeneratesANewButtonTag()
    {
        $tag = Tag::button('Save', 'submit', 'save');
        $this->assertEquals('button', $tag->getName());
        $expected = '<button type="submit" name="save">Save</button>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testCanvasMethodGeneratesANewCanvasTag()
    {
        $tag = Tag::canvas('', 800, 600);
        $this->assertEquals('canvas', $tag->getName());
        $expected = '<canvas width="800" height="600"></canvas>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testCaptionMethodGeneratesANewCaptionTag()
    {
        $tag = Tag::caption('Phower is the power of PHP.');
        $this->assertEquals('caption', $tag->getName());
        $expected = '<caption>Phower is the power of PHP.</caption>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testCiteMethodGeneratesANewCiteTag()
    {
        $tag = Tag::cite('Phower is the power of PHP.');
        $this->assertEquals('cite', $tag->getName());
        $expected = '<cite>Phower is the power of PHP.</cite>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testCodeMethodGeneratesANewCodeTag()
    {
        $tag = Tag::code('var x = 1;');
        $this->assertEquals('code', $tag->getName());
        $expected = '<code>var x = 1;</code>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testColMethodGeneratesANewColTag()
    {
        $tag = Tag::col(2);
        $this->assertEquals('col', $tag->getName());
        $expected = '<col span="2">';
        $this->assertEquals($expected, $tag->render());
    }

    public function testColgroupMethodGeneratesANewColgroupTag()
    {
        $tag = Tag::colgroup([Tag::col(), Tag::col(2)], 2);
        $this->assertEquals('colgroup', $tag->getName());
        $expected = '<colgroup span="2"><col><col span="2"></colgroup>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testDdMethodGeneratesANewDdTag()
    {
        $tag = Tag::dd('Phower is the power of PHP.');
        $this->assertEquals('dd', $tag->getName());
        $expected = '<dd>Phower is the power of PHP.</dd>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testDelMethodGeneratesANewDelTag()
    {
        $escaper = new Escaper();
        $now = date('c');
        $tag = Tag::del('Apple', 'why.html', $now);
        $this->assertEquals('del', $tag->getName());
        $expected = sprintf('<del cite="why.html" datetime="%s">Apple</del>', $escaper->escapeAttribute($now));
        $this->assertEquals($expected, $tag->render());
    }

    public function testDfnMethodGeneratesANewDfnTag()
    {
        $tag = Tag::dfn('HTML');
        $this->assertEquals('dfn', $tag->getName());
        $expected = '<dfn>HTML</dfn>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testDivMethodGeneratesANewDivTag()
    {
        $tag = Tag::div('Some content', 'div-id', 'div-class');
        $this->assertEquals('div', $tag->getName());
        $expected = '<div id="div-id" class="div-class">Some content</div>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testDlMethodGeneratesANewDlTag()
    {
        $tag = Tag::dl(Tag::dd('Phower is the power of PHP.'));
        $this->assertEquals('dl', $tag->getName());
        $expected = '<dl><dd>Phower is the power of PHP.</dd></dl>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testDtMethodGeneratesANewDtTag()
    {
        $tag = Tag::dt('Phower is the power of PHP.');
        $this->assertEquals('dt', $tag->getName());
        $expected = '<dt>Phower is the power of PHP.</dt>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testEmMethodGeneratesANewEmTag()
    {
        $tag = Tag::em('Phower is the power of PHP.');
        $this->assertEquals('em', $tag->getName());
        $expected = '<em>Phower is the power of PHP.</em>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testEmbedMethodGeneratesANewEmbedTag()
    {
        $tag = Tag::embed('movie.swf', 'application/x-shockwave-flash', 640, 400);
        $this->assertEquals('embed', $tag->getName());
        $expected = '<embed src="movie.swf" type="application&#x2F;x-shockwave-flash" width="640" height="400">';
        $this->assertEquals($expected, $tag->render());
    }

    public function testFieldsetMethodGeneratesANewFieldsetTag()
    {
        $tag = Tag::fieldset(new Tag('legend', 'The Legend'), 'my-fieldset');
        $this->assertEquals('fieldset', $tag->getName());
        $expected = '<fieldset name="my-fieldset"><legend>The Legend</legend></fieldset>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testFigcaptionMethodGeneratesANewFigcaptionTag()
    {
        $tag = Tag::figcaption('Phower is the power of PHP.');
        $this->assertEquals('figcaption', $tag->getName());
        $expected = '<figcaption>Phower is the power of PHP.</figcaption>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testFigureMethodGeneratesANewFigureTag()
    {
        $tag = Tag::figure(Tag::figcaption('Phower is the power of PHP.'));
        $this->assertEquals('figure', $tag->getName());
        $expected = '<figure><figcaption>Phower is the power of PHP.</figcaption></figure>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testFooterMethodGeneratesANewFooterTag()
    {
        $tag = Tag::footer('Phower is the power of PHP.');
        $this->assertEquals('footer', $tag->getName());
        $expected = '<footer>Phower is the power of PHP.</footer>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testFormMethodGeneratesANewFormTag()
    {
        $tag = Tag::form('', 'index.php', 'post', 'application/x-www-form-urlencoded', 'my-form');
        $this->assertEquals('form', $tag->getName());
        $expected = '<form action="index.php" method="post" enctype="application&#x2F;x-www-form-urlencoded" name="my-form"></form>';
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

    public function testHeadMethodGeneratesANewHeadTag()
    {
        $tag = Tag::head(new Tag('title', 'Phower'));
        $this->assertEquals('head', $tag->getName());
        $expected = '<head><title>Phower</title></head>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testHrMethodGeneratesANewHrTag()
    {
        $tag = Tag::hr();
        $this->assertEquals('hr', $tag->getName());
        $expected = '<hr>';
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

    public function testInputMethodGeneratesANewInputTag()
    {
        $tag = Tag::input('text', 'my-input', 'Phower');
        $this->assertEquals('input', $tag->getName());
        $expected = '<input type="text" name="my-input" value="Phower">';
        $this->assertEquals($expected, $tag->render());
    }

    public function testInsMethodGeneratesANewInsTag()
    {
        $tag = Tag::ins('Phower');
        $this->assertEquals('ins', $tag->getName());
        $expected = '<ins>Phower</ins>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testKbdMethodGeneratesANewKbdTag()
    {
        $tag = Tag::kbd('Phower');
        $this->assertEquals('kbd', $tag->getName());
        $expected = '<kbd>Phower</kbd>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testLabelMethodGeneratesANewLabelTag()
    {
        $tag = Tag::label('My Label:', 'input-id');
        $this->assertEquals('label', $tag->getName());
        $expected = '<label for="input-id">My Label:</label>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testLegendMethodGeneratesANewLegendTag()
    {
        $tag = Tag::legend('My Legend:');
        $this->assertEquals('legend', $tag->getName());
        $expected = '<legend>My Legend:</legend>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testLiMethodGeneratesANewLiTag()
    {
        $tag = Tag::li('Phower is the power of PHP!');
        $this->assertEquals('li', $tag->getName());
        $expected = '<li>Phower is the power of PHP!</li>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testLinkMethodGeneratesANewLinkTag()
    {
        $tag = Tag::link('stylesheet', 'text/css', 'theme.css');
        $this->assertEquals('link', $tag->getName());
        $expected = '<link rel="stylesheet" type="text&#x2F;css" href="theme.css">';
        $this->assertEquals($expected, $tag->render());
    }

    public function testMapMethodGeneratesANewMapTag()
    {
        $tag = Tag::map('', 'my-map');
        $this->assertEquals('map', $tag->getName());
        $expected = '<map name="my-map"></map>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testMarkMethodGeneratesANewMarkTag()
    {
        $tag = Tag::mark('Phower');
        $this->assertEquals('mark', $tag->getName());
        $expected = '<mark>Phower</mark>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testMetaMethodGeneratesANewMetaTag()
    {
        $tag = Tag::meta('author', 'Pedro Brazao Ferreira');
        $this->assertEquals('meta', $tag->getName());
        $expected = '<meta name="author" content="Pedro&#x20;Brazao&#x20;Ferreira">';
        $this->assertEquals($expected, $tag->render());

        $tag = Tag::meta(null, null, 'utf-8');
        $this->assertEquals('meta', $tag->getName());
        $expected = '<meta charset="utf-8">';
        $this->assertEquals($expected, $tag->render());

        $tag = Tag::meta(null, '30', null, 'refresh');
        $this->assertEquals('meta', $tag->getName());
        $expected = '<meta content="30" http-equiv="refresh">';
        $this->assertEquals($expected, $tag->render());
    }

    public function testNavMethodGeneratesANewNavTag()
    {
        $tag = Tag::nav();
        $this->assertEquals('nav', $tag->getName());
        $expected = '<nav></nav>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testNoscriptMethodGeneratesANewNoscriptTag()
    {
        $tag = Tag::noscript('Your browser does not support JavaScript!');
        $this->assertEquals('noscript', $tag->getName());
        $expected = '<noscript>Your browser does not support JavaScript!</noscript>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testObjectMethodGeneratesANewObjectTag()
    {
        $tag = Tag::object('', 'movie', 'application/x-shockwave-flash', 'movie.swf', 640, 400);
        $this->assertEquals('object', $tag->getName());
        $expected = '<object name="movie" type="application&#x2F;x-shockwave-flash" data="movie.swf" width="640" height="400"></object>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testOlMethodGeneratesANewOlTag()
    {
        $tag = Tag::ol('', '1', '1', 'reversed');
        $this->assertEquals('ol', $tag->getName());
        $expected = '<ol type="1" start="1" reversed="reversed"></ol>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testOptgroupMethodGeneratesANewOptgroupTag()
    {
        $tag = Tag::optgroup('', 'Group Label');
        $this->assertEquals('optgroup', $tag->getName());
        $expected = '<optgroup label="Group&#x20;Label"></optgroup>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testOptionMethodGeneratesANewOptionTag()
    {
        $tag = Tag::option('Option Text', '1', true);
        $this->assertEquals('option', $tag->getName());
        $expected = '<option value="1" selected="selected">Option Text</option>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testPMethodGeneratesANewPTag()
    {
        $tag = Tag::p('Phower is the power of PHP.');
        $this->assertEquals('p', $tag->getName());
        $expected = '<p>Phower is the power of PHP.</p>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testParamMethodGeneratesANewParamTag()
    {
        $tag = Tag::param('autoplay', 'true');
        $this->assertEquals('param', $tag->getName());
        $expected = '<param name="autoplay" value="true">';
        $this->assertEquals($expected, $tag->render());
    }

    public function testPreMethodGeneratesANewPreTag()
    {
        $tag = Tag::pre('Phower is the power of PHP!');
        $this->assertEquals('pre', $tag->getName());
        $expected = '<pre>Phower is the power of PHP!</pre>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testQMethodGeneratesANewQTag()
    {
        $tag = Tag::q('Phower is the power of PHP.');
        $this->assertEquals('q', $tag->getName());
        $expected = '<q>Phower is the power of PHP.</q>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testSMethodGeneratesANewSTag()
    {
        $tag = Tag::s('Phower is the power of PHP.');
        $this->assertEquals('s', $tag->getName());
        $expected = '<s>Phower is the power of PHP.</s>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testSampMethodGeneratesANewSampTag()
    {
        $tag = Tag::samp('Phower is the power of PHP.');
        $this->assertEquals('samp', $tag->getName());
        $expected = '<samp>Phower is the power of PHP.</samp>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testScriptMethodGeneratesANewScriptTag()
    {
        $tag = Tag::script('', 'app.js', 'text/javascript');
        $this->assertEquals('script', $tag->getName());
        $expected = '<script src="app.js" type="text&#x2F;javascript"></script>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testSectionMethodGeneratesANewSectionTag()
    {
        $tag = Tag::section('Phower is the power of PHP.');
        $this->assertEquals('section', $tag->getName());
        $expected = '<section>Phower is the power of PHP.</section>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testSelectMethodGeneratesANewSelectTag()
    {
        $tag = Tag::select('', 'my-select', true, 3);
        $this->assertEquals('select', $tag->getName());
        $expected = '<select name="my-select" multiple="multiple" size="3"></select>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testSmallMethodGeneratesANewSmallTag()
    {
        $tag = Tag::small('Phower is the power of PHP.');
        $this->assertEquals('small', $tag->getName());
        $expected = '<small>Phower is the power of PHP.</small>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testSourceMethodGeneratesANewSourceTag()
    {
        $tag = Tag::source('movie.swf', 'application/x-shockwave-flash');
        $this->assertEquals('source', $tag->getName());
        $expected = '<source src="movie.swf" type="application&#x2F;x-shockwave-flash">';
        $this->assertEquals($expected, $tag->render());
    }

    public function testSpanMethodGeneratesANewSpanTag()
    {
        $tag = Tag::span('Phower is the power of PHP.');
        $this->assertEquals('span', $tag->getName());
        $expected = '<span>Phower is the power of PHP.</span>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testStrongMethodGeneratesANewStrongTag()
    {
        $tag = Tag::strong('Phower is the power of PHP.');
        $this->assertEquals('strong', $tag->getName());
        $expected = '<strong>Phower is the power of PHP.</strong>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testStyleMethodGeneratesANewStyleTag()
    {
        $tag = Tag::style('', 'text/css');
        $this->assertEquals('style', $tag->getName());
        $expected = '<style type="text&#x2F;css"></style>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testSubMethodGeneratesANewSubTag()
    {
        $tag = Tag::sub('Phower is the power of PHP.');
        $this->assertEquals('sub', $tag->getName());
        $expected = '<sub>Phower is the power of PHP.</sub>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testSupMethodGeneratesANewSupTag()
    {
        $tag = Tag::sup('Phower is the power of PHP.');
        $this->assertEquals('sup', $tag->getName());
        $expected = '<sup>Phower is the power of PHP.</sup>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testTableMethodGeneratesANewTableTag()
    {
        $tag = Tag::table('');
        $this->assertEquals('table', $tag->getName());
        $expected = '<table></table>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testTbodyMethodGeneratesANewTbodyTag()
    {
        $tag = Tag::tbody('');
        $this->assertEquals('tbody', $tag->getName());
        $expected = '<tbody></tbody>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testTdMethodGeneratesANewTdTag()
    {
        $tag = Tag::td('', 2, 2);
        $this->assertEquals('td', $tag->getName());
        $expected = '<td colspan="2" rowspan="2"></td>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testTextareaMethodGeneratesANewTextareaTag()
    {
        $tag = Tag::textarea('', 'my-textarea', 8, 60);
        $this->assertEquals('textarea', $tag->getName());
        $expected = '<textarea name="my-textarea" rows="8" cols="60"></textarea>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testTfootMethodGeneratesANewTfootTag()
    {
        $tag = Tag::tfoot('');
        $this->assertEquals('tfoot', $tag->getName());
        $expected = '<tfoot></tfoot>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testThMethodGeneratesANewThTag()
    {
        $tag = Tag::th('', 2, 2);
        $this->assertEquals('th', $tag->getName());
        $expected = '<th colspan="2" rowspan="2"></th>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testTheadMethodGeneratesANewTheadTag()
    {
        $tag = Tag::thead('');
        $this->assertEquals('thead', $tag->getName());
        $expected = '<thead></thead>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testTimeMethodGeneratesANewTimeTag()
    {
        $escaper = new Escaper();
        $now = date('c');
        $tag = Tag::time('now', $now);
        $this->assertEquals('time', $tag->getName());
        $expected = sprintf('<time datetime="%s">now</time>', $escaper->escapeAttribute($now));
        $this->assertEquals($expected, $tag->render());
    }

    public function testTitleMethodGeneratesANewTitleTag()
    {
        $tag = Tag::title('Phower is the power of PHP.');
        $this->assertEquals('title', $tag->getName());
        $expected = '<title>Phower is the power of PHP.</title>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testTrMethodGeneratesANewTrTag()
    {
        $tag = Tag::tr('Phower is the power of PHP.');
        $this->assertEquals('tr', $tag->getName());
        $expected = '<tr>Phower is the power of PHP.</tr>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testUMethodGeneratesANewUTag()
    {
        $tag = Tag::u('Phower is the power of PHP.');
        $this->assertEquals('u', $tag->getName());
        $expected = '<u>Phower is the power of PHP.</u>';
        $this->assertEquals($expected, $tag->render());
    }

    public function testUlMethodGeneratesANewUlTag()
    {
        $tag = Tag::ul();
        $this->assertEquals('ul', $tag->getName());
        $expected = '<ul></ul>';
        $this->assertEquals($expected, $tag->render());
    }

}
