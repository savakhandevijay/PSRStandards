<?php

namespace CustomFixers\Rules;

use Exception;
use SplFileInfo;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use CustomFixers\Exceptions\DevChecklistEntryNotFoundException;
use InvalidArgumentException;
use PhpCsFixer\Error;
use PhpCsFixer\Error\ErrorsManager;

class ReleaseChecklistFixer implements FixerInterface
{
    private $wssDevCheckListUrl = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQ1wlbcd3wuOe8lJnj1R60uUVrsuV899INNyOfh1HgJzKbcCeeJqjNmUIh7IlmcH19pNYGTTDFQNOCX/pub?output=csv';

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Custom fixer for release checklist.',
            [new CodeSample("<?php\n\necho CustomFixers\\ReleaseChecklistFixer :: class;\n")]
        );
    }
    // if the fixer changes code behavior in any way, return "true" changing a class name is such case
    public function isRisky(): bool
    {
        return true;
    }
    // it's used to order all fixers before running them `0` by default, higher value is first
    public function getPriority(): int
    {
        return 0;
    }
    // in 99.9% this is true, since only *.php are passed you can detect specific names, e.g. "*Repository.php"
    public function supports(SplFileInfo $file): bool
    {
        return true;
    }
    /**
     * Function validate kind of token if return true, means it fails to validate, this will internally calls
     * fix function and return error from that function.
     *
     * @param Tokens $tokens
     * @return boolean
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return false;
        // return $tokens->isTokenKindFound(T_STRING);
    }

    public function applyfix(\SplFileInfo $file, Tokens $tokens): void
    {
        $caseNumber = 'WSS-10111641';
        $httpErrorCode = 500;
        $errorMessage = sprintf('Missing WSS DEV checklist entry for Jira case %s', $caseNumber);
        if(!ini_set('default_socket_timeout', 20)) {
            echo "<!-- unable to change socket timeout -->";
        }
        try {
            $tickets = $this->getDevChecklistAddedCases();
            if(in_array($caseNumber, $tickets)) {
                echo 'Invalid Case';
            }
        } catch (Exception $e) {
            $e->getMessage();
            throw new DevChecklistEntryNotFoundException($errorMessage, $httpErrorCode);
        }
    }

    private function getDevChecklistAddedCases(): array
    {
        $jiraCases = [];
        if (($handle = fopen($this->wssDevCheckListUrl, 'r')) !== false) {
            $row = 0;
            while ($data = fgetcsv($handle, 1000, ',', '"', '\\')) {
                $row++;
                if ($row == 1) {
                    continue;
                }

                $jiraCases = array_filter($data, function ($element) {
                    if (preg_match('/^WSS-(\d)+/', $element)) {
                        return true;
                    }
                });
                break;
            }
            fclose($handle);
        }
        return $jiraCases;
    }

    public function getName(): string
    {
        return 'CustomFixers/release_checklist';
    }

    public function fix(\SplFileInfo $file, Tokens $tokens): void
    {
        $errorMessage = sprintf('Missing WSS DEV checklist entry for Jira case %s', 'wss-111');
        throw new DevChecklistEntryNotFoundException($errorMessage);
    }
}
