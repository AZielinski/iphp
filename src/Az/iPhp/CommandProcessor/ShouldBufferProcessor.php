<?php

namespace Az\iPhp\CommandProcessor;

use Az\iPhp\Common\TokenizedInput;
use Az\iPhp\Event\CommandEvent;

class ShouldBufferProcessor
{

    public function onInputLine(CommandEvent $event)
    {
        $event->shouldBuffer = !$this->isInputComplete($event->getTokenizedInput());
        if($event->shouldBuffer)
        {
            $event->stopPropagation();
        }
    }

    protected function isInputComplete(TokenizedInput $input)
    {
        $tokens = $input->getTokens();

        $open = [
            'parenthesis' => 0,
            'curlyBrace'  => 0,
            'squareBrace' => 0,
            'singleQuote' => 0,
            'doubleQuote' => 0,
        ];

        foreach($tokens as $tok)
        {
            switch($tok)
            {
                case '(': ++$open['parenthesis']; break;
                case ')': --$open['parenthesis']; break;

                case '{': ++$open['curlyBrace'];  break;
                case '}': --$open['curlyBrace'];  break;

                case '[': ++$open['squareBrace']; break;
                case ']': --$open['squareBrace']; break;

                case '"': ++$open['doubleQuote']; break;
                case "'": --$open['singleQuote']; break;
            }
        }

        $inlineCurly = 0;
        $flowControlStack = [];

        $isControlStructureActive = false;
        $controlStructureDeclarationFinished = false;
        $controlStructureDeclarationFinishedJustNow = false;
        foreach($tokens as $tok)
        {
            if(is_array($tok) && in_array($tok[0], [
                        T_COMMENT, T_WHITESPACE
                    ]))
            {
                continue;
            }

            if(is_array($tok) && in_array($tok[0], [
                        T_DO, T_IF, T_WHILE, T_FOR, T_FOREACH, T_SWITCH
                    ], true))
            {
                $isControlStructureActive = true;
                $controlStructureDeclarationFinished = false;
                $controlStructureDeclarationFinishedJustNow = false;
                array_push($flowControlStack, [
                        'type'   => $tok[0],
                        'inline' => true
                    ]);
                continue;
            }

            if($isControlStructureActive && !$controlStructureDeclarationFinished)
            {
                $controlStructureDeclarationFinished = $tok === ')';
                $controlStructureDeclarationFinishedJustNow = true;
                continue;
            }

            if($controlStructureDeclarationFinishedJustNow)
            {
                $inlineCurly = 0;
                $controlStructureDeclarationFinishedJustNow = false;
                if(is_scalar($tok) && $tok === '{')
                {
                    $flowControlStack[count($flowControlStack)-1]['inline'] = false;
                }

                continue;

                /*
                if(is_array($tok) && in_array($tok[0], [
                    T_VARIABLE,

                    T_DNUMBER, T_STRING, T_LNUMBER,
                    T_ENCAPSED_AND_WHITESPACE, T_CONSTANT_ENCAPSED_STRING, T_NUM_STRING, T_STRING_VARNAME,

                    T_RETURN, T_BREAK, T_CONTINUE,
                ]))
                {
                    $isControlStructureActive = false;
                }
                */
            }

            if(is_scalar($tok) && $tok === '{')
            {
                ++$inlineCurly;
            }

            if(is_scalar($tok) && $tok === ';')
            {
                for($i=count($flowControlStack)-1;$i>=0;$i--)
                {
                    if($flowControlStack[$i]['inline'])
                    {
                        unset($flowControlStack[$i]);
                    }
                    else
                    {
                        break;
                    }
                }
                $flowControlStack = array_values($flowControlStack);
                continue;
            }

            if(is_scalar($tok) && $tok === '}')
            {
                if($inlineCurly)
                {
                    --$inlineCurly;
                    continue;
                }

                $count = count($flowControlStack);
                if($count)
                {
                    unset($flowControlStack[$count-1]);
                    $flowControlStack = array_values($flowControlStack);

                    for($i=count($flowControlStack)-1;$i>=0;$i--)
                    {
                        if($flowControlStack[$i]['inline'])
                        {
                            unset($flowControlStack[$i]);
                        }
                        else
                        {
                            break;
                        }
                    }
                    $flowControlStack = array_values($flowControlStack);
                }
            }

            // T_FUNCTION, T_CLASS, T_PUBLIC, T_PROTECTED, T_PRIVATE
        }

        $count = count($flowControlStack);
        if($count > 0)
        {
            return $count;
        }

        if(array_sum($open) !== 0) return false;

        for($i=count($tokens)-1;$i>=0;$i--)
        {
            $tok = $tokens[$i];
            if(is_array($tok) && in_array($tok[0], [
                        T_COMMENT, T_WHITESPACE, T_STRING
                    ]))
            {
                continue;
            }

            if(is_array($tok) && in_array($tok[0], [
                        T_FUNCTION,
                        T_CLASS, T_IMPLEMENTS, T_EXTENDS
                    ]))
            {
                return false;
            }

            break;
        }

        return true;
    }

}
