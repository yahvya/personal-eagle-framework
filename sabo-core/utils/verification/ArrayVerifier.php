<?php

namespace SaboCore\Utils\Verification;

/**
 * @brief array verifier util
 */
readonly class ArrayVerifier{
    /**
     * @param array $toVerify array to verify
     */
    public function __construct(public array $toVerify) {
    }

    /**
     * @brief verify the presence of each given keys. you can verify sub elements by using the level separator ["level-1" => "level-2" => "value"] "level-1.level-2"
     * @param string[] $keys keys list
     * @param string $levelSeparator level separator
     * @return bool if each given keys are present
     */
    public function verifyKeys(array $keys,string $levelSeparator = "."):bool{
        foreach($keys as $key){
            # getting levels
            $parts = explode(separator: $levelSeparator,string: $key);
            $toVerify = $this->toVerify;

            foreach($parts as $keyPart){
                # verify the current level
                if(!array_key_exists(key: $keyPart,array: $toVerify))
                    return false;

                # switching to the next level
                $toVerify = $toVerify[$keyPart];
            }
        }

        return true;
    }
}