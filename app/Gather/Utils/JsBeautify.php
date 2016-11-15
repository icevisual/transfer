<?php
namespace App\Gather\Utils;

class JsBeautify
{

    var $input, $output, $token_text, $last_type, $last_text, $last_word, $current_mode, $modes, $indent_string;

    var $whitespace, $wordchar, $punct, $parser_pos, $line_starters, $in_case;

    var $prefix, $token_type, $do_block_just_closed, $var_line, $var_line_tainted;
    
    var $indent_level;

    function trim_output()
    {
        while (count($this->output) && ($this->output[count($this->output) - 1] == ' ' || $this->output[count($this->output) - 1] == $this->indent_string)) {
            array_pop($this->output);
        }
    }

    function print_newline($ignore_repeated = true)
    {
        $this->trim_output();
        
        if (! count($this->output)) {
            return; // no newline on start of file
        }
        
        if ($this->output[count($this->output) - 1] !== "\n" || ! $ignore_repeated) {
            array_push($this->output, "\n");
        }
        for ($i = 0; $i < $this->indent_level; $i ++) {
            array_push($this->output, $this->indent_string);
        }
    }

    function print_space()
    {
        $last_output = count($this->output) ? $this->output[count($this->output) - 1] : ' ';
        if ($last_output !== ' ' && $last_output !== '\n' && $last_output !== $this->indent_string) { // prevent occassional duplicate space
            array_push($this->output, " ");
        }
    }

    function print_token()
    {
        array_push($this->output, $this->token_text);
    }

    function indent()
    {
        $this->indent_level ++;
    }

    function unindent()
    {
        if ($this->indent_level) {
            $this->indent_level --;
        }
    }

    function remove_indent()
    {
        if (count($this->output) && $this->output[count($this->output) - 1] == $this->indent_string) {
            array_pop($this->output);
        }
    }

    function set_mode($mode)
    {
        array_push($this->modes, $this->current_mode);
        $this->current_mode = $mode;
    }

    function restore_mode()
    {
        $this->do_block_just_closed = $this->current_mode == 'DO_BLOCK';
        $this->current_mode = array_pop($this->modes);
    }

//     function in_array($what, $arr)
//     {
//         for ($i = 0; $i < count($arr); $i ++) {
//             if ($arr[$i] == $what) {
//                 return true;
//             }
//         }
//         return false;
//     }

    function get_next_token()
    {
        $n_newlines = 0;
        $c = '';
        
        do {
            if ($this->parser_pos >= strlen($this->input)) {
                return [
                    '',
                    'TK_EOF'
                ];
            }
            $c = $this->input{$this->parser_pos};
            
            $this->parser_pos += 1;
            if ($c == "\n") {
                $n_newlines += 1;
            }
        } while (in_array($c, $this->whitespace));
        
        if ($n_newlines > 1) {
            for ($i = 0; $i < 2; $i ++) {
                $this->print_newline($i == 0);
            }
        }
        $wanted_newline = ($n_newlines == 1);
        
        if (in_array($c, $this->wordchar)) {
            if ($this->parser_pos < strlen($this->input)) {
                while (in_array($this->input{$this->parser_pos}, $this->wordchar)) {
                    $c .= $this->input{$this->parser_pos};
                    $this->parser_pos += 1;
                    if ($this->parser_pos == strlen($this->input)) {
                        break;
                    }
                }
            }
            
            // small and surprisingly unugly hack for 1E-10 representation
            if ($this->parser_pos !== strlen($this->input) && preg_match('/^[0-9]+[Ee]$/', $c) && $this->input{$this->parser_pos} == '-') {
                $this->parser_pos += 1;
                
                $t = $this->get_next_token($this->parser_pos);
                $c .= '-' . $t[0];
                return [
                    $c,
                    'TK_WORD'
                ];
            }
            
            if ($c == 'in') { // hack for 'in' operator
                return [
                    $c,
                    'TK_OPERATOR'
                ];
            }
            return [
                $c,
                'TK_WORD'
            ];
        }
        
        if ($c == '(' || $c == '[') {
            return [
                $c,
                'TK_START_EXPR'
            ];
        }
        
        if ($c == ')' || $c == ']') {
            return [
                $c,
                'TK_END_EXPR'
            ];
        }
        
        if ($c == '{') {
            return [
                $c,
                'TK_START_BLOCK'
            ];
        }
        
        if ($c == '}') {
            return [
                $c,
                'TK_END_BLOCK'
            ];
        }
        
        if ($c == ';') {
            return [
                $c,
                'TK_END_COMMAND'
            ];
        }
        
        if ($c == '/') {
            $comment = '';
            // peek for comment /* ... */
            if ($this->input{$this->parser_pos} == '*') {
                $this->parser_pos += 1;
                if ($this->parser_pos < strlen($this->input)) {
                    while (! ($this->input{$this->parser_pos} == '*' && $this->input{$this->parser_pos + 1} && $this->input{$this->parser_pos + 1} == '/') && $this->parser_pos < strlen($this->input)) {
                        $comment .= $this->input{$this->parser_pos};
                        $this->parser_pos += 1;
                        if ($this->parser_pos >= strlen($this->input)) {
                            break;
                        }
                    }
                }
                $this->parser_pos += 2;
                return [
                    '/*' . $comment . '*/',
                    'TK_BLOCK_COMMENT'
                ];
            }
            // peek for comment // ...
            if ($this->input{$this->parser_pos} == '/') {
                $comment = $c;
                while ($this->input{$this->parser_pos} !== "\x0d" && $this->input{$this->parser_pos} !== "\x0a") {
                    $comment .= $this->input{$this->parser_pos};
                    $this->parser_pos += 1;
                    if ($this->parser_pos >= strlen($this->input)) {
                        break;
                    }
                }
                $this->parser_pos += 1;
                if ($wanted_newline) {
                    $this->print_newline();
                }
                return [
                    $comment,
                    'TK_COMMENT'
                ];
            }
        }
        
        if ($c == "'" || // string
$c == '"' || // string
($c == '/' && (($this->last_type == 'TK_WORD' && $this->last_text == 'return') || ($this->last_type == 'TK_START_EXPR' || $this->last_type == 'TK_END_BLOCK' || $this->last_type == 'TK_OPERATOR' || $this->last_type == 'TK_EOF' || $this->last_type == 'TK_END_COMMAND')))) { // regexp
            $sep = $c;
            $esc = false;
            $c = '';
            
            if ($this->parser_pos < strlen($this->input)) {
                
                while ($esc || $this->input{$this->parser_pos} !== $sep) {
                    $c .= $this->input{$this->parser_pos};
                    if (! $esc) {
                        $esc = $this->input{$this->parser_pos} == '\\';
                    } else {
                        $esc = false;
                    }
                    $this->parser_pos += 1;
                    if ($this->parser_pos >= strlen($this->input)) {
                        break;
                    }
                }
            }
            
            $this->parser_pos += 1;
            if ($this->last_type == 'TK_END_COMMAND') {
                $this->print_newline();
            }
            return [
                $sep . $c . $sep,
                'TK_STRING'
            ];
        }
        
        if (in_array($c, $this->punct)) {
            while ($this->parser_pos < strlen($this->input) && in_array($c . $this->input{$this->parser_pos}, $this->punct)) {
                $c .= $this->input{$this->parser_pos};
                $this->parser_pos += 1;
                if ($this->parser_pos >= strlen($this->input)) {
                    break;
                }
            }
            return [
                $c,
                'TK_OPERATOR'
            ];
        }
        
        return [
            $c,
            'TK_UNKNOWN'
        ];
    }

    function js_beautify($js_source_text, $indent_size = 4, $indent_character = ' ', $indent_level = 0)
    {
        
        // ----------------------------------
        $this->indent_string = '';
        while ($indent_size --) {
            $this->indent_string .= $indent_character;
        }
        
        $this->input = $js_source_text;
        
        $this->last_word = ''; // last 'TK_WORD' passed
        $this->last_type = 'TK_START_EXPR'; // last token type
        $this->last_text = ''; // last token text
        $this->output = [];
        
        $this->do_block_just_closed = false;
        $this->var_line = false;
        $this->var_line_tainted = false;
        
        $this->whitespace = str_split("\n\r\t ");
        $this->wordchar = str_split("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_$");
        $this->punct = str_split('+ - * / % & ++ -- = += -= *= /= %= == == != !== > < >= <= >> << >>> >>>= >>= <<= && &= | || ! !! , : ? ^ ^= |=');
        
        // words which should always start on new line.
        $this->line_starters = str_split('continue,try,throw,return,var,if,switch,case,default,for,while,break,function');
        
        // states showing if we are currently in expression (i.e. "if" case) - 'EXPRESSION', or in usual block (like, procedure), 'BLOCK'.
        // some formatting depends on that.
        $this->current_mode = 'BLOCK';
        $this->modes = [
            $this->current_mode
        ];
        
        $this->parser_pos = 0; // parser position
        $this->in_case = false; // flag for parser that case/default has been processed, and next colon needs special attention
        while (true) {
            $t = $this->get_next_token($this->parser_pos);
            $this->token_text = $t[0];
            $this->token_type = $t[1];
            if ($this->token_type == 'TK_EOF') {
                break;
            }
            
            switch ($this->token_type) {
                
                case 'TK_START_EXPR':
                    $this->var_line = false;
                    $this->set_mode('EXPRESSION');
                    if ($this->last_type == 'TK_END_EXPR' || $this->last_type == 'TK_START_EXPR') {
                        // do nothing on (( and )( and ][ and ]( ..
                    } else 
                        if ($this->last_type !== 'TK_WORD' && $this->last_type !== 'TK_OPERATOR') {
                            $this->print_space();
                        } else 
                            if (in_array($this->last_word, $this->line_starters) && $this->last_word !== 'function') {
                                $this->print_space();
                            }
                    $this->print_token();
                    break;
                
                case 'TK_END_EXPR':
                    $this->print_token();
                    $this->restore_mode();
                    break;
                
                case 'TK_START_BLOCK':
                    
                    if ($this->last_word == 'do') {
                        $this->set_mode('DO_BLOCK');
                    } else {
                        $this->set_mode('BLOCK');
                    }
                    if ($this->last_type !== 'TK_OPERATOR' && $this->last_type !== 'TK_START_EXPR') {
                        if ($this->last_type == 'TK_START_BLOCK') {
                            $this->print_newline();
                        } else {
                            $this->print_space();
                        }
                    }
                    $this->print_token();
                    $this->indent();
                    break;
                
                case 'TK_END_BLOCK':
                    if ($this->last_type == 'TK_START_BLOCK') {
                        // nothing
                        $this->trim_output();
                        $this->unindent();
                    } else {
                        $this->unindent();
                        $this->print_newline();
                    }
                    $this->print_token();
                    $this->restore_mode();
                    break;
                
                case 'TK_WORD':
                    
                    if ($this->do_block_just_closed) {
                        $this->print_space();
                        $this->print_token();
                        $this->print_space();
                        break;
                    }
                    
                    if ($this->token_text == 'case' || $this->token_text == 'default') {
                        if ($this->last_text == ':') {
                            // switch cases following one another
                            $this->remove_indent();
                        } else {
                            // case statement starts in the same line where switch
                            $this->unindent();
                            $this->print_newline();
                            $this->indent();
                        }
                        $this->print_token();
                        $this->in_case = true;
                        break;
                    }
                    
                    $this->prefix = 'NONE';
                    if ($this->last_type == 'TK_END_BLOCK') {
                        if (! in_array(strtolower($this->token_text), [
                            'else',
                            'catch',
                            'finally'
                        ])) {
                            $this->prefix = 'NEWLINE';
                        } else {
                            $this->prefix = 'SPACE';
                            $this->print_space();
                        }
                    } else 
                        if ($this->last_type == 'TK_END_COMMAND' && ($this->current_mode == 'BLOCK' || $this->current_mode == 'DO_BLOCK')) {
                            $this->prefix = 'NEWLINE';
                        } else 
                            if ($this->last_type == 'TK_END_COMMAND' && $this->current_mode == 'EXPRESSION') {
                                $this->prefix = 'SPACE';
                            } else 
                                if ($this->last_type == 'TK_WORD') {
                                    $this->prefix = 'SPACE';
                                } else 
                                    if ($this->last_type == 'TK_START_BLOCK') {
                                        $this->prefix = 'NEWLINE';
                                    } else 
                                        if ($this->last_type == 'TK_END_EXPR') {
                                            $this->print_space();
                                            $this->prefix = 'NEWLINE';
                                        }
                    
                    if ($this->last_type !== 'TK_END_BLOCK' && in_array(strtolower($this->token_text), [
                        'else',
                        'catch',
                        'finally'
                    ])) {
                        $this->print_newline();
                    } else 
                        if (in_array($this->token_text, $this->line_starters) || $this->prefix == 'NEWLINE') {
                            if ($this->last_text == 'else') {
                                // no need to force newline on else break
                                $this->print_space();
                            } else 
                                if (($this->last_type == 'TK_START_EXPR' || $this->last_text == '=') && $this->token_text == 'function') {
                                    // no need to force newline on 'function': (function
                                    // DONOTHING
                                } else 
                                    if ($this->last_type == 'TK_WORD' && ($this->last_text == 'return' || $this->last_text == 'throw')) {
                                        // no newline between 'return nnn'
                                        $this->print_space();
                                    } else 
                                        if ($this->last_type !== 'TK_END_EXPR') {
                                            if (($this->last_type !== 'TK_START_EXPR' || $this->token_text !== 'var') && $this->last_text !== ':') {
                                                // no need to force newline on 'var': for (var x = 0...)
                                                if ($this->token_text == 'if' && $this->last_type == 'TK_WORD' && $this->last_word == 'else') {
                                                    // no newline for } else if {
                                                    $this->print_space();
                                                } else {
                                                    $this->print_newline();
                                                }
                                            }
                                        } else {
                                            if (in_array($this->token_text, $this->line_starters) && $this->last_text !== ')') {
                                                $this->print_newline();
                                            }
                                        }
                        } else 
                            if ($this->prefix == 'SPACE') {
                                $this->print_space();
                            }
                    $this->print_token();
                    $this->last_word = $this->token_text;
                    
                    if ($this->token_text == 'var') {
                        $this->var_line = true;
                        $this->var_line_tainted = false;
                    }
                    
                    break;
                
                case 'TK_END_COMMAND':
                    
                    $this->print_token();
                    $this->var_line = false;
                    break;
                
                case 'TK_STRING':
                    
                    if ($this->last_type == 'TK_START_BLOCK' || $this->last_type == 'TK_END_BLOCK') {
                        $this->print_newline();
                    } else 
                        if ($this->last_type == 'TK_WORD') {
                            $this->print_space();
                        }
                    $this->print_token();
                    break;
                
                case 'TK_OPERATOR':
                    
                    $start_delim = true;
                    $end_delim = true;
                    if ($this->var_line && $this->token_text !== ',') {
                        $this->var_line_tainted = true;
                        if ($this->token_text == ':') {
                            $this->var_line = false;
                        }
                    }
                    
                    if ($this->token_text == ':' && $this->in_case) {
                        $this->print_token(); // colon really asks for separate treatment
                        $this->print_newline();
                        break;
                    }
                    
                    $this->in_case = false;
                    
                    if ($this->token_text == ',') {
                        if ($this->var_line) {
                            if ($this->var_line_tainted) {
                                $this->print_token();
                                $this->print_newline();
                                $this->var_line_tainted = false;
                            } else {
                                $this->print_token();
                                $this->print_space();
                            }
                        } else 
                            if ($this->last_type == 'TK_END_BLOCK') {
                                $this->print_token();
                                $this->print_newline();
                            } else {
                                if ($this->current_mode == 'BLOCK') {
                                    $this->print_token();
                                    $this->print_newline();
                                } else {
                                    // EXPR od DO_BLOCK
                                    $this->print_token();
                                    $this->print_space();
                                }
                            }
                        break;
                    } else 
                        if ($this->token_text == '--' || $this->token_text == '++') { // unary operators special case
                            if ($this->last_text == ';') {
                                // space for (;; ++i)
                                $start_delim = true;
                                $end_delim = false;
                            } else {
                                $start_delim = false;
                                $end_delim = false;
                            }
                        } else 
                            if ($this->token_text == '!' && $this->last_type == 'TK_START_EXPR') {
                                // special case handling: if (!a)
                                $start_delim = false;
                                $end_delim = false;
                            } else 
                                if ($this->last_type == 'TK_OPERATOR') {
                                    $start_delim = false;
                                    $end_delim = false;
                                } else 
                                    if ($this->last_type == 'TK_END_EXPR') {
                                        $start_delim = true;
                                        $end_delim = true;
                                    } else 
                                        if ($this->token_text == '.') {
                                            // decimal digits or object.property
                                            $start_delim = false;
                                            $end_delim = false;
                                        } else 
                                            if ($this->token_text == ':') {
                                                // zz: xx
                                                // can't differentiate ternary op, so for now it's a ? b: c; without space before colon
                                                if (preg_match('/^\d+$/', $this->last_text)) {
                                                    // a little help for ternary a ? 1 : 0;
                                                    $start_delim = true;
                                                } else {
                                                    $start_delim = false;
                                                }
                                            }
                    if ($start_delim) {
                        $this->print_space();
                    }
                    
                    $this->print_token();
                    
                    if ($end_delim) {
                        $this->print_space();
                    }
                    break;
                
                case 'TK_BLOCK_COMMENT':
                    
                    $this->print_newline();
                    $this->print_token();
                    $this->print_newline();
                    break;
                
                case 'TK_COMMENT':
                    
                    // print_newline();
                    $this->print_space();
                    $this->print_token();
                    $this->print_newline();
                    break;
                
                case 'TK_UNKNOWN':
                    $this->print_token();
                    break;
            }
            
            $this->last_type = $this->token_type;
            $this->last_text = $this->token_text;
        }
        
        return implode('', $this->output);
    }
}
