<?php

namespace CustomFixers\Fixer;

use PhpCsFixer\AbstractFixer as PhpCsFixerAbstractFixer;

/**
 * undocumented class
 */
abstract class AbstractFixer extends PhpCsFixerAbstractFixer
{
    /**
     * Custom class name must have `Fixers` as suffix otherwise it will be invalide name
     *
     * @return string
     */
    public function getName(): string
    {
        return sprintf('CustomFixers/%s', parent::getName());
    }
}
