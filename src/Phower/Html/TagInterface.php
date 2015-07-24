<?php

namespace Phower\Html;

use Phower\Escaper\EscaperInterface;

interface TagInterface
{

    /**
     * Get name
     * 
     * @return string
     */
    public function getName();

    /**
     * Set value
     * 
     * @param string|TagInterface $value
     * @return self
     */
    public function setValue($value);

    /**
     * Get value
     * 
     * @return string|TagInterface
     */
    public function getValue();

    /**
     * Set attributes
     * 
     * @param array $attributes
     * @return self
     */
    public function setAttributes(array $attributes);

    /**
     * Get attributes
     * 
     * @return array
     */
    public function getAttributes();

    /**
     * Add attribute
     * 
     * @param string $name
     * @param string $value
     * @return self
     */
    public function addAttribute($name, $value);

    /**
     * Add attributes
     * 
     * @param array $attributes
     * @return self
     */
    public function addAttributes(array $attributes);

    /**
     * Has attribute
     * 
     * @param string $name
     * @return boolean
     */
    public function hasAttribute($name);

    /**
     * Get attribute
     * 
     * @param string $name
     * @return string
     */
    public function getAttribute($name);

    /**
     * Set attribute
     * 
     * @param string $name
     * @param string $value
     * @return self
     */
    public function setAttribute($name, $value);

    /**
     * Escape
     * 
     * @param boolean|null $escape
     * @return self|boolean
     */
    public function escape($escape = null);

    /**
     * Set escaper
     * 
     * @param \Phower\Html\EscaperInterface $escaper
     * @return self
     */
    public function setEscaper(EscaperInterface $escaper);

    /**
     * Get escaper
     * 
     * @return \Phower\Html\EscaperInterface|null
     */
    public function getEscaper();

    /**
     * Open tag
     * 
     * @return string
     */
    public function openTag();

    /**
     * Close tag
     * 
     * @return string
     */
    public function closeTag();

    /**
     * Render
     * 
     * @return string
     */
    public function render();
}
