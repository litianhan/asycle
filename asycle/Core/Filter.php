<?php

namespace Asycle\Core;
/**
 * 参数过滤器
 * Date: 2017/5/10
 * Time: 14:23
 */
class Filter
{
    /**
     * 当前过滤的参数key
     * @var null
     */
    protected $currentKey = null;
    /**
     * 当前过滤的参数值
     * @var null
     */
    protected $currentValue = null;
    /**
     * 输入参数
     * @var array
     */
    protected $input = [];
    /**
     * 过滤成功后输出的参数
     * @var array
     */
    protected $output = [];
    /**
     * 是否有错误
     * @var bool
     */
    protected $error  = false;
    /**
     * 第一个错误的key
     * @var null
     */
    protected $firstErrorKey = null;
    /**
     * 错误消息，key=>msg
     * @var array
     */
    protected $errorMsg = [];
    /**
     * 首次发行错误后是否停止过滤
     * @var bool
     */
    protected $onlyFirstError = true;
    public function __construct(array $input = [])
    {
        $this->input = $input;
    }
    public function reset(array $input = []){
        $this->input = $input;
        $this->currentKey = null;
        $this->currentValue = null;
        $this->errorMsg = [];
        $this->error = false;
        $this->firstErrorKey= null;
        $this->output = [];
        return $this;
    }

    /**
     * 添加错误信息
     * @param $key
     * @param $errorMsg
     */
    protected function addError($key,$errorMsg){
        if( ! $this->onlyFirstError){
            $this->error = true;
            $this->errorMsg [$key]= $errorMsg;
        }elseif( ! $this->error){
            $this->error = true;
            $this->firstErrorKey = $key;
            $this->errorMsg [$key]= $errorMsg;
        }
    }

    /**
     * 允许保存多次错误
     * @param bool $enable
     * @return $this
     */
    public function multiError($enable = true){
        $this->onlyFirstError = ! $enable;
        return $this;
    }

    /**
     * 返回当前过滤的值，如果校验失败返回null
     * @return null
     */
    public function fetchCurrentKey(){
        if(isset($this->errorMsg[$this->currentKey])){
            return null;
        }
        return $this->currentValue;

    }

    /**
     *返回所有已经校验的key=>value数组
     * @return array
     */
    public function fetchAll(){
        if($this->error){
            return [];
        }
        if( ! empty($this->currentKey)){
            $this->output[$this->currentKey] = $this->currentValue;
            $this->currentValue = null;
            $this->currentKey = null;
        }
        return $this->output;
    }

    /**
     * 设置当前过滤的必需的key
     * @param $key
     * @param string $errorMsg
     * @return $this
     */
    public function requiredKey($key,$errorMsg = null){
        if($this->error and $this->onlyFirstError){
            return $this;
        }
        if(empty($key)){
            throw new \InvalidArgumentException('Invalid key.');
        }
        if(! empty($this->currentKey)){
            $this->output[$this->currentKey] = $this->currentValue;
        }
        if( ! isset($this->input[$key])){
            $this->addError($key,$errorMsg ?? $key.' is required.');
            return $this;
        }
        $this->currentKey = $key;
        $this->currentValue = $this->input[$key];
        return $this;
    }
    /**
     * 设置当前过滤的key
     * @param $key
     * @param null $default
     * @return $this
     */
    public function includeKey($key,$default){
        if($this->error and $this->onlyFirstError){
            return $this;
        }
        if(empty($key)){
            throw new \InvalidArgumentException('Invalid key.');
        }
        if(! empty($this->currentKey)){
            $this->output[$this->currentKey] = $this->currentValue;
        }

        $this->currentKey = $key;
        $this->currentValue = $this->input[$key] ?? $default;
        return $this;
    }

    /**
     * 当前key对应的值是整数
     * @param null $min
     * @param null $max
     * @param string $errorMsg
     * @return $this
     */
    public function isInteger($min = null,$max = null,$errorMsg = null){
        if($this->error and $this->onlyFirstError){
            return $this;
        }
        if( ! is_numeric($this->currentValue)){
            $this->addError($this->currentKey,$errorMsg ?? $this->currentKey.' is not numeric.');
            return $this;
        }
        if(! ctype_digit(ltrim($this->currentValue,'-'))){

            $this->addError($this->currentKey,$errorMsg ?? $this->currentKey.' is not integer.');
            return $this;
        }
        $this->currentValue = intval($this->currentValue);
        if(! is_null($min) and $this->currentValue < $min){
            $this->addError($this->currentKey,$errorMsg ?? $this->currentKey.' is smaller than min.');
        }
        if(! is_null($max) and $this->currentValue > $max){
            $this->addError($this->currentKey,$errorMsg ?? $this->currentKey.' is bigger than min.');
        }
        return $this;
    }

    /**
     * 当前key对应的值是浮点数
     * @param null $min
     * @param null $max
     * @param string $errorMsg
     * @return $this
     */
    public function isFloat($min = null,$max = null,$errorMsg = null){
        if($this->error and $this->onlyFirstError){
            return $this;
        }
        if( ! is_numeric($this->currentValue)){
            $this->addError($this->currentKey,$errorMsg ?? $this->currentKey.' is not numeric.');
            return $this;
        }
        $this->currentValue = floatval($this->currentValue);
        if(! is_null($min) and $this->currentValue < $min){
            $this->addError($this->currentKey,$errorMsg ?? $this->currentKey.' is smaller than min.');
        }
        if(! is_null($max) and $this->currentValue > $max){
            $this->addError($this->currentKey,$errorMsg ?? $this->currentKey.' is bigger than min.');
        }
        return $this;
    }

    /**
     * 当前key对应的值是字符串
     * @param null $minLength
     * @param null $maxLength
     * @param string $errorMsg
     * @return $this
     */
    public function isString($minLength = null,$maxLength = null,$errorMsg = null){
        if($this->error and $this->onlyFirstError){
            return $this;
        }
        if( ! is_string($this->currentValue)){
            $this->addError($this->currentKey,$errorMsg ?? $this->currentKey.' is not string.');
            return $this;
        }
        $len = strlen($this->currentValue);
        if(! is_null($minLength) and $len < $minLength){
            $this->addError($this->currentKey,$errorMsg ?? $this->currentKey.' is shorter than min.');
            return $this;
        }
        if(! is_null($maxLength) and $len > $maxLength){
            $this->addError($this->currentKey,$errorMsg ?? $this->currentKey.' is longer than min.');
            return $this;
        }
        return $this;
    }

    /**
     * 返回所有错误
     * @return array
     */
    public function getErrors(){
        return $this->errorMsg;
    }

    /**
     * 返回第一个错误的key
     * @return null
     */
    public function getFirstErrorKey(){
        return $this->firstErrorKey;
    }

    /**
     * 返回第一个错误信息
     * @return mixed|null
     */
    public function getFirstErrorMsg(){
        if($this->error){
            return current($this->errorMsg);
        }
        return null;
    }

    /**
     * 是否校验失败
     * @return bool
     */
    public function hasError(){
        return $this->error;
    }

    /**
     * 当前key对应的值是邮箱
     * @param string $errorMsg
     * @return $this
     */
    public function isEmail($errorMsg = null)
    {
        if($this->error and $this->onlyFirstError){
            return $this;
        }
        if (filter_var($this->currentValue, FILTER_VALIDATE_EMAIL) === false) {
            $this->addError($this->currentKey,$errorMsg ?? $this->currentKey.' is invalid email.');
        }
        return $this;
    }

    /**
     * 当前key对应的值是用户名，字母开头，数字、字母和_-组合的字符串
     * @param int $minLength
     * @param int $maxLength
     * @param string $errorMsg
     * @return $this
     */
    public function isUsername(int $minLength = 6, int $maxLength = 32,$errorMsg = null)
    {
        $name = &$this->currentValue;
        if (empty($name)) {
            $this->addError($this->currentKey,$errorMsg);
            return $this;
        }
        --$minLength;
        if ($minLength < 0) {
            $minLength = 0;
        }
        if ($maxLength < $minLength) {
            $maxLength = $minLength;
        }
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_-]{' . $minLength . ',' . $maxLength . '}$/', $name)) {
            $this->addError($this->currentKey,$errorMsg ?? $this->currentKey.' is invalid username.');
            return $this;
        }
        return $this;
    }

    /**
     * 当前key对应的值是密码，特殊字符和数字字母组合的字符串
     * @param int $minLength
     * @param int $maxLength
     * @param string $errorMsg
     * @return $this
     */
    public function isPassword(int $minLength = 6, $maxLength = 32,$errorMsg = null)
    {

        $pwd = &$this->currentValue;
        if ($minLength < 0) {
            $minLength = 0;
        }
        if ($maxLength < $minLength) {
            $maxLength = $minLength;
        }
        $match = '/^[\\~!@#$%^&*()-_=+|{}\[\],.?\/:;\'\"\d\w]{' . $minLength . ',' . $maxLength . '}$/';
        $v = trim($pwd);
        if (empty($v) or preg_match($match, $v) === 0) {
            $this->addError($this->currentKey,$errorMsg ?? $this->currentKey.' is invalid password.');
            return $this;
        }
        return $this;
    }

    /**
     * 当前key对应的值是Hash字符串，16进制字符串
     * @param int $minLength
     * @param int $maxLength
     * @param string $errorMsg
     * @return $this
     */
    public function isHashString(int $minLength = 0, int $maxLength = 0,$errorMsg = null)
    {
        if($this->error and $this->onlyFirstError){
            return $this;
        }
        $hash = &$this->currentValue;
        if ($minLength === 0 and $maxLength === 0) {
            $match = '/^[a-fA-F0-9]*$/';
        } else {
            $match = '/^[a-fA-F0-9]{' . $minLength . ',' . $maxLength . '}$/';
        }
        if( ! preg_match($match, $hash) !== 0){
            $this->addError($this->currentKey,$errorMsg ?? $this->currentKey.' is invalid hash string.');
        }
        return $this;
    }

    /**
     * 当前key对应的值是URL
     * @param string $errorMsg
     * @return $this
     */
    public function isURL($errorMsg = null)
    {
        if($this->error and $this->onlyFirstError){

            return $this;
        }
        $url = &$this->currentValue;
        if (empty($url)) {
            $this->addError($this->currentKey,$errorMsg ?? $this->currentKey.' is invalid url.');
            return $this;
        }
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            $this->addError($this->currentKey,$errorMsg ?? $this->currentKey.' is invalid url.');
            return $this;
        }
        return $this;
    }

    /**
     * 当前key对应的值是手机号码
     * @param array $allowedPrefix
     * @param string $errorMsg
     * @return $this
     */
    public function isMobilePhone(array $allowedPrefix = [],$errorMsg = null)
    {
        if($this->error and $this->onlyFirstError){
            return $this;
        }
        $number = &$this->currentValue;
        if ( ! preg_match('/^1([0-9]{10})$/', $number)) {
            $this->addError($this->currentKey,$errorMsg ?? $this->currentKey.' is invalid mobile phone.');
            return $this;
        }
        if( ! empty($allowedPrefix)){
            $allowed = implode('|',$allowedPrefix);
            $preg = '/^(' .$allowed . ')/' ;
            if ( ! preg_match($preg, $number)) {
                $this->addError($this->currentKey,$errorMsg ?? $this->currentKey.' is not in allowed prefix.');
            }
        }
        return $this;
    }

    /**
     * 去除字符串中不可见的字符
     * @param $str
     * @param bool $urlEncoded
     * @return mixed
     */
    protected  function removeInvisibleChar($str, $urlEncoded = true)
    {
        $invisible = [];
        if ($urlEncoded) {
            $invisible[] = '/%0[0-8bcef]/i';    // url encoded 00-08, 11, 12, 14, 15
            $invisible[] = '/%1[0-9a-f]/i';    // url encoded 16-31
        }
        $invisible[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';    // 00-08, 11, 12, 14-31, 127
        do {
            $str = preg_replace($invisible, '', $str, -1, $count);
        } while ($count);
        return $str;
    }

    /**
     * 过滤文件名
     */
    public function sanitizeFilename()
    {
        $str = &$this->currentValue;
        $bad = [
            '/', './', '../', '<!--', '-->', '<', '>',
            "'", '"', '&', '$', '#', '*',
            '{', '}', '[', ']', '=',
            ';', '?', '%20', '%22',
            '%3c',        // <
            '%253c',    // <
            '%3e',        // >
            '%0e',        // >
            '%28',        // (
            '%29',        // )
            '%2528',    // (
            '%26',        // &
            '%24',        // $
            '%3f',        // ?
            '%3b',        // ;
            '%3d'        // =
        ];
        $str = $this->removeInvisibleChar($str, false);

        do {
            $old = $str;
            $str = str_replace($bad, '', $str);
        } while ($old !== $str);

        $str = stripslashes($str);
        return $this;
    }

    /**
     * xss过滤
     */
    public function xssClean()
    {
        $val = &$this->currentValue;
        // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
        // this prevents some character re-spacing such as <java\0script>
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
        $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

        // straight replacements, the user should never need these since they're normal characters
        // this prevents like <IMG SRC=@avascript:alert('XSS')>
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';
        for ($i = 0; $i < strlen($search); $i++) {
            // ;? matches the ;, which is optional
            // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

            // @ @ search for the hex values
            $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[ $i ])) . ';?)/i', $search[ $i ], $val); // with a ;
            // @ @ 0{0,7} matches '0' zero to seven times
            $val = preg_replace('/(&#0{0,8}' . ord($search[ $i ]) . ';?)/', $search[ $i ], $val); // with a ;
        }

        // now the only remaining whitespace attacks are \t, \n, and \r
        $ra1 = ['javascript', 'vbscript', 'expression', 'applet',
            'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed',
            'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer',
            'bgsound', 'title', 'base'];
        $ra2 = ['onabort', 'onactivate', 'onafterprint', 'onafterupdate',
            'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate',
            'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload',
            'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick',
            'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable',
            'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag',
            'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop',
            'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin',
            'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete',
            'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove',
            'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend',
            'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset',
            'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete',
            'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart',
            'onstop', 'onsubmit', 'onunload'];
        $ra = array_merge($ra1, $ra2);

        $found = true; // keep replacing as long as the previous round replaced something
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[ $i ]); $j++) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                        $pattern .= '|';
                        $pattern .= '|(&#0{0,8}([9|10|13]);)';
                        $pattern .= ')*';
                    }
                    $pattern .= $ra[ $i ][ $j ];
                }
                $pattern .= '/i';
                $replacement = substr($ra[ $i ], 0, 2) . '<x>' . substr($ra[ $i ], 2); // add in <> to nerf the tag
                $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
                if ($val_before == $val) {
                    // no replacements were made, so exit the loop
                    $found = false;
                }
            }
        }
        return $this;
    }
}