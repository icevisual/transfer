<?php
namespace App\Gather\Utils;

class Trie
{

    private $dics = [];

    private $data_key = '#DATA';

    public function scan($show = false)
    {
        $show && dump($this->dics);
        return $this->dics;
    }

    public function complete($str)
    {
        $alias = $this->dics;
        $mb_len = mb_strlen($str);
        $hit = [];
        for ($i = 0; $i < $mb_len; $i ++) {
            $wd = mb_substr($str, $i, 1);
            if (isset($alias[$wd])) {
                $alias = &$alias[$wd];
            } else {
                return $hit;
            }
        }
        $rest = $alias;
        if (isset($rest[$this->data_key])) {
            unset($rest[$this->data_key]);
        }
        $loop = $alias;
        $current = current($loop);
        if (key($current) == $this->data_key) {
            $current = next($current);
        }
        if ($current) {
            $stack = $current;
            while ($stack) {
                $end = end($stack);
                if (! (isset($end[$this->data_key]) && count($end) == 1)) {} else {}
            }
        }
        
        dump($loop);
        
        foreach ($rest as $key => $value) {
            if ($value && isset($value[$this->data_key]) && count($value) == 1) {
                // End Point
            }
            $hit[] = $key;
        }
        return $hit;
    }

    public function add($key, $value)
    {
        $mb_len = mb_strlen($key);
        $alias = &$this->dics;
        for ($i = 0; $i < $mb_len; $i ++) {
            $wd = mb_substr($key, $i, 1);
            if (isset($alias[$wd])) {
                $alias = &$alias[$wd];
                if ($i == $mb_len - 1) { // End
                    $alias[$this->data_key][] = $value;
                }
            } else {
                if ($i == $mb_len - 1) { // End
                    $alias[$wd][$this->data_key][] = $value;
                } else { // middle
                    $alias[$wd] = [];
                    $alias = &$alias[$wd];
                }
            }
        }
    }

    /**
     * 查找
     *
     * @param unknown $dics            
     * @param unknown $str            
     * @return multitype:string
     */
    public function find($str)
    {
        $alias = $this->dics;
        $mb_len = mb_strlen($str);
        $last = false;
        $ls = [];
        $match = '';
        for ($i = 0; $i < $mb_len; $i ++) {
            $wd = mb_substr($str, $i, 1);
            if (isset($alias[$wd])) {
                // dump($wd);
                $last = isset($alias[$wd][$this->data_key]) ? $alias[$wd][$this->data_key] : $last;
                $match .= $wd;
                $alias = &$alias[$wd];
            } else {
                if ($last === false) {
                    break;
                } else {
                    $ls[$match] = $last;
                    $last = false;
                    $match = '';
                    $alias = &$dics;
                    $i -= 1;
                }
            }
        }
        if ($last !== false) {
            $ls[$match] = $last;
        }
        return $ls;
    }
}