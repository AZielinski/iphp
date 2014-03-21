<?php

namespace Az\iPhp\Common;

class TokenizedInput
{

    protected $raw;
    protected $tokens;

    public function __construct($raw)
    {
        $this->raw     = $raw;
        $this->tokens  = array_slice(token_get_all('<?php '.$raw), 1);
    }

    /**
     * @return mixed
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    public function endsWithTokens(array $allowedTokens, array $stop = [';'], $stopMandatory = false)
    {
        for($i=count($this->tokens)-1;$i>=0;$i--)
        {
            $token = $this->tokens[$i];
            if(is_array($token))
            {
                $token = $token[0];
            }

            if(in_array($token, $stop, true))
            {
                return true;
            }

            if(!in_array($token, $allowedTokens, true))
            {
                // echo("TOKEN: ".$token); die(token_name($token));
                return false;
            }
        }

        return $stopMandatory;
    }

}
