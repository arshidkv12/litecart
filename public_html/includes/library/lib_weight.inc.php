<?php

  class weight {

    public static $classes = array(
      'kg' => array(
        'name' => 'Kilograms',
        'unit' => 'kg',
        'value' => 1,
        'decimals' => 2,
      ),
      'g' => array(
        'name' => 'Grams',
        'unit' => 'g',
        'value' => 1000,
        'decimals' => 0,
      ),
      'dwt' => array(
        'name' => 'Pennyweights',
        'unit' => 'dwt',
        'value' => 643.01493137256,
        'decimals' => 0,
      ),
      'lb' => array(
        'name' => 'Pounds',
        'unit' => 'lb',
        'value' => 2.2046,
        'decimals' => 2,
      ),
      'oz' => array(
        'name' => 'Ounces',
        'unit' => 'oz',
        'value' => 35.274,
        'decimals' => 1,
      ),
      'st' => array(
        'name' => 'Stones',
        'unit' => 'st',
        'value' => 0.1575,
        'decimals' => 2,
      ),
    );

    public static function convert($value, $from, $to) {

      if ($value == 0) return 0;

      if ($from == $to) return (float)$value;

      if (!isset(self::$classes[$from])) {
        trigger_error('The unit '. $from .' is not a valid weight class.', E_USER_WARNING);
        return;
      }

      if (!isset(self::$classes[$to])) {
        trigger_error('The unit '. $to .' is not a valid weight class.', E_USER_WARNING);
        return;
      }

      if (self::$classes[$from]['value'] == 0 || self::$classes[$to]['value'] == 0) return;

      return $value * (self::$classes[$to]['value'] / self::$classes[$from]['value']);
    }

    public static function format($value, $class) {

      if (!isset(self::$classes[$class])) {
        trigger_error('Invalid weight class ('. $class .')', E_USER_WARNING);
        return;
      }

      $decimals = self::$classes[$class]['decimals'];
      $formatted = rtrim(rtrim(number_format((float)$value, (int)$decimals, language::$selected['decimal_point'], language::$selected['thousands_sep']), '0'), language::$selected['decimal_point']);

      return $formatted .' '. self::$classes[$class]['unit'];
    }
  }
