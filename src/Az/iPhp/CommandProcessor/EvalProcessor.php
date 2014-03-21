<?php

namespace Az\iPhp\CommandProcessor;

use Az\iPhp\Common\TokenizedInput;
use Az\iPhp\Event\CommandEvent;

class EvalProcessor
{

    public function onInputLine(CommandEvent $event)
    {
        $event->output = $this->execute($event->getTokenizedInput());
        $event->stopPropagation();
    }

    protected function execute(TokenizedInput $input)
    {
        $isDumpable = $this->isDumpable($input);
        $command = $input->getRaw().';';

        ob_start();
        if($isDumpable)
        {
            $command = 'return '.$command;
        }

        $result = eval($command);
        $stdOut = ob_get_clean();

        if(!$stdOut && $isDumpable)
        {
            $dump = null;

            if(is_array($result))
            {
                if($this->isAssoc($result))
                {
                    $dump = json_encode($result, JSON_FORCE_OBJECT);
                } else {
                    $dump = json_encode($result);
                }
            }
            elseif(is_object($result) && !get_class_methods($result))
            {
                $dump = json_encode($result, JSON_FORCE_OBJECT);
            }
            else
            {
                ob_start();
                var_dump($result);
                $dump = ob_get_clean();
            }

            return $dump;
        }

        return $stdOut;
    }

    protected function isDumpable(TokenizedInput $input)
    {
        $tokens = $input->getTokens();

        if(count($tokens) === 1 && is_array($tokens[0]) && $tokens[0][0] === T_WHITESPACE)
        {
            return false;
        }

        $isDumpable = true;
        $lastChance = false;
        foreach($tokens as $token)
        {
            if($lastChance)
            {
                if(is_scalar($token)) $lastChance = in_array($token,    [' ', "\n",]);
                else                  $lastChance = in_array($token[0], [T_WHITESPACE,]);
            }
            else if($isDumpable)
            {
                if(is_scalar($token))
                {
                    $isDumpable = in_array($token, [
                            ' ', "\n",
                            '.', ',',
                            '+', '-', '/', '*', '%',
                            '^', '|', '&',
                            '?', ':',
                            '@',
                            '!', '&&', '||',
                            '<', '<=', '>', '>=', '==', '===',
                            '[', ']',  '(', ')',
                            '->', '<<', '>>'
                        ]);
                } else {
                    $isDumpable = in_array($token[0], [
                            T_OBJECT_OPERATOR,
                            T_SL, T_SR,

                            T_DNUMBER, T_STRING, T_LNUMBER,
                            T_ENCAPSED_AND_WHITESPACE, T_CONSTANT_ENCAPSED_STRING, T_NUM_STRING, T_STRING_VARNAME,

                            T_VARIABLE,

                            T_DOLLAR_OPEN_CURLY_BRACES, T_CURLY_OPEN,
                            T_WHITESPACE,

                            T_ARRAY,
                            T_ARRAY_CAST, T_BOOL_CAST, T_INT_CAST, T_OBJECT_CAST, T_STRING_CAST,
                            T_BOOLEAN_AND, T_BOOLEAN_OR,
                            T_IS_EQUAL, T_IS_NOT_EQUAL, T_IS_GREATER_OR_EQUAL, T_IS_IDENTICAL, T_IS_NOT_IDENTICAL, T_IS_SMALLER_OR_EQUAL,
                            T_CHARACTER, T_CLONE, T_COMMENT,
                            T_NEW,
                            T_INSTANCEOF,
                            T_ISSET,
                            T_DOUBLE_ARROW,
                            T_DOUBLE_COLON,
                        ]);
                }

                if(!$isDumpable) $lastChance = true;
            }

            if(!$lastChance && !$isDumpable) break;
        }

        return $lastChance || $isDumpable;
    }

    private function isAssoc($array) {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }

} 