<?php
namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PasswordModificationEvent extends Event
{
    use SerializesModels;

    /**
     * 变更密码类型，创建账户
     * 
     * @var unknown
     */
    const CG_WAY_CREATE = 1;

    /**
     * 变更密码类型，重设密码
     * 
     * @var unknown
     */
    const CG_WAY_RESET = 2;

    /**
     * 变更密码类型，忘记密码
     * 
     * @var unknown
     */
    const CG_WAY_FORGET = 3;

    /**
     * 变更密码类型，主动修改
     * 
     * @var unknown
     */
    const CG_WAY_UPDATE = 4;

    protected $_event_data = [];

    /**
     *
     * @param unknown $data<pre>
     *            [
     *            'way' => '',
     *            'account_id' => ''
     *            ]</pre>
     */
    public function __construct($data)
    {
        $this->setEventData($data);
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
