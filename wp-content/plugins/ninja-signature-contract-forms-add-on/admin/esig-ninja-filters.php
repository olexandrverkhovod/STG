<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!class_exists('esigNinjaFilters')):

    class esigNinjaFilters {

        protected static $instance = null;

        private function __construct() {
            add_filter("esig_document_title_filter", array($this, "ninja_document_title_filter"), 10, 2);
            add_filter("esig_strip_shortcodes_tagnames", array($this, "tag_list_filter"), 10, 1);
        }

        public function tag_list_filter($listArray) {
            $listArray[] = "ninja_form";
            return $listArray;
        }

        public function ninja_document_title_filter($docTitle, $docId) {
            $formIntegration = WP_E_Sig()->document->getFormIntegration($docId);
            if ($formIntegration != "ninja") {
                return $docTitle;
            }

            preg_match_all('/{{+(.*?)}}/', $docTitle, $matchesAll);

            if (empty($matchesAll[1])) {
                return $docTitle;
            }
            if (!is_array($matchesAll[1])) {
                return $docTitle;
            }

            $titleResult = $matchesAll[1];
            foreach ($titleResult as $result) {

                preg_match_all('!\d+!', $result, $matches);
                if (empty($matches[0])) {
                    continue;
                }
                $fieldId = is_array($matches) ? $matches[0][0] : false;
                if (is_numeric($fieldId)) {
                    $nfValue = ESIG_NF_SETTING::get_value($docId, $fieldId,"value","default");
                    $docTitle = str_replace("{{ninja-field-id-" . $fieldId . "}}", $nfValue, $docTitle);   
                }
            }

            return $docTitle;

           
        }

        /**
         * Return an instance of this class.
         * @since     0.1
         * @return    object    A single instance of this class.
         */
        public static function instance() {

            // If the single instance hasn't been set, set it now.
            if (null == self::$instance) {
                self::$instance = new self;
            }

            return self::$instance;
        }

    }

    

    

    

    
endif;
