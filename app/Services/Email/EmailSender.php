<?php
namespace App\Services\Email;

class EmailSender
{

    const EMAIL_PASSWRD = 0;

    const EMAIL_PASSWRD_RESET = 1;

    /**
     *
     * @var AliyunEmail
     */
    protected $_handler;

    protected $_driver = 'aliyuncs';

    protected $_template = [
        self::EMAIL_PASSWRD => [
            'subject' => '欢迎使用薪福多平台！',
            'view' => 'email.password'
        ],
        self::EMAIL_PASSWRD_RESET => [
            'subject' => '您正在申请重置薪福多密码',
            'view' => 'email.passwordReset'
        ]
    ];

    public function __construct()
    {
        $config = \Config::get('mail.' . $this->_driver);
        $AccessKeySecret = $config['AccessKeySecret'];
        $AccessKeyId = $config['AccessKeyId'];
        $AccountName = $config['AccountName'];
        $this->_handler = new AliyunEmail($AccountName, $AccessKeyId, $AccessKeySecret);
    }
    
    public function hasTemplate($template){
        return isset($this->_template[$template]);
    }
    
    public function getTemplateInfo($template){
        return $this->_template[$template];
    }
    
    public function getEmailSubjectContent($template, $data)
    {
        if ($this->hasTemplate($template)) {
            $templateInfo = $this->getTemplateInfo($template);
            // TODO : Check Template Paramaters
            return [
                'Subject' => $templateInfo['subject'],
                'Content' => \View::make($templateInfo['view'], $data)->__toString()
            ];
        } else {
            throw new \App\Exceptions\ServiceException('No Such Template');
        }
    }

    public function sendEmail($ToAddress, $template, $param)
    {
        $param['ToAddress'] = $ToAddress;
        $SubjectContent = $this->getEmailSubjectContent($template, $param);
        return $this->_handler->SingleSendMail($ToAddress, $SubjectContent['Subject'], $SubjectContent['Content']);
    }
}