<?php

/**
 * This file is part of the Griston Project (https://github.com/griston).
 *
 * Copyright (c) David Skála (http://www.griston.net)
 *
 * For the full copyright and license information,
 * please view the file LICENSE.md that was distributed with this source code.
 */

namespace Griston\Forms\Controls;

use Nette\Forms\Container;
use Nette\Utils\Html;

/**
 * Url note for form
 *
 * @author David Skála
 * @property string $value
 */
class NoteUrlInput extends \Nette\Forms\Controls\BaseControl {

    const DEFAULT_HREF = 'this';

    /** @var bool */
    private static $registered = FALSE;

    /** @var string */
    private $href;

    /**
     * @param string $href
     * @param string|NULL $label
     */
    public function __construct($href = self::DEFAULT_HREF, $label = NULL) {
        parent::__construct($label);
        $this->href = $href;
    }

    /**
     * @param string|NULL $value
     * @return NoteUrlInput
     */
    public function setValue($value = NULL) {
        if ($value === NULL) {
            return parent::setValue(NULL);
        }

        return parent::setValue($value->format($this->href));
    }

    /**
     * @return \DateTimeImmutable|NULL
     */
    public function getValue() {
        if (!$this->isFilled()) {
            return NULL;
        }

        $datetime = \DateTimeImmutable::createFromFormat($this->format, $this->getRawValue());
        if ($datetime === FALSE || $datetime->format($this->format) !== $this->getRawValue()) {
            return NULL;
        }

        return $datetime->setTime(0, 0, 0);
    }

    /**
     * @return mixed
     */
    public function getRawValue() {
        return parent::getValue();
    }

    public function loadHttpData() {
        $input = $this->getHttpData(\Nette\Forms\Form::DATA_TEXT);
        if (empty($input)) {
            parent::setValue(NULL);
            return;
        }

        parent::setValue($this->href);
    }

    /**
     * @return \Nette\Utils\Html
     */
    public function getControl() {
        $control = Html::el('a');
        $control->href($this->getRawValue());
        $control->text('odkaz');
        return $control;
    }

    /**
     * @param string|bool $message
     * @return NoteUrlInput
     */
    public function setRequired($message = TRUE) {
        return true;
    }

    public static function register() {
        if (static::$registered) {
            throw new \Nette\InvalidStateException('NoteUrl control already registered.');
        }

        static::$registered = TRUE;

        $class = get_called_class();
        $callback = function (
                Container $container,
                $name,
                $label = NULL,
                $format = self::DEFAULT_HREF
                ) use ($class) {
            $control = new $class($format, $label);
            $container->addComponent($control, $name);
            return $control;
        };

        Container::extensionMethod('addNoteUrl', $callback);
    }

}
