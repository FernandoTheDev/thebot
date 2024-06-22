<?php

include(__DIR__ . "/Validate.php");

/**
 * Extrar y validar las CCs
 */
abstract class Card {
    public static $valid_card;
    public static $card;
    public static $luhn;
    public static $regex = "/(\d{15,16})+?[^0-9]+?(\d{1,2})[\D]*?(\d{2,4})[^0-9]+?(\d{3,4})/";

    /**
     * Extraer y validar las CCs, devuelve new[ok]false en caso de que no haya encontrado ninguna CC valida
     *
     * @param string $cards
     * @return array
     */
    public static function SetCard(string $cards) {
        $new = array('cc' => array('valid' => [], 'invalid' => []), 'ok' => false, 'data' => []);
        $cards = self::ExtractCard($cards);

        $total = count($cards);

        for($i = 0; $i < $total; $i++) {
            $card = explode('|', $cards[$i]);
            $val = self::Validate_Struct($card);
            if($val['ok']) {
                $new['cc']['valid'][] = ['reason' => $val, 'cc' => $card, 'ok' => $val['ok']];
            } else {
                $new['cc']['invalid'][] = ['reason' => $val, 'cc' => $card, 'ok' => $val['ok']];
            }
        }

        $new['ok'] = $total != 0;
        $new['data'] = ['all' => $total, 'valid' => count($new['cc']['valid']), 'invalid' => count($new['cc']['invalid'])];


        if(count($new['cc']['valid']) > 0) {
            self::$valid_card = $new['cc']['valid'][0]['cc'];
        }

        return $new;
    }

    /**
     * Extrae todas las CC's de un string
     *
     * @param string $input String con las CC's
     * @return array
     */
    public static function ExtractCard(string $input) {
        if(file_exists($input)) {
            $input = file_get_contents($input);
        }

        if(preg_match_all(self::$regex, $input, $cards)) {

            $total = count($cards[0]);
            $ccs = array();

            for($i = 0; $i < $total; $i++) {
                $cards[2][$i] = (strlen($cards[2][$i]) == 1) ? '0'.$cards[2][$i] : $cards[2][$i];
                $cards[3][$i] = (strlen($cards[3][$i]) == 2) ? '20'.$cards[3][$i] : $cards[3][$i];
                $ccs[] = $cards[1][$i].'|'.$cards[2][$i].'|'.$cards[3][$i].'|'.$cards[4][$i];
            }
            $ccs = array_unique($ccs);
            $cece = array();
            foreach($ccs as $item) {
                $cece[] = $item;
            }
            return $cece;
        } else {
            return [];
        }
    }

    /**
     * Validar el luhn, cvv, y date de una CC
     * @param array $cc CC a validar
     * @return array
     */
    private static function Validate_Struct(array $cc) {
        $card = $cc[0];
        $first = $card[0];

        if(in_array($first, [3, 4, 5, 6]) === false) {
            return ['ok' => false, 'msg' => 'Only Amex, Visa, Mastercard or Discover is acept']; // Invalid card
        }

        // Validar la expiración
        $expr = Validate::ValidateExpired($cc[1], $cc[2]);
        if(!$expr['ok']) {
            return $expr;
        }

        // Validar el luhn
        if(!Validate::luhn($card)) {
            return ['ok' => false, 'msg' => 'Invalid Luhn'];
        }

        // Validar el cvv
        $cv = Validate::Cvv($cc[3], $card);
        if(!$cv['ok']) {
            return $cv;
        }

        // Valida la longitud
        $le = Validate::CardLenght($card);
        if(!$le['ok']) {
            return $le;
        }

        return ['ok' => true, 'msg' => null];
    }

    public static function CartType(string $card) {
        $cardNames = array(
            "3" => "American Express",
            "4" => "Visa",
            "5" => "MasterCard",
            "6" => "Discover"
         );
         return $cardNames[substr($card, 0, 1)];
    }

    public static function GetCard() {
        return self::$valid_card;
    }

    public static function getParsedCard() {
        return implode('|', self::$valid_card);
    }
}
