<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Company\CompanyBill;

class BalanceEvent extends Event
{
    use SerializesModels;
    
    public $description = 'Account Balance Change Event';
    
    protected $company_id;
    
    protected $event_type;
    
    protected $relation_id;
    
    protected $amount;
    
    public function getEventInfo(){
        if($this->event_type == CompanyBill::EVENT_SALARY_PAY){
            return CompanyBill::getEventName( $this->event_type,[$this->relation_id]);
        }else{
            return CompanyBill::getEventName( $this->event_type);
        }
    }
    
    
    public function getRecordData(){
        $eventInfo = $this->getEventInfo();
        $data = [
            'company_id' => $this->company_id,
            'amount' => $this->amount,
            'inout' => $eventInfo['inout'],
            'event_type' => $this->event_type,
            'event_name' => $eventInfo['name'],
            'relation_id' => $this->relation_id,
        ];
        return $data;
    }
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($company_id,$event_type,$amount,$relation_id = 0 )
    {
        $this->company_id = $company_id;
        $this->event_type = $event_type;
        $this->amount = $amount;
        $this->relation_id = $relation_id;
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
