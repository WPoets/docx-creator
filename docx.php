<?php

    \aw2_library::add_service('docx.parse_template','Parse the docx template',['namespace'=>__NAMESPACE__]);
    function parse_template($atts,$content=null,$shortcode){
        if(\aw2_library::pre_actions('all',$atts,$content)==false)return;
        
        extract(\aw2_library::shortcode_atts( array(
            'data'=>null,
            'path' => null,
            'filename' => 'Report.docx'
        ), $atts) );
        if(is_null($path)) return;
        
        $lib_path = 'vendor/autoload.php';
        require_once $lib_path;

        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($path);

        foreach( $data['sections'] as $item => $value ){
            if('true' == $value){
                $templateProcessor->cloneBlock($item, 1, true, false);
            }else{
                $templateProcessor->cloneBlock($item, 0, true, false);
            }
        }

        foreach( $data['flat'] as $item=> $value ){
            $templateProcessor->setValue($item, $value);
        }
        
        foreach( $data['rows'] as $item=> $value ){
            $templateProcessor->cloneRowAndSetValues($item, $value);
        }        
        
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $templateProcessor->saveAs('php://output');
        exit();
    }
?>