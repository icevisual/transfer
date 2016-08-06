<?php
namespace App\Models\Common;

class RequestLog extends \Eloquent
{

    protected $table = 'request_log';

    protected $primaryKey = 'id';

    protected $guarded = [];
    
    /**
     * Custom format for the last login date
     *
     * @param $value
     * @return string
     */
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d.m.Y.');
    }
    
    public static function getTableCreateStatment(){
        return $str = "
CREATE TABLE `x_request_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(100) NOT NULL COMMENT 'IP',
  `uri` varchar(100) DEFAULT NULL COMMENT '路由',
  `params` text COMMENT '参数',
  `time_usage` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '时间使用量S',
  `memory_usage` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '内存使用量MB',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='请求日志';
            ";
    }
}