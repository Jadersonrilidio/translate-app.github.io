<?php

/* class renamed for easier understanding */
class TranslationYandex {
    
    /* constant not used (may delete) */
    const DETECT_YA_URL = 'https://translate.yandex.net/api/v1.5/tr.json/detect';
    
    const TRANSLATE_YA_URL = 'https://translate.yandex.net/api/v1.5/tr.json/translate';
    
    /* variable set to private due to security issues (the valid API key is exposed but for Heroku page correct working purposes) */
    private $key = 'trnsl.1.1.20220202T171040Z.c73730c1db5f436c.49416f63010117766dd7c4985599617a2f9c3a44';

    public function __construct() {}

    /* function set to private, as no need for public use (only used inside instance method) */
    private function init() {
        /* parent::init() missused, deleted */
        if ( empty($this->key) ) {
            /* InvalidConfigException misspeled, changed */
            throw new InvalidArgumentException('Field <b> key </b> is required.'); /* string $key with wrong sintax, use of \$ to convert variable sign to string on output (or only use single quotation) */
        } 
    }

    /**
     * @param $format text format need to translate;
     * @return string
     */
    /* static modifier not needed (therefore, we need a construct method)... if so, the called methods inside this one must be also static */
    private function translate_text($format='text') { 
        $this->init(); /* method duplication fixed */

        $values = array(
            'key' => $this->key,
            'text' => $_GET['text_from'], /* better use $_POST['text'] instead of GET requests (enhanced security), however it is limited by 10000 characters, according to yandex text translate API docs */
            'lang' => $_GET['lang_from'].'-'.$_GET['lang_to'], 
            'format' => ($format=='text') ? 'plain' : $format
        );

        $formData = http_build_query($values);

        /* renamed object $ch for ease code understanding */
        $curlHandle = curl_init(self::TRANSLATE_YA_URL);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $formData);
       
        $json = curl_exec($curlHandle);
        curl_close($curlHandle);

        $data = json_decode($json, true);

        /* return condition refactored */
        return ($data['code']==200) ? $data['text'] : $data;
    }

    /**
     * @author method used for evaluate the submit button condition before run the translate_text method;
     */
    public function echo_text() {
        if (isset($_GET['submit'])) {
            echo $this->translate_text()[0];
        }
    }
}

?>