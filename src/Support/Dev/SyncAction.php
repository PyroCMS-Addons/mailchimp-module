<?php namespace Thrive\MailchimpModule\Support\Dev;

/**
 * https://stackoverflow.com/questions/254514/enumerations-on-php
 *  new SyncAction() will create the default Enum
 *  php 8x will include enums but for now we have this
 */
abstract class SyncAction
{

	private static $constCacheArray = NULL;

		
    const ErrResolveSuggestPull     =  'ErrResolveSuggestPull';
    const ErrResolveSuggestPush     =  'ErrResolveSuggestPush';
    const Pull                      =  'Pull';
    const Push                      =  'Push';
    const NoChange                  =  'NoChange';
    const ErrResolveNoSuggestion    =  'ErrResolveNoSuggestion';


	/**

	 */
    private static function getConstants() {
        if (self::$constCacheArray == NULL) {
            self::$constCacheArray = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }


    public static function isValidName($name, $strict = false) {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }	

	public static function isValidValue($value, $strict = true) {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict);
    }

}