<?php if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Plugin Name: Ninja Forms - Excel Export
 * Plugin URI: http://etzelstorfer.com/en/
 * Description: Export Ninja Forms submissions to Excel file
 * Version: 3.3.1
 * Author: Hannes Etzelstorfer
 * Author URI: http://etzelstorfer.com/en/
 * Text Domain: ninja-forms-excel-export
 *
 * Copyright 2018 Hannes Etzelstorfer.
 */


if( version_compare( get_option( 'ninja_forms_version', '0.0.0' ), '3.0', '<' ) || get_option( 'ninja_forms_load_deprecated', FALSE ) ) {
    include 'deprecated/ninja-forms-excel-export.php';

} else {

    /**
     * Class NF_ExcelExport
     */
    final class NF_ExcelExport
    {
        const VERSION = '3.3.1';
        const SLUG    = 'excel-export';
        const NAME    = 'Excel Export';
        const AUTHOR  = 'Hannes Etzelstorfer';
        const PREFIX  = 'NF_ExcelExport';

        /**
         * @var NF_ExcelExport
         * @since 3.0
         */
        private static $instance;

        /**
         * Plugin Directory
         *
         * @since 3.0
         * @var string $dir
         */
        public static $dir = '';

        /**
         * Plugin URL
         *
         * @since 3.0
         * @var string $url
         */
        public static $url = '';

        private $subs_per_page = 200;

        /**
         * Main Plugin Instance
         *
         * Insures that only one instance of a plugin class exists in memory at any one
         * time. Also prevents needing to define globals all over the place.
         *
         * @since 3.0
         * @static
         * @static var array $instance
         * @return NF_ExcelExport Highlander Instance
         */
        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof NF_ExcelExport)) {
                self::$instance = new NF_ExcelExport();

                self::$dir = plugin_dir_path(__FILE__);

                self::$url = plugin_dir_url(__FILE__);

                /*
                 * Register our autoloader
                 */
                spl_autoload_register(array(self::$instance, 'autoloader'));
            }
        }

        public function __construct()
        {
            /*
             * Required for all Extensions.
             */
            add_action( 'admin_init', array( $this, 'setup_license') );

            //require self::$dir . 'includes/class-ninjaformsspreadsheet.php';
            load_plugin_textdomain('ninja-forms-spreadsheet', false, self::$dir . '/translations' );

            add_action( 'admin_menu', array( $this, 'add_admin_page'));
            add_action( 'wp_ajax_nf_spreadsheet_export', array($this,'export_file') );
            add_action( 'wp_ajax_nf_spreadsheet_save_field_settings', array($this,'save_field_settings') );
            add_action( 'wp_ajax_nf_spreadsheet_save_filter', array($this,'save_filter') );
            add_action( 'admin_init', array( $this, 'output_export_file' ));
        }




        public function add_admin_page(){
            if( function_exists('Ninja_Forms') )
                Ninja_Forms()->menus[ 'excel-export' ]         = new NF_ExcelExport_Admin_Menus_ExcelExport();
        }
        

        /*
         * Optional methods for convenience.
         */

        public function autoloader($class_name)
        {

            if (class_exists($class_name)) return;

            if ( false === strpos( $class_name, self::PREFIX ) ) return;

            $class_name = str_replace( self::PREFIX, '', $class_name );
            $classes_dir = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR;
            $class_file = str_replace('_', DIRECTORY_SEPARATOR, $class_name) . '.php';

            if (file_exists($classes_dir . $class_file)) {
                require_once $classes_dir . $class_file;
            }
        }




        public function save_field_settings(){
            $form_id = $_POST['form_id'];
            $field_settings = $_POST['field_settings'];
            $fields_associative = array();
            foreach ($field_settings as $field) {
                $fields_associative[ $field['field_key'] ] = $field;
            }

            update_option( 'nf_excel_field_settings_' . $form_id, $fields_associative );
            wp_die();
        }




        public function save_filter(){
            if( array_key_exists('form_id', $_POST) && array_key_exists('filter', $_POST) ){
                $form_id = $_POST['form_id'];
                $filters = $_POST['filter'];

                update_option( 'nf_excel_filter_' . $form_id, $filters );
            }
            wp_die();
        }
                



        public function export_file(){
            $form_id = $_POST['spreadsheet_export_form_id'];
            $field_ids_raw = $_POST['spreadsheet_export_field_ids'];
            $filters = json_decode( stripslashes( $_POST['spreadsheet_export_filter'] ) );
            $use_xls = false;

            if( $_POST['spreadsheet_export_file_format'] == 'xls' )
                $use_xls = true;

            if( $use_xls )
                $tmp_file = 'form-submissions'.$_POST['spreadsheet_export_tmp_name'].'.xls';
            else
                $tmp_file = 'form-submissions'.$_POST['spreadsheet_export_tmp_name'].'.xlsx';

            $tmp_file = trailingslashit( get_temp_dir() ).$tmp_file;

            $iteration = $_POST['spreadsheet_export_iteration'];

            $sub_results = $this->get_submissions($form_id,$filters,$iteration);

            $num_submissions = $this->count_submissions($form_id,$filters);

            $fields_meta = Ninja_Forms()->form($form_id)->get_fields();
            
            $selected_fields = array();
            foreach($field_ids_raw AS $key => $active){
                if($active == 1)
                    $selected_fields[] = $key;
            }

            $field_names=array();

            $header_row=array();
            $header_row['id'] = __('ID','ninja-forms-spreadsheet');
            $header_row['date_submitted'] = __('Submission date','ninja-forms-spreadsheet');
            $selected_field_names = array();
            foreach($fields_meta as $field_id => $field){
                $field_settings = $field->get_settings();
                $field_names[$field_id] = sanitize_title( $field_settings['label'].'-'.$field_id );
                $field_types[$field_id] = $field_settings['type'];
                if(in_array($field_id, $selected_fields)){
                    $selected_field_names[$field_id]=(isset($field_settings['admin_label']) && $field_settings['admin_label'] ? $field_settings['admin_label'] : $field_settings['label']);
                }
            }

            foreach ($selected_fields as $key) {
                $header_row[$key] = $selected_field_names[$key];
            }
            
            
            require_once(self::$dir.'includes/PHPExcel.php');

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;
            if (!PHPExcel_Settings::setCacheStorageMethod($cacheMethod)) {
                die($cacheMethod . " caching method is not available" . EOL);
            }
            
            if( $iteration > 0 ){
                $objPHPExcel = PHPExcel_IOFactory::load($tmp_file);
                $row_number = $this->subs_per_page * $iteration+1;
            }else{
                $objPHPExcel = new PHPExcel(); 
                $row_number = 1;
            }

            // this should be the same wordpress uses and therefore be the saver choice
            PHPExcel_Shared_File::setUseUploadTempDirectory( true );

            if( $use_xls )
                $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
            else
                $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            
            // Table Headline
            if( $iteration==0 ){
                $col_index = 0;
                foreach ($header_row as $headline) {
                    $col = PHPExcel_Cell::stringFromColumnIndex( $col_index );
                    $objPHPExcel->getActiveSheet()->getCell($col.$row_number)->setValue( $headline );
                    $objPHPExcel->getActiveSheet()->getStyle($col.$row_number)->getFont()->setBold(true);
                    $col_index++;
                }
                $objPHPExcel->getActiveSheet()->freezePane( "A2" );
            }
            $row_number++; 

            foreach($sub_results as $sub){
                $col = 'A';
                $objPHPExcel->getActiveSheet()->getCell($col++.$row_number)->setValueExplicit( $sub['_seq_num'], PHPExcel_Cell_DataType::TYPE_NUMERIC );
                $objPHPExcel->getActiveSheet()->getCell($col++.$row_number)->setValueExplicit( $sub['date_submitted'] );
                foreach ($selected_fields as $col_index => $field_id) {
                    $field_key = '_field_'.$field_id;
                    if( isset($sub[$field_key]) ){
                        $field_value = $sub[$field_key];
                        $col = PHPExcel_Cell::stringFromColumnIndex( 2+$col_index );

                        $field_value = maybe_unserialize( $field_value );

                        $field_value = apply_filters('nf_subs_export_pre_value', $field_value, $field_id);
                        $field_value = apply_filters('ninja_forms_subs_export_pre_value', $field_value, $field_id, $form_id);
                        $field_value = apply_filters('ninja_forms_subs_export_field_value_' . $field_types[$field_id], $field_value, $fields_meta[$field_id] );

                        if( in_array($field_types[$field_id], array( 'shipping', 'total', 'number' ) ) ){ // float values
                            $objPHPExcel->getActiveSheet()->getCell($col.$row_number) ->setValueExplicit( $field_value, PHPExcel_Cell_DataType::TYPE_NUMERIC );
                            $objPHPExcel->getActiveSheet()->getStyle($col.$row_number)
                                ->getNumberFormat()
                                ->setFormatCode('#,##');
                        }elseif( in_array($field_types[$field_id], array( 'starrating', 'quantity' ) ) ){ // integer values
                            $objPHPExcel->getActiveSheet()->getCell($col.$row_number) ->setValueExplicit( $field_value, PHPExcel_Cell_DataType::TYPE_NUMERIC );
                            $objPHPExcel->getActiveSheet()->getStyle($col.$row_number)
                                ->getNumberFormat()
                                ->setFormatCode('#');
                        }elseif( in_array($field_types[$field_id], array( 'checkbox' ) ) || strpos($field_types[$field_id], '-optin') ){
                            $objPHPExcel->getActiveSheet()->getCell($col.$row_number)->setValueExplicit( $field_value, PHPExcel_Cell_DataType::TYPE_STRING );
                        }elseif( in_array($field_types[$field_id], array( 'listcheckbox' ) ) ){
                            $field_output = $field_value;
                            if( is_array($field_value) ){
                                $field_output = '';
                                foreach ($field_value as $key => $value) {
                                    if( $field_output == '' )
                                        $field_output = $value;
                                    else
                                        $field_output .= ', ' . $value;
                                }
                            }
                            $objPHPExcel->getActiveSheet()->getCell($col.$row_number)->setValueExplicit( htmlspecialchars_decode ( $field_output,ENT_QUOTES ), PHPExcel_Cell_DataType::TYPE_STRING );
                        }elseif( $field_types[$field_id] == 'file_upload' ){
                            if( is_array($field_value)){
                                $field_value = implode("\n", $field_value);
                            }
                            
                            $objPHPExcel->getActiveSheet()->getCell($col.$row_number)->setValueExplicit( htmlspecialchars_decode ( $field_value,ENT_QUOTES ), PHPExcel_Cell_DataType::TYPE_STRING );
                            $objPHPExcel->getActiveSheet()->getStyle($col.$row_number)
                                ->getAlignment()
                                ->setWrapText(true);
                        }elseif( $field_types[$field_id] == 'textarea' ){
                            $objPHPExcel->getActiveSheet()->getCell($col.$row_number)->setValueExplicit( htmlspecialchars_decode ( $field_value,ENT_QUOTES ), PHPExcel_Cell_DataType::TYPE_STRING );
                            $objPHPExcel->getActiveSheet()->getStyle($col.$row_number)
                                ->getAlignment()
                                ->setWrapText(true);
                        }else{
                            if( is_array($field_value) ){
                                $field_value = implode('; ', $field_value);
                            }
                            $objPHPExcel->getActiveSheet()->getCell($col.$row_number)->setValueExplicit( htmlspecialchars_decode ( $field_value,ENT_QUOTES ), PHPExcel_Cell_DataType::TYPE_STRING );
                        }
                    }
                }
                $row_number++;
            }

            if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ){
                echo json_encode(array(
                        'iteration'     => intval( $iteration ),
                        'num_iterations' => ceil( $num_submissions/$this->subs_per_page ),
                        //'debug' => 'Iteration: ' . $iteration . ' #Results (this iteration): ' . count($sub_results) . ' Results (total): ' . $num_submissions
                    )
                );
                $objWriter->save($tmp_file);
            }else{
                //echo 'Iteration: ' . $iteration . ' #Results (this iteration): ' . count($sub_results) . ' Results (total): ' . $num_submissions;
                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                foreach ($selected_fields as $col_index => $field_key) {
                    $col = PHPExcel_Cell::stringFromColumnIndex( 2+$col_index );
                    $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
                }
                if (headers_sent()) 
                    ob_clean(); // clean output buffer to catch any notices and warnings sent before (by other plugins)

                $output_file_name = sanitize_title( Ninja_Forms()->form($form_id)->get()->get_setting( 'title' ) ) . '_' . date('Y-m-d_His');
                if( $use_xls ){
                    header('Content-Type: application/vnd.ms-excel');
                    header('Content-Disposition: attachment;filename="' . $output_file_name . '.xls"');
                }else{
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="' . $output_file_name . '.xlsx"');
                }
                header('Cache-Control: max-age=0');
                $objWriter->save("php://output");
            }
            
            die();
            
        }




        public function output_export_file(){
            if( ! current_user_can( apply_filters( 'ninja_forms_admin_excel_export_capabilities', 'manage_options' ) ) ) return;

            if( isset($_POST['spreadsheet_export_tmp_name']) ){
                $this->export_file();
                die;
            }
        }





        private function get_submissions($form_id,$filters,$iteration){
            $query_args = array(
                'post_type'         => 'nf_sub',
                'posts_per_page'    => $this->subs_per_page,
                'offset'            => $this->subs_per_page * $iteration,
                'date_query'        => array(
                    'inclusive'     => true,
                ),
                'meta_query'        => array(
                    array(
                        'key' => '_form_id',
                        'value' => $form_id,
                    )
                )
            );

            if( $filters ){
                $query_args = $this->apply_query_filters( $query_args, $filters );   
            }

            $subs = new WP_Query( $query_args );
            // echo $subs->request;
            $sub_objects = array();
            $sub_index = 0;

            if ( is_array( $subs->posts ) && ! empty( $subs->posts ) ) {
                foreach ( $subs->posts as $sub ) {
                    $sub_objects[$sub_index] = Ninja_Forms()->form( $form_id )->get_sub( $sub->ID )->get_field_values();
                    $sub_objects[$sub_index]['date_submitted'] = get_the_date('', $sub->ID );
                    $sub_index++;
                }           
            }

            return $sub_objects;
        }


        private function count_submissions($form_id,$filters){
            $query_args = array(
                'post_type'         => 'nf_sub',
                'posts_per_page'    => -1,
                'date_query'        => array(
                    'inclusive'     => true,
                ),
                'meta_query'        => array(
                    array(
                        'key' => '_form_id',
                        'value' => $form_id,
                    )
                ),
                'fields' => 'ids'
            );

            if( $filters ){
                $query_args = $this->apply_query_filters( $query_args, $filters );   
            }


            $subs = new WP_Query( $query_args );;
            $num_submissions = $subs->found_posts;
            wp_reset_postdata();
            return $num_submissions;
        }

    

        private function apply_query_filters( $query_args, $filters ){
            foreach ($filters as $filter) {
                if( $filter->field_key == 'submission_date' ){
                    $date = $filter->value;
                    if( $filter->condition == 'GT' )
                        $query_args['date_query']['after'] = $date . ' 23:59:59';
                    elseif( $filter->condition == 'GE' )
                        $query_args['date_query']['after'] = $date . ' 00:00:00';
                    elseif( $filter->condition == 'LT' )
                        $query_args['date_query']['before'] = $date . ' 00:00:00';
                    elseif( $filter->condition == 'LE' )
                        $query_args['date_query']['before'] = $date . ' 23:59:59';
                    elseif( $filter->condition == 'EQUAL' ){
                        $query_args['date_query']['after'] = $date . ' 00:00:00';
                        $query_args['date_query']['before'] = $date . ' 23:59:59';
                    }
                    // ignore EMPTY and NOTEMPTY
                }elseif( $filter->field_type == 'date' ){
                    $query_args = $this->apply_query_filter_date( $query_args, $filter );
                }elseif( in_array( $filter->field_type, array('number', 'starrating', 'quantity', 'shipping', 'total') )){
                    $query_args = $this->apply_query_filter_numeric( $query_args, $filter );
                }
                else{
                    $query_args = $this->apply_query_filter_general( $query_args, $filter );
                }
            }

            return $query_args;
        }



        private function apply_query_filter_date( $query_args, $filter ){
            global $wpdb;
            $date = $filter->value;
            $meta_key = '_field_' . $filter->field_id;

            //convert NinjaForm date format string to mysql date format string
            $dateformat = $filter->dateformat;
            $dateformat = str_replace( array( 'DD', 'MM', 'YYYY', 'dddd', 'MMMM', 'D' ) , array( '%d', '%m', '%Y', '%W', '%M', '%e' ), $dateformat );

            if( in_array( $filter->condition, array( 'GT', 'GE', 'LT', 'LE', 'EQUAL', 'NE' ) ) ){
                if ($filter->condition == 'GT' )
                    $condition = '>';
                elseif ($filter->condition == 'GE' )
                    $condition = '>=';
                elseif ($filter->condition == 'LT' )
                    $condition = '<';
                elseif ($filter->condition == 'LE' )
                    $condition = '<=';
                elseif ($filter->condition == 'EQUAL' )
                    $condition = '=';
                elseif ($filter->condition == 'NE' )
                    $condition = '<>';
                    
                $where_filter = $wpdb->prepare( "   
                        AND post_id IN(
                            SELECT post_id
                            FROM {$wpdb->postmeta}
                            WHERE 
                                {$wpdb->postmeta}.meta_key = %s
                                AND STR_TO_DATE({$wpdb->postmeta}.meta_value, %s) $condition %s
                        )
                        ", 
                        $meta_key,
                        $dateformat,
                        $filter->value
                    );
                add_filter('posts_where', function( $where ) use ( &$where_filter ){
                    return $where . $where_filter;
                });

            }elseif( $filter->condition == 'EMPTY' ){
                // empty could also mean "not existing" when a new field was added to a form after a submission
                $where_filter = $wpdb->prepare( "   
                        AND 
                            (
                                post_id IN(
                                    SELECT post_id
                                    FROM {$wpdb->postmeta}
                                    WHERE 
                                        {$wpdb->postmeta}.meta_key = %s
                                        AND {$wpdb->postmeta}.meta_value = ''
                                )
                            OR 
                            post_id NOT IN(
                                    SELECT post_id
                                    FROM {$wpdb->postmeta}
                                    WHERE 
                                        {$wpdb->postmeta}.meta_key = %s
                                )
                        )
                        ", 
                        $meta_key,
                        $meta_key
                    );
                add_filter('posts_where', function( $where ) use ( &$where_filter ){
                    return $where . $where_filter;
                });
            }elseif( $filter->condition == 'NOTEMPTY' ){
                $where_filter = $wpdb->prepare( "   
                        AND post_id IN(
                            SELECT post_id
                            FROM {$wpdb->postmeta}
                            WHERE 
                                {$wpdb->postmeta}.meta_key = %s
                                AND {$wpdb->postmeta}.meta_value <> ''
                        )
                        ", 
                        $meta_key
                    );
                add_filter('posts_where', function( $where ) use ( &$where_filter ){
                    return $where . $where_filter;
                });
            }
            
            return $query_args;
        }



        private function apply_query_filter_numeric( $query_args, $filter ){
            global $wpdb;
            $value = $filter->value;
            $meta_key = '_field_' . $filter->field_id;

            if ($filter->condition == 'EMPTY' ){
                // empty could also mean "not existing" when a new field was added to a form after a submission
                $where_filter = $wpdb->prepare( "   
                        AND 
                            (
                                post_id IN(
                                    SELECT post_id
                                    FROM {$wpdb->postmeta}
                                    WHERE 
                                        {$wpdb->postmeta}.meta_key = %s
                                        AND {$wpdb->postmeta}.meta_value = ''
                                )
                            OR 
                            post_id NOT IN(
                                    SELECT post_id
                                    FROM {$wpdb->postmeta}
                                    WHERE 
                                        {$wpdb->postmeta}.meta_key = %s
                                )
                        )
                        ", 
                        $meta_key,
                        $meta_key
                    );
                add_filter('posts_where', function( $where ) use ( &$where_filter ){
                    return $where . $where_filter;
                });

            }else{
                if ($filter->condition == 'GT' )
                    $condition = '>';
                elseif ($filter->condition == 'GE' )
                    $condition = '>=';
                elseif ($filter->condition == 'LT' )
                    $condition = '<';
                elseif ($filter->condition == 'LE' )
                    $condition = '<=';
                elseif ($filter->condition == 'EQUAL' )
                    $condition = '=';
                elseif ($filter->condition == 'NE' )
                    $condition = '<>';
                elseif ($filter->condition == 'NOTEMPTY' ){
                    $condition = '<>';
                    $value = '';
                }
                    
                $where_filter = $wpdb->prepare( "   
                        AND post_id IN(
                            SELECT post_id
                            FROM {$wpdb->postmeta}
                            WHERE 
                                {$wpdb->postmeta}.meta_key = %s
                                AND {$wpdb->postmeta}.meta_value $condition " . ( $value=='' ? '%s' : '%d' ) . "
                        )
                        ", 
                        $meta_key,
                        $value
                    );
                add_filter('posts_where', function( $where ) use ( &$where_filter ){
                    return $where . $where_filter;
                });
            }
            
            return $query_args;
        }



        private function apply_query_filter_general( $query_args, $filter ){
            global $wpdb;
            $value = $filter->value;
            if( !property_exists( $filter, 'field_id') )
                return $query_args;

            $meta_key = '_field_' . $filter->field_id;

            if ($filter->condition == 'EMPTY' ){
                // empty could also mean "not existing" when a new field was added to a form after a submission
                $where_filter = $wpdb->prepare( "   
                        AND 
                            (
                                post_id IN(
                                    SELECT post_id
                                    FROM {$wpdb->postmeta}
                                    WHERE 
                                        {$wpdb->postmeta}.meta_key = %s
                                        AND {$wpdb->postmeta}.meta_value = ''
                                )
                            OR 
                            post_id NOT IN(
                                    SELECT post_id
                                    FROM {$wpdb->postmeta}
                                    WHERE 
                                        {$wpdb->postmeta}.meta_key = %s
                                )
                        )
                        ", 
                        $meta_key,
                        $meta_key
                    );
                add_filter('posts_where', function( $where ) use ( &$where_filter ){
                    return $where . $where_filter;
                });

            }else{
                if ($filter->condition == 'GT' )
                    $condition = '>';
                elseif ($filter->condition == 'GE' )
                    $condition = '>=';
                elseif ($filter->condition == 'LT' )
                    $condition = '<';
                elseif ($filter->condition == 'LE' )
                    $condition = '<=';
                elseif ($filter->condition == 'EQUAL' )
                    $condition = '=';
                elseif ($filter->condition == 'NE' )
                    $condition = '<>';
                elseif ($filter->condition == 'NOTEMPTY' ){
                    $condition = '<>';
                    $value = '';
                }elseif ($filter->condition == 'CONTAINS' ){
                    $condition = 'LIKE';
                    $value = '%'.$value.'%';
                }elseif ($filter->condition == 'LIKE' ){
                    $condition = 'LIKE';
                    $value = str_replace( '*', '%', $value );
                }
                 

                $where_filter = $wpdb->prepare( "   
                        AND post_id IN(
                            SELECT post_id
                            FROM {$wpdb->postmeta}
                            WHERE 
                                {$wpdb->postmeta}.meta_key = %s
                                AND {$wpdb->postmeta}.meta_value $condition %s
                        )
                        ", 
                        $meta_key,
                        $value
                    );
                add_filter('posts_where', function( $where ) use ( &$where_filter ){
                    return $where . $where_filter;
                });
            }
            
            return $query_args;
        }



        public static function template( $file_name = '', array $data = array(), $return = FALSE )
        {
            if( ! $file_name ) return FALSE;

            extract( $data );

            $path = self::$dir . 'includes/Templates/' . $file_name;

            if( ! file_exists( $path ) ) return FALSE;

            if( $return ) return file_get_contents( $path );

            include $path;
        }

        /*
         * Required methods for all extension.
         */

        public function setup_license()
        {
            if ( ! class_exists( 'NF_Extension_Updater' ) ) return;

            new NF_Extension_Updater( self::NAME, self::VERSION, self::AUTHOR, __FILE__, self::SLUG );
        }
    }

    /**
     * The main function responsible for returning The Highlander Plugin
     * Instance to functions everywhere.
     *
     * Use this function like you would a global variable, except without needing
     * to declare the global.
     *
     * @since 3.0
     * @return {class} Highlander Instance
     */
    function NF_ExcelExport()
    {
        return NF_ExcelExport::instance();
    }

    NF_ExcelExport();
}
