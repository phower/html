<?php

namespace Phower\Html;

use InvalidArgumentException;
use Phower\Escaper\EscaperInterface;
use Phower\Escaper\Escaper;

class Tag implements TagInterface
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|TagInterfaceF|array
     */
    protected $value;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var boolean
     */
    protected $escape;

    /**
     * @var \Phower\Html\EscaperInterface
     */
    protected $escaper;

    /**
     * @var boolean
     */
    protected $xhtml = false;

    /**
     * @var string
     */
    protected $closingBracket;

    /**
     * Construct
     * 
     * @param string $name
     * @param string|TagInterface|array $value
     * @param array $attributes
     * @param boolean $escape
     */
    public function __construct($name, $value = null, array $attributes = [], $escape = true, EscaperInterface $escaper = null)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Tag name must be a string.');
        }

        if (strlen(trim($name)) === 0) {
            throw new InvalidArgumentException('Tag name can\'t be an empty string.');
        }

        if (!($value === null || is_string($value) || $value instanceof TagInterface || is_array($value))) {
            throw new InvalidArgumentException('Tag value must be null, a string, an instance of \Phower\Html\TagInterface or an array.');
        }

        $this->name = $name;
        $this->value = $value;
        $this->attributes = $attributes;
        $this->escape = (boolean) $escape;
        $this->escaper = $escaper;
    }

    /**
     * Get name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     * 
     * @param string|TagInterface|array $value
     * @return self
     */
    public function setValue($value)
    {
        if (!($value === null || is_string($value) || $value instanceof TagInterface || is_array($value))) {
            throw new InvalidArgumentException('Tag value must be null, a string, an instance of \Phower\Html\TagInterface or an array.');
        }

        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     * 
     * @return string|TagInterface|array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set attributes
     * 
     * @param array $attributes
     * @return self
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = [];
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }
        return $this;
    }

    /**
     * Has attribute
     * 
     * @param string $name
     * @return boolean
     */
    public function hasAttribute($name)
    {
        return array_key_exists($name, $this->attributes);
    }

    /**
     * Get attributes
     * 
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Add attribute
     * 
     * @param string $name
     * @param string $value
     * @return self
     */
    public function addAttribute($name, $value)
    {
        if ($this->hasAttribute($name)) {
            throw new InvalidArgumentException(sprintf('Attribute with name "%s" already exists.', $name));
        }
        $this->setAttribute($name, $value);
        return $this;
    }

    /**
     * Add attributes
     * 
     * @param array $attributes
     * @return self
     */
    public function addAttributes(array $attributes)
    {
        foreach ($attributes as $name => $value) {
            $this->addAttribute($name, $value);
        }
        return $this;
    }

    /**
     * Get attribute
     * 
     * @param string $name
     * @return string
     */
    public function getAttribute($name)
    {
        if (!$this->hasAttribute($name)) {
            throw new InvalidArgumentException(sprintf('Attribute "%s" does not exist.', $name));
        }
        return $this->attributes[$name];
    }

    /**
     * Set attribute
     * 
     * @param string $name
     * @param string $value
     * @return self
     */
    public function setAttribute($name, $value)
    {
        if (!is_string($name) || strlen(trim($name)) === 0) {
            throw new InvalidArgumentException('Arguments name must be a string.');
        }
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * Escape
     * 
     * @param boolean|null $escape
     * @return self|boolean
     */
    public function escape($escape = null)
    {
        if ($escape !== null) {
            $this->escape = (boolean) $escape;
            return $this;
        }

        return $this->escape;
    }

    /**
     * Set escaper
     * 
     * @param \Phower\Html\EscaperInterface $escaper
     * @return self
     */
    public function setEscaper(EscaperInterface $escaper)
    {
        $this->escaper = $escaper;
        return $this;
    }

    /**
     * Get html escaper
     * 
     * @return \Phower\Html\EscaperInterface|null
     */
    public function getEscaper()
    {
        if ($this->escaper === null) {
            $this->escaper = new Escaper();
        }
        return $this->escaper;
    }

    /**
     * Is XHTML
     * 
     * @param boolean|null $isXhtml
     * @return \Phower\Html\Tag|boolean
     */
    public function isXhtml($isXhtml = null)
    {
        if ($isXhtml !== null) {
            $this->closingBracket = null;
            $this->xhtml = (boolean) $isXhtml;
            return $this;
        }

        return $this->xhtml;
    }

    /**
     * Get closing bracket
     * 
     * @return string
     */
    public function getClosingBracket()
    {
        if ($this->closingBracket === null) {
            if ($this->isXhtml()) {
                $this->closingBracket = ' />';
            } else {
                $this->closingBracket = '>';
            }
        }

        return $this->closingBracket;
    }

    /**
     * Convert attributes to HTML string
     * 
     * @param array $attributes
     * @return string
     */
    protected function htmlAttributes(array $attributes)
    {
        $html = '';
        $escaper = $this->getEscaper();

        foreach ($attributes as $name => $value) {
            $name = $escaper->escapeHtml($name);

            if (is_array($value)) {
                $value = implode(' ', $value);
            }

            $value = $escaper->escapeAttribute($value);
            $html .= " $name=" . $this->quote($value);
        }

        return $html;
    }

    /**
     * Quote
     * 
     * @param string $value
     * @return string
     */
    public function quote($value)
    {
        if (strpos($value, '"') !== false) {
            $quoted = "'$value'";
        } else {
            $quoted = "\"$value\"";
        }
        return $quoted;
    }

    /**
     * Open tag
     * 
     * @return string
     */
    public function openTag()
    {
        $html = '<' . $this->name;

        if (!empty($this->attributes)) {
            $html .= $this->htmlAttributes($this->attributes);
        }

        $html .= '>';

        return $html;
    }

    /**
     * Close tag
     * 
     * @return string
     */
    public function closeTag()
    {
        $html = '</' . $this->name . '>';
        return $html;
    }

    /**
     * Render
     * 
     * @return string
     */
    public function render()
    {
        $html = '';

        if ($this->value === null) {
            $html = substr($this->openTag(), 0, -1) . $this->getClosingBracket();
        } else {
            $html = $this->openTag();

            if (is_string($this->value)) {
                $html .= $this->escape ?
                        $this->getEscaper()->escapeHtml($this->value) :
                        $this->value;
            } elseif ($this->value instanceof TagInterface) {
                $html .= $this->value->render();
            } elseif (is_array($this->value)) {
                foreach ($this->value as $value) {
                    if (is_string($value)) {
                        $html .= $this->escape ?
                                $this->getEscaper()->escapeHtml($value) :
                                $value;
                    } elseif ($value instanceof TagInterface) {
                        $html .= $value->render();
                    }
                }
            }

            $html .= $this->closeTag();
        }

        return $html;
    }

    /**
     * Output this tag render result
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Generate a new "a" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $href
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function a($value = '', $href = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($href !== null) {
            $mainAttributes['href'] = $href;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('a', $value, $attributes);
    }

    /**
     * Generate a new "abbr" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $title
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function abbr($value = '', $title = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($title !== null) {
            $mainAttributes['title'] = $title;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('abbr', $value, $attributes);
    }

    /**
     * Generate a new "address" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function address($value = '', array $attributes = [])
    {
        return new static('address', $value, $attributes);
    }

    /**
     * Generate a new "article" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function article($value = '', array $attributes = [])
    {
        return new static('article', $value, $attributes);
    }

    /**
     * Generate a new "aside" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function aside($value = '', array $attributes = [])
    {
        return new static('aside', $value, $attributes);
    }

    /**
     * Generate a new "audio" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $controls
     * @param string|null $loop
     * @param string|null $muted
     * @param string|null $preload
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function audio($value = '', $controls = null, $loop = null, $muted = null, $preload = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($controls !== null) {
            $mainAttributes['controls'] = $controls;
        }

        if ($loop !== null) {
            $mainAttributes['loop'] = $loop;
        }

        if ($muted !== null) {
            $mainAttributes['muted'] = $muted;
        }

        if ($preload !== null) {
            $mainAttributes['preload'] = $preload;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('audio', $value, $attributes);
    }

    /**
     * Generate a new "b" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function b($value = '', array $attributes = [])
    {
        return new static('b', $value, $attributes);
    }

    /**
     * Generate a new "base" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $href
     * @param string|null $target
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function base($href = null, $target = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($href !== null) {
            $mainAttributes['href'] = $href;
        }

        if ($target !== null) {
            $mainAttributes['target'] = $target;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('base', null, $attributes);
    }

    /**
     * Generate a new "blockquote" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $cite
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function blockquote($value = '', $cite = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($cite !== null) {
            $mainAttributes['cite'] = $cite;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('blockquote', $value, $attributes);
    }

    /**
     * Generate a new "body" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function body($value = '', array $attributes = [])
    {
        return new static('body', $value, $attributes);
    }

    /**
     * Generate a new "br" tag
     * 
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function br(array $attributes = [])
    {
        return new static('br', null, $attributes);
    }

    /**
     * Generate a new "button" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $type
     * @param string|null $name
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function button($value = '', $type = null, $name = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($type !== null) {
            $mainAttributes['type'] = $type;
        }

        if ($name !== null) {
            $mainAttributes['name'] = $name;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('button', $value, $attributes);
    }

    /**
     * Generate a new "canvas" tag
     * 
     * @param string $value
     * @param string|null $width
     * @param string|null $height
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function canvas($value = '', $width = null, $height = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($width !== null) {
            $mainAttributes['width'] = $width;
        }

        if ($height !== null) {
            $mainAttributes['height'] = $height;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('canvas', $value, $attributes);
    }

    /**
     * Generate a new "caption" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function caption($value = '', array $attributes = [])
    {
        return new static('caption', $value, $attributes);
    }

    /**
     * Generate a new "cite" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function cite($value = '', array $attributes = [])
    {
        return new static('cite', $value, $attributes);
    }

    /**
     * Generate a new "code" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function code($value = '', array $attributes = [])
    {
        return new static('code', $value, $attributes);
    }

    /**
     * Generate a new "col" tag
     * 
     * @param int|null $span
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function col($span = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($span !== null) {
            $mainAttributes['span'] = $span;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('col', null, $attributes);
    }

    /**
     * Generate a new "colgroup" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param int|null $span
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function colgroup($value = '', $span = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($span !== null) {
            $mainAttributes['span'] = $span;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('colgroup', $value, $attributes);
    }

    /**
     * Generate a new "dd" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function dd($value = '', array $attributes = [])
    {
        return new static('dd', $value, $attributes);
    }

    /**
     * Generate a new "del" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $cite
     * @param string|null $datetime
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function del($value = '', $cite = null, $datetime = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($cite !== null) {
            $mainAttributes['cite'] = $cite;
        }

        if ($datetime !== null) {
            $mainAttributes['datetime'] = $datetime;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('del', $value, $attributes);
    }

    /**
     * Generate a new "dfn" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function dfn($value = '', array $attributes = [])
    {
        return new static('dfn', $value, $attributes);
    }

    /**
     * Generate a new "div" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $id
     * @param string|null $class
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function div($value = '', $id = null, $class = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($id !== null) {
            $mainAttributes['id'] = $id;
        }

        if ($class !== null) {
            $mainAttributes['class'] = $class;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('div', $value, $attributes);
    }

    /**
     * Generate a new "dl" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function dl($value = '', array $attributes = [])
    {
        return new static('dl', $value, $attributes);
    }

    /**
     * Generate a new "dt" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function dt($value = '', array $attributes = [])
    {
        return new static('dt', $value, $attributes);
    }

    /**
     * Generate a new "em" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function em($value = '', array $attributes = [])
    {
        return new static('em', $value, $attributes);
    }

    /**
     * Generate a new "embed" tag
     * 
     * @param string|null $src
     * @param string|null $type
     * @param int|null $width
     * @param int|null $height
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function embed($src = null, $type = null, $width = null, $height = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($src !== null) {
            $mainAttributes['src'] = $src;
        }

        if ($type !== null) {
            $mainAttributes['type'] = $type;
        }

        if ($width !== null && (int) $width > 0) {
            $mainAttributes['width'] = (int) $width;
        }

        if ($height !== null && (int) $height > 0) {
            $mainAttributes['height'] = (int) $height;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('embed', null, $attributes);
    }

    /**
     * Generate a new "fieldset" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $name
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function fieldset($value = '', $name = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($name !== null) {
            $mainAttributes['name'] = $name;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('fieldset', $value, $attributes);
    }

    /**
     * Generate a new "figcaption" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function figcaption($value = '', array $attributes = [])
    {
        return new static('figcaption', $value, $attributes);
    }

    /**
     * Generate a new "figure" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function figure($value = '', array $attributes = [])
    {
        return new static('figure', $value, $attributes);
    }

    /**
     * Generate a new "footer" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function footer($value = '', array $attributes = [])
    {
        return new static('footer', $value, $attributes);
    }

    /**
     * Generate a new "form" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $action
     * @param string|null $method
     * @param string|null $enctype
     * @param string|null $name
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function form($value = '', $action = null, $method = null, $enctype = null, $name = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($action !== null) {
            $mainAttributes['action'] = $action;
        }

        if ($method !== null) {
            $mainAttributes['method'] = $method;
        }

        if ($enctype !== null) {
            $mainAttributes['enctype'] = $enctype;
        }

        if ($name !== null) {
            $mainAttributes['name'] = $name;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('form', $value, $attributes);
    }

    /**
     * Generate a new "h1" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function h1($value = '', array $attributes = [])
    {
        return new static('h1', $value, $attributes);
    }

    /**
     * Generate a new "h2" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function h2($value = '', array $attributes = [])
    {
        return new static('h2', $value, $attributes);
    }

    /**
     * Generate a new "h3" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function h3($value = '', array $attributes = [])
    {
        return new static('h3', $value, $attributes);
    }

    /**
     * Generate a new "h4" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function h4($value = '', array $attributes = [])
    {
        return new static('h4', $value, $attributes);
    }

    /**
     * Generate a new "h5" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function h5($value = '', array $attributes = [])
    {
        return new static('h5', $value, $attributes);
    }

    /**
     * Generate a new "h6" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function h6($value = '', array $attributes = [])
    {
        return new static('h6', $value, $attributes);
    }

    /**
     * Generate a new "head" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function head($value = '', array $attributes = [])
    {
        return new static('head', $value, $attributes);
    }

    /**
     * Generate a new "hr" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function hr(array $attributes = [])
    {
        return new static('hr', null, $attributes);
    }

    /**
     * Generate a new "iframe" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $src
     * @param string|null $name
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function iframe($value = '', $src = null, $name = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($src !== null) {
            $mainAttributes['src'] = $src;
        }

        if ($name !== null) {
            $mainAttributes['name'] = $name;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('iframe', $value, $attributes);
    }

    /**
     * Generate a new "img" tag
     * 
     * @param string|null $src
     * @param string|null $alt
     * @param int|null $width
     * @param int|null $height
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function img($src = null, $alt = null, $width = null, $height = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($src !== null) {
            $mainAttributes['src'] = $src;
        }

        if ($alt !== null) {
            $mainAttributes['alt'] = $alt;
        }

        if ($width !== null && (int) $width > 0) {
            $mainAttributes['width'] = (int) $width;
        }

        if ($height !== null && (int) $height > 0) {
            $mainAttributes['height'] = (int) $height;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('img', null, $attributes);
    }

    /**
     * Generate a new "input" tag
     * 
     * @param string|null $type
     * @param string|null $name
     * @param string|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function input($type = null, $name = null, $value = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($type !== null) {
            $mainAttributes['type'] = $type;
        }

        if ($name !== null) {
            $mainAttributes['name'] = $name;
        }

        if ($value !== null) {
            $mainAttributes['value'] = $value;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('input', null, $attributes);
    }

    /**
     * Generate a new "ins" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function ins($value = '', array $attributes = [])
    {
        return new static('ins', $value, $attributes);
    }

    /**
     * Generate a new "kbd" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function kbd($value = '', array $attributes = [])
    {
        return new static('kbd', $value, $attributes);
    }

    /**
     * Generate a new "label" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $for
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function label($value = '', $for = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($for !== null) {
            $mainAttributes['for'] = $for;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('label', $value, $attributes);
    }

    /**
     * Generate a new "legend" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function legend($value = '', array $attributes = [])
    {
        return new static('legend', $value, $attributes);
    }

    /**
     * Generate a new "li" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function li($value = '', array $attributes = [])
    {
        return new static('li', $value, $attributes);
    }

    /**
     * Generate a new "link" tag
     * 
     * @param string|null $rel
     * @param string|null $type
     * @param string|null $href
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function link($rel = null, $type = null, $href = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($rel !== null) {
            $mainAttributes['rel'] = $rel;
        }

        if ($type !== null) {
            $mainAttributes['type'] = $type;
        }

        if ($href !== null) {
            $mainAttributes['href'] = $href;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('link', null, $attributes);
    }

    /**
     * Generate a new "map" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $name
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function map($value = '', $name = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($name !== null) {
            $mainAttributes['name'] = $name;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('map', $value, $attributes);
    }

    /**
     * Generate a new "mark" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function mark($value = '', array $attributes = [])
    {
        return new static('mark', $value, $attributes);
    }

    /**
     * Generate a new "meta" tag
     * 
     * @param string|null $name
     * @param string|null $content
     * @param string|null $charset
     * @param string|null $httpEquiv
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function meta($name = null, $content = null, $charset = null, $httpEquiv = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($name !== null) {
            $mainAttributes['name'] = $name;
        }

        if ($content !== null) {
            $mainAttributes['content'] = $content;
        }

        if ($charset !== null) {
            $mainAttributes['charset'] = $charset;
        }

        if ($httpEquiv !== null) {
            $mainAttributes['http-equiv'] = $httpEquiv;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('meta', null, $attributes);
    }

    /**
     * Generate a new "nav" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function nav($value = '', array $attributes = [])
    {
        return new static('nav', $value, $attributes);
    }

    /**
     * Generate a new "noscript" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function noscript($value = '', array $attributes = [])
    {
        return new static('noscript', $value, $attributes);
    }

    /**
     * Generate a new "object" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $name
     * @param string|null $type
     * @param string|null $data
     * @param int|null $width
     * @param int|null $height
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function object($value = '', $name = null, $type = null, $data = null, $width = null, $height = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($name !== null) {
            $mainAttributes['name'] = $name;
        }

        if ($type !== null) {
            $mainAttributes['type'] = $type;
        }

        if ($data !== null) {
            $mainAttributes['data'] = $data;
        }

        if ($width !== null && (int) $width > 0) {
            $mainAttributes['width'] = (int) $width;
        }

        if ($height !== null && (int) $height > 0) {
            $mainAttributes['height'] = (int) $height;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('object', $value, $attributes);
    }

    /**
     * Generate a new "ol" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $type
     * @param string|null $start
     * @param string|null $reversed
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function ol($value = '', $type = null, $start = null, $reversed = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($type !== null) {
            $mainAttributes['type'] = $type;
        }

        if ($start !== null) {
            $mainAttributes['start'] = $start;
        }

        if ($reversed !== null) {
            $mainAttributes['reversed'] = $reversed;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('ol', $value, $attributes);
    }

    /**
     * Generate a new "optgroup" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $label
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function optgroup($value = '', $label = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($label !== null) {
            $mainAttributes['label'] = $label;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('optgroup', $value, $attributes);
    }

    /**
     * Generate a new "option" tag
     * 
     * @param string|TagInterface|array|null $text
     * @param string|null $value
     * @param boolean|null $selected
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function option($text = '', $value = null, $selected = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($value !== null) {
            $mainAttributes['value'] = $value;
        }

        if ($selected) {
            $mainAttributes['selected'] = 'selected';
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('option', $text, $attributes);
    }

    /**
     * Generate a new "p" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function p($value = '', array $attributes = [])
    {
        return new static('p', $value, $attributes);
    }

    /**
     * Generate a new "param" tag
     * 
     * @param string|null $name
     * @param string|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function param($name = null, $value = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($name !== null) {
            $mainAttributes['name'] = $name;
        }

        if ($value !== null) {
            $mainAttributes['value'] = $value;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('param', null, $attributes);
    }

    /**
     * Generate a new "pre" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function pre($value = '', array $attributes = [])
    {
        return new static('pre', $value, $attributes);
    }

    /**
     * Generate a new "q" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function q($value = '', array $attributes = [])
    {
        return new static('q', $value, $attributes);
    }

    /**
     * Generate a new "s" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function s($value = '', array $attributes = [])
    {
        return new static('s', $value, $attributes);
    }

    /**
     * Generate a new "samp" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function samp($value = '', array $attributes = [])
    {
        return new static('samp', $value, $attributes);
    }

    /**
     * Generate a new "script" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $name
     * @param string|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function script($value = '', $src = null, $type = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($src !== null) {
            $mainAttributes['src'] = $src;
        }

        if ($type !== null) {
            $mainAttributes['type'] = $type;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('script', $value, $attributes);
    }

    /**
     * Generate a new "section" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function section($value = '', array $attributes = [])
    {
        return new static('section', $value, $attributes);
    }

    /**
     * Generate a new "select" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $name
     * @param boolean|null $multiple
     * @param int|null $size
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function select($value = '', $name = null, $multiple = null, $size = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($name !== null) {
            $mainAttributes['name'] = $name;
        }

        if ($multiple) {
            $mainAttributes['multiple'] = 'multiple';
        }

        if ($size && (int) $size > 0) {
            $mainAttributes['size'] = (int) $size;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('select', $value, $attributes);
    }

    /**
     * Generate a new "small" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function small($value = '', array $attributes = [])
    {
        return new static('small', $value, $attributes);
    }

    /**
     * Generate a new "source" tag
     * 
     * @param string|null $src
     * @param string|null $type
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function source($src = null, $type = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($src !== null) {
            $mainAttributes['src'] = $src;
        }

        if ($type !== null) {
            $mainAttributes['type'] = $type;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('source', null, $attributes);
    }

    /**
     * Generate a new "span" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function span($value = '', array $attributes = [])
    {
        return new static('span', $value, $attributes);
    }

    /**
     * Generate a new "strong" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function strong($value = '', array $attributes = [])
    {
        return new static('strong', $value, $attributes);
    }

    /**
     * Generate a new "style" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $type
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function style($value = '', $type = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($type !== null) {
            $mainAttributes['type'] = $type;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('style', $value, $attributes);
    }

    /**
     * Generate a new "sub" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function sub($value = '', array $attributes = [])
    {
        return new static('sub', $value, $attributes);
    }

    /**
     * Generate a new "sup" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function sup($value = '', array $attributes = [])
    {
        return new static('sup', $value, $attributes);
    }

    /**
     * Generate a new "table" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function table($value = '', array $attributes = [])
    {
        return new static('table', $value, $attributes);
    }

    /**
     * Generate a new "tbody" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function tbody($value = '', array $attributes = [])
    {
        return new static('tbody', $value, $attributes);
    }

    /**
     * Generate a new "td" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param int|null $colspan
     * @param int|null $rowspan
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function td($value = '', $colspan = null, $rowspan = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($colspan !== null && (int) $colspan > 0) {
            $mainAttributes['colspan'] = (int) $colspan;
        }

        if ($rowspan !== null && (int) $rowspan > 0) {
            $mainAttributes['rowspan'] = (int) $rowspan;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('td', $value, $attributes);
    }

    /**
     * Generate a new "textarea" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $name
     * @param int|null $rows
     * @param int|null $cols
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function textarea($value = '', $name = null, $rows = null, $cols = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($name !== null) {
            $mainAttributes['name'] = $name;
        }

        if ($rows !== null && (int) $rows > 0) {
            $mainAttributes['rows'] = (int) $rows;
        }

        if ($cols !== null && (int) $cols > 0) {
            $mainAttributes['cols'] = (int) $cols;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('textarea', $value, $attributes);
    }

    /**
     * Generate a new "tfoot" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function tfoot($value = '', array $attributes = [])
    {
        return new static('tfoot', $value, $attributes);
    }

    /**
     * Generate a new "th" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param int|null $colspan
     * @param int|null $rowspan
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function th($value = '', $colspan = null, $rowspan = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($colspan !== null && (int) $colspan > 0) {
            $mainAttributes['colspan'] = (int) $colspan;
        }

        if ($rowspan !== null && (int) $rowspan > 0) {
            $mainAttributes['rowspan'] = (int) $rowspan;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('th', $value, $attributes);
    }

    /**
     * Generate a new "thead" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function thead($value = '', array $attributes = [])
    {
        return new static('thead', $value, $attributes);
    }

    /**
     * Generate a new "time" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param string|null $datetime
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function time($value = '', $datetime = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($datetime !== null) {
            $mainAttributes['datetime'] = $datetime;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('time', $value, $attributes);
    }

    /**
     * Generate a new "title" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function title($value = '', array $attributes = [])
    {
        return new static('title', $value, $attributes);
    }

    /**
     * Generate a new "tr" tag
     * 
     * @param string|TagInterface|array|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function tr($value = '', array $attributes = [])
    {
        return new static('tr', $value, $attributes);
    }

    /**
     * Generate a new "u" tag
     * 
     * @param suing|TagInterface|array|null $value
     * @param array $atuibutes
     * @return \Phower\Html\Tag
     */
    public static function u($value = '', array $atuibutes = [])
    {
        return new static('u', $value, $atuibutes);
    }

    /**
     * Generate a new "ul" tag
     * 
     * @param suing|TagInterface|array|null $value
     * @param array $atuibutes
     * @return \Phower\Html\Tag
     */
    public static function ul($value = '', array $atuibutes = [])
    {
        return new static('ul', $value, $atuibutes);
    }

}
