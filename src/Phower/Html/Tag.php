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
     * @param string|null $value
     * @param string|null $href
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function a($value = null, $href = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($href !== null) {
            $mainAttributes['href'] = $href;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('a', $value, $attributes);
    }

    /**
     * Generate a new "blockquote" tag
     * 
     * @param string|null $value
     * @param string|null $cite
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function blockquote($value = null, $cite = null, array $attributes = [])
    {
        $mainAttributes = [];

        if ($cite !== null) {
            $mainAttributes['cite'] = $cite;
        }

        $attributes = array_merge($mainAttributes, $attributes);

        return new static('blockquote', $value, $attributes);
    }

    /**
     * Generate a new "h1" tag
     * 
     * @param string|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function h1($value = null, array $attributes = [])
    {
        return new static('h1', $value, $attributes);
    }

    /**
     * Generate a new "h2" tag
     * 
     * @param string|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function h2($value = null, array $attributes = [])
    {
        return new static('h2', $value, $attributes);
    }

    /**
     * Generate a new "h3" tag
     * 
     * @param string|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function h3($value = null, array $attributes = [])
    {
        return new static('h3', $value, $attributes);
    }

    /**
     * Generate a new "h4" tag
     * 
     * @param string|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function h4($value = null, array $attributes = [])
    {
        return new static('h4', $value, $attributes);
    }

    /**
     * Generate a new "h5" tag
     * 
     * @param string|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function h5($value = null, array $attributes = [])
    {
        return new static('h5', $value, $attributes);
    }

    /**
     * Generate a new "h6" tag
     * 
     * @param string|null $value
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function h6($value = null, array $attributes = [])
    {
        return new static('h6', $value, $attributes);
    }

    /**
     * Generate a new "iframe" tag
     * 
     * @param string|null $value
     * @param string|null $src
     * @param string|null $name
     * @param array $attributes
     * @return \Phower\Html\Tag
     */
    public static function iframe($value = null, $src = null, $name = null, array $attributes = [])
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
     * @param string|null $name
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

}
