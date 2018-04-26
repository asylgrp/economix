<?php

namespace asylgrp\descparser;

class Grammar
{
    protected $string;
    protected $position;
    protected $value;
    protected $cache;
    protected $cut;
    protected $errors;
    protected $warnings;

    protected function parseDESC()
    {
        $_position = $this->position;

        if (isset($this->cache['DESC'][$_position])) {
            $_success = $this->cache['DESC'][$_position]['success'];
            $this->position = $this->cache['DESC'][$_position]['position'];
            $this->value = $this->cache['DESC'][$_position]['value'];

            return $_success;
        }

        $_value4 = array();
        $_cut5 = $this->cut;

        while (true) {
            $_position3 = $this->position;

            $this->cut = false;
            $_position1 = $this->position;
            $_cut2 = $this->cut;

            $this->cut = false;
            $_success = $this->parseDATE();

            if (!$_success && !$this->cut) {
                $this->position = $_position1;

                $_success = $this->parseNAME();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position1;

                $_success = $this->parseRELATION();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position1;

                $_success = $this->parseTAG();
            }

            if (!$_success && !$this->cut) {
                $this->position = $_position1;

                $_success = $this->parseIGNORED_CHAR();
            }

            $this->cut = $_cut2;

            if (!$_success) {
                break;
            }

            $_value4[] = $this->value;
        }

        if (!$this->cut) {
            $_success = true;
            $this->position = $_position3;
            $this->value = $_value4;
        }

        $this->cut = $_cut5;

        if ($_success) {
            $nodes = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$nodes) {
                return array_values(array_filter($nodes));
            });
        }

        $this->cache['DESC'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'DESC');
        }

        return $_success;
    }

    protected function parseIGNORED_CHAR()
    {
        $_position = $this->position;

        if (isset($this->cache['IGNORED_CHAR'][$_position])) {
            $_success = $this->cache['IGNORED_CHAR'][$_position]['success'];
            $this->position = $this->cache['IGNORED_CHAR'][$_position]['position'];
            $this->value = $this->cache['IGNORED_CHAR'][$_position]['value'];

            return $_success;
        }

        if ($this->position < strlen($this->string)) {
            $_success = true;
            $this->value = substr($this->string, $this->position, 1);
            $this->position += 1;
        } else {
            $_success = false;
        }

        if ($_success) {
            $this->value = call_user_func(function () {
                return null;
            });
        }

        $this->cache['IGNORED_CHAR'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'IGNORED_CHAR');
        }

        return $_success;
    }

    protected function parseDATE()
    {
        $_position = $this->position;

        if (isset($this->cache['DATE'][$_position])) {
            $_success = $this->cache['DATE'][$_position]['success'];
            $this->position = $this->cache['DATE'][$_position]['position'];
            $this->value = $this->cache['DATE'][$_position]['value'];

            return $_success;
        }

        $_position9 = $this->position;

        if (preg_match('/^[0-9\\/-]$/', substr($this->string, $this->position, 1))) {
            $_success = true;
            $this->value = substr($this->string, $this->position, 1);
            $this->position += 1;
        } else {
            $_success = false;
        }

        if ($_success) {
            $_value7 = array($this->value);
            $_cut8 = $this->cut;

            while (true) {
                $_position6 = $this->position;

                $this->cut = false;
                if (preg_match('/^[0-9\\/-]$/', substr($this->string, $this->position, 1))) {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, 1);
                    $this->position += 1;
                } else {
                    $_success = false;
                }

                if (!$_success) {
                    break;
                }

                $_value7[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position6;
                $this->value = $_value7;
            }

            $this->cut = $_cut8;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position9, $this->position - $_position9));
        }

        if ($_success) {
            $value = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$value) {
                return new Tree\DateNode($value);
            });
        }

        $this->cache['DATE'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'DATE');
        }

        return $_success;
    }

    protected function parseNAME()
    {
        $_position = $this->position;

        if (isset($this->cache['NAME'][$_position])) {
            $_success = $this->cache['NAME'][$_position]['success'];
            $this->position = $this->cache['NAME'][$_position]['position'];
            $this->value = $this->cache['NAME'][$_position]['value'];

            return $_success;
        }

        $_value10 = array();

        if (substr($this->string, $this->position, strlen('@')) === '@') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('@'));
            $this->position += strlen('@');
        } else {
            $_success = false;

            $this->report($this->position, '\'@\'');
        }

        if ($_success) {
            $_value10[] = $this->value;

            $_success = $this->parseSTRING();

            if ($_success) {
                $value = $this->value;
            }
        }

        if ($_success) {
            $_value10[] = $this->value;

            $this->value = $_value10;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$value) {
                return new Tree\NameNode($value);
            });
        }

        $this->cache['NAME'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'NAME');
        }

        return $_success;
    }

    protected function parseRELATION()
    {
        $_position = $this->position;

        if (isset($this->cache['RELATION'][$_position])) {
            $_success = $this->cache['RELATION'][$_position]['success'];
            $this->position = $this->cache['RELATION'][$_position]['position'];
            $this->value = $this->cache['RELATION'][$_position]['value'];

            return $_success;
        }

        $_value11 = array();

        if (substr($this->string, $this->position, strlen('rel:')) === 'rel:') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('rel:'));
            $this->position += strlen('rel:');
        } else {
            $_success = false;

            $this->report($this->position, '\'rel:\'');
        }

        if ($_success) {
            $_value11[] = $this->value;

            $_success = $this->parseSTRING();

            if ($_success) {
                $value = $this->value;
            }
        }

        if ($_success) {
            $_value11[] = $this->value;

            $this->value = $_value11;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$value) {
                return new Tree\RelationNode($value);
            });
        }

        $this->cache['RELATION'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'RELATION');
        }

        return $_success;
    }

    protected function parseTAG()
    {
        $_position = $this->position;

        if (isset($this->cache['TAG'][$_position])) {
            $_success = $this->cache['TAG'][$_position]['success'];
            $this->position = $this->cache['TAG'][$_position]['position'];
            $this->value = $this->cache['TAG'][$_position]['value'];

            return $_success;
        }

        $_value12 = array();

        if (substr($this->string, $this->position, strlen('#')) === '#') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('#'));
            $this->position += strlen('#');
        } else {
            $_success = false;

            $this->report($this->position, '\'#\'');
        }

        if ($_success) {
            $_value12[] = $this->value;

            $_success = $this->parseSTRING();

            if ($_success) {
                $value = $this->value;
            }
        }

        if ($_success) {
            $_value12[] = $this->value;

            $this->value = $_value12;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$value) {
                return new Tree\TagNode($value);
            });
        }

        $this->cache['TAG'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'TAG');
        }

        return $_success;
    }

    protected function parseSTRING()
    {
        $_position = $this->position;

        if (isset($this->cache['STRING'][$_position])) {
            $_success = $this->cache['STRING'][$_position]['success'];
            $this->position = $this->cache['STRING'][$_position]['position'];
            $this->value = $this->cache['STRING'][$_position]['value'];

            return $_success;
        }

        $_position13 = $this->position;
        $_cut14 = $this->cut;

        $this->cut = false;
        $_success = $this->parseQUOTED_STRING();

        if (!$_success && !$this->cut) {
            $this->position = $_position13;

            $_success = $this->parseUNQUOTED_STRING();
        }

        $this->cut = $_cut14;

        $this->cache['STRING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'STRING');
        }

        return $_success;
    }

    protected function parseQUOTED_STRING()
    {
        $_position = $this->position;

        if (isset($this->cache['QUOTED_STRING'][$_position])) {
            $_success = $this->cache['QUOTED_STRING'][$_position]['success'];
            $this->position = $this->cache['QUOTED_STRING'][$_position]['position'];
            $this->value = $this->cache['QUOTED_STRING'][$_position]['value'];

            return $_success;
        }

        $_value22 = array();

        if (substr($this->string, $this->position, strlen('"')) === '"') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen('"'));
            $this->position += strlen('"');
        } else {
            $_success = false;

            $this->report($this->position, '\'"\'');
        }

        if ($_success) {
            $_value22[] = $this->value;

            $_position21 = $this->position;

            $_value19 = array();
            $_cut20 = $this->cut;

            while (true) {
                $_position18 = $this->position;

                $this->cut = false;
                $_value17 = array();

                $_position15 = $this->position;
                $_cut16 = $this->cut;

                $this->cut = false;
                if (substr($this->string, $this->position, strlen('"')) === '"') {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, strlen('"'));
                    $this->position += strlen('"');
                } else {
                    $_success = false;

                    $this->report($this->position, '\'"\'');
                }

                if (!$_success) {
                    $_success = true;
                    $this->value = null;
                } else {
                    $_success = false;
                }

                $this->position = $_position15;
                $this->cut = $_cut16;

                if ($_success) {
                    $_value17[] = $this->value;

                    if ($this->position < strlen($this->string)) {
                        $_success = true;
                        $this->value = substr($this->string, $this->position, 1);
                        $this->position += 1;
                    } else {
                        $_success = false;
                    }
                }

                if ($_success) {
                    $_value17[] = $this->value;

                    $this->value = $_value17;
                }

                if (!$_success) {
                    break;
                }

                $_value19[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position18;
                $this->value = $_value19;
            }

            $this->cut = $_cut20;

            if ($_success) {
                $this->value = strval(substr($this->string, $_position21, $this->position - $_position21));
            }

            if ($_success) {
                $str = $this->value;
            }
        }

        if ($_success) {
            $_value22[] = $this->value;

            if (substr($this->string, $this->position, strlen('"')) === '"') {
                $_success = true;
                $this->value = substr($this->string, $this->position, strlen('"'));
                $this->position += strlen('"');
            } else {
                $_success = false;

                $this->report($this->position, '\'"\'');
            }
        }

        if ($_success) {
            $_value22[] = $this->value;

            $this->value = $_value22;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$str) {
                return $str;
            });
        }

        $this->cache['QUOTED_STRING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'QUOTED_STRING');
        }

        return $_success;
    }

    protected function parseUNQUOTED_STRING()
    {
        $_position = $this->position;

        if (isset($this->cache['UNQUOTED_STRING'][$_position])) {
            $_success = $this->cache['UNQUOTED_STRING'][$_position]['success'];
            $this->position = $this->cache['UNQUOTED_STRING'][$_position]['position'];
            $this->value = $this->cache['UNQUOTED_STRING'][$_position]['value'];

            return $_success;
        }

        $_position29 = $this->position;

        $_value25 = array();

        $_position23 = $this->position;
        $_cut24 = $this->cut;

        $this->cut = false;
        if (substr($this->string, $this->position, strlen(' ')) === ' ') {
            $_success = true;
            $this->value = substr($this->string, $this->position, strlen(' '));
            $this->position += strlen(' ');
        } else {
            $_success = false;

            $this->report($this->position, '\' \'');
        }

        if (!$_success) {
            $_success = true;
            $this->value = null;
        } else {
            $_success = false;
        }

        $this->position = $_position23;
        $this->cut = $_cut24;

        if ($_success) {
            $_value25[] = $this->value;

            if ($this->position < strlen($this->string)) {
                $_success = true;
                $this->value = substr($this->string, $this->position, 1);
                $this->position += 1;
            } else {
                $_success = false;
            }
        }

        if ($_success) {
            $_value25[] = $this->value;

            $this->value = $_value25;
        }

        if ($_success) {
            $_value27 = array($this->value);
            $_cut28 = $this->cut;

            while (true) {
                $_position26 = $this->position;

                $this->cut = false;
                $_value25 = array();

                $_position23 = $this->position;
                $_cut24 = $this->cut;

                $this->cut = false;
                if (substr($this->string, $this->position, strlen(' ')) === ' ') {
                    $_success = true;
                    $this->value = substr($this->string, $this->position, strlen(' '));
                    $this->position += strlen(' ');
                } else {
                    $_success = false;

                    $this->report($this->position, '\' \'');
                }

                if (!$_success) {
                    $_success = true;
                    $this->value = null;
                } else {
                    $_success = false;
                }

                $this->position = $_position23;
                $this->cut = $_cut24;

                if ($_success) {
                    $_value25[] = $this->value;

                    if ($this->position < strlen($this->string)) {
                        $_success = true;
                        $this->value = substr($this->string, $this->position, 1);
                        $this->position += 1;
                    } else {
                        $_success = false;
                    }
                }

                if ($_success) {
                    $_value25[] = $this->value;

                    $this->value = $_value25;
                }

                if (!$_success) {
                    break;
                }

                $_value27[] = $this->value;
            }

            if (!$this->cut) {
                $_success = true;
                $this->position = $_position26;
                $this->value = $_value27;
            }

            $this->cut = $_cut28;
        }

        if ($_success) {
            $this->value = strval(substr($this->string, $_position29, $this->position - $_position29));
        }

        if ($_success) {
            $str = $this->value;
        }

        if ($_success) {
            $this->value = call_user_func(function () use (&$str) {
                return $str;
            });
        }

        $this->cache['UNQUOTED_STRING'][$_position] = array(
            'success' => $_success,
            'position' => $this->position,
            'value' => $this->value
        );

        if (!$_success) {
            $this->report($_position, 'UNQUOTED_STRING');
        }

        return $_success;
    }

    private function line()
    {
        if (!empty($this->errors)) {
            $positions = array_keys($this->errors);
        } else {
            $positions = array_keys($this->warnings);
        }

        return count(explode("\n", substr($this->string, 0, max($positions))));
    }

    private function rest()
    {
        return '"' . substr($this->string, $this->position) . '"';
    }

    protected function report($position, $expecting)
    {
        if ($this->cut) {
            $this->errors[$position][] = $expecting;
        } else {
            $this->warnings[$position][] = $expecting;
        }
    }

    private function expecting()
    {
        if (!empty($this->errors)) {
            ksort($this->errors);

            return end($this->errors)[0];
        }

        ksort($this->warnings);

        return implode(', ', end($this->warnings));
    }

    public function parse($_string)
    {
        $this->string = $_string;
        $this->position = 0;
        $this->value = null;
        $this->cache = array();
        $this->cut = false;
        $this->errors = array();
        $this->warnings = array();

        $_success = $this->parseDESC();

        if ($_success && $this->position < strlen($this->string)) {
            $_success = false;

            $this->report($this->position, "end of file");
        }

        if (!$_success) {
            throw new \InvalidArgumentException("Syntax error, expecting {$this->expecting()} on line {$this->line()}");
        }

        return $this->value;
    }
}