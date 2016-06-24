<?php




if (! function_exists('loadXml')) {

    function array_dot_set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    function loadXml1($xmlStr){

        $xml = simplexml_load_string($xmlStr);

        $stack = [ $xml->getName() => $xml];

        $data = [];
        $prefix = $xml->getName() ;
        // 先序
        while (!empty($stack)){
            $prefix = array_keys($stack);
            $prefix = end($prefix);
            $node = array_pop($stack);

            if($node->count()){
                dump($prefix);
            }else{
                dump($prefix.'=>'.$node->getName().':'.$node->__toString());
                array_set($data, $prefix, $node->__toString());
            }

            if($childrens = $node->children()){
                $unshift = [];
                foreach ($childrens as $k => $v){
                    $unshift [$prefix.'.'.$k] = $v;
                }
                $unshift = array_reverse($unshift);
                foreach ($unshift as $k => $v){
                    $stack[$k] = $v;
                }
            }
        }
        return $data;
    }


    function loadXml2($xmlStr)
    {
        $doc = new \DOMDocument();
        //         $xmlStr = iconv('UTF-8', 'GBK', $xmlStr);
        $doc->loadXML($xmlStr);

        $stack = [$doc];
        $data = [];

        while (!empty($stack)){
            $node = array_pop($stack);

            $childrens = $node->childNodes ;
            if($childrens){
                $node->childNodes  = null;
                $stack[] = $node;
                $stack[] = $childrens[0];
            }else{
                $nextSibling  = $node->nextSibling;
                if($nextSibling){
                    $stack[] = $nextSibling;
                }else{
                    dump($node->nodeName);
                }
            }
        }
        exit;


        while (!empty($stack)){
            $prefix = array_keys($stack);
            $prefix = end($prefix);
            $node = array_pop($stack);

            if($node->nodeName != '#comment'){
                if($node->childNodes->length == 0){
                    dump($prefix.' ===> ');
                    array_dot_set($data, $prefix, '');
                }else if($node->childNodes->length == 1
                    && $node->childNodes[0]->nodeType == XML_TEXT_NODE){
                    dump($prefix.' ===> '.$node->nodeName.':'.trim($node->childNodes[0]->wholeText));
                    array_dot_set($data, $prefix, trim($node->childNodes[0]->wholeText));
                }else{
                    dump($prefix);
                }
            }
            $childrens = $node->childNodes;
            if( $childrens
                && (
                    $childrens->length > 1
                    || (
                        $childrens->length == 1
                        && $childrens[0]->nodeType != XML_TEXT_NODE) )
            ){
                $unshift = [];

                foreach ($childrens as $k => $v){
                    if($v->nodeType != XML_TEXT_NODE){
                        $unshift [$prefix.'.'.$v->nodeName.'.'.$k] = $v;
                    }
                }
                $unshift = array_reverse($unshift);
                foreach ($unshift as $k => $v){
                    $stack[$k] = $v;
                }
            }else{

            }
        }
        return $data;
    }

    function loadXml($xmlStr)
    {
        $doc = new \DOMDocument();
        $xmlStr = iconv('UTF-8', 'GBK', $xmlStr);
        $doc->loadXML($xmlStr);

        //         foreach ($doc->childNodes[0]->childNodes as $v){
        //             dump($v);
        //         }
        //         edump($doc->childNodes[0]->childNodes);
        $stack = [ $doc->nodeName => $doc];
        $data = [];
        while (!empty($stack)){
            $prefix = array_keys($stack);
            $prefix = end($prefix);
            $node = array_pop($stack);

            if($node->nodeName != '#comment'){
                if($node->childNodes->length == 0){
                    dump($prefix.' ===> ');
                    array_dot_set($data, $prefix, '');
                }else if($node->childNodes->length == 1
                    && $node->childNodes[0]->nodeType == XML_TEXT_NODE){
                    dump($prefix.' ===> '.$node->nodeName.':'.trim($node->childNodes[0]->wholeText));
                    array_dot_set($data, $prefix, trim($node->childNodes[0]->wholeText));
                }else{
                    dump($prefix);
                }
            }
            $childrens = $node->childNodes;
            if( $childrens
                && (
                    $childrens->length > 1
                    || (
                        $childrens->length == 1
                        && $childrens[0]->nodeType != XML_TEXT_NODE) )
            ){
                $unshift = [];

                if('CMBSDKPGK' == $node->nodeName){
                    edump($node->getElementsByTagName('NTQTSINFZ'));
                }

                foreach ($childrens as $k => $v){
                    if($v->nodeType != XML_TEXT_NODE){
                        $unshift [$prefix.'.'.$v->nodeName.'.'.$k] = $v;
                    }
                }
                $unshift = array_reverse($unshift);
                foreach ($unshift as $k => $v){
                    $stack[$k] = $v;
                }
            }
        }
        return $data;
    }
}