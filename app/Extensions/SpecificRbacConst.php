<?php
namespace App\Http\Middleware;

class SpecificRbacConst
{

    /**
     * 权限群组-员工管理
     *
     * @var unknown
     */
    const GROUP_EMPLOYEE_MANAGE = 0b1;

    /**
     * 权限群组-薪资报表
     *
     * @var unknown
     */
    const GROUP_SALARY_STATISTICS = 0b10;

    /**
     * 权限群组-薪资发放管理
     *
     * @var unknown
     */
    const GROUP_SALARY_MANAGE = 0b100;

    /**
     * 权限群组-福豆管理
     *
     * @var unknown
     */
    const GROUP_FUDOU_MANAGE = 0b1000;

    /**
     * 权限群组-操作员管理
     *
     * @var unknown
     */
    const GROUP_OPERATOR_MANAGE = 0b10000;

    /**
     * 权限群组-系统管理
     *
     * @var unknown
     */
    const GROUP_SYSTEM_MANAGE = 0b100000;

    /**
     * 角色-HR
     * 
     * @var unknown
     */
    const ROLE_HR = 1;

    /*
     * 角色-财务
     * @var unknown
     */
    const ROLE_FINANCE = 2;

    /**
     * 角色-管理员
     * 
     * @var unknown
     */
    const ROLE_MANAGER = 3;

    public static function getRoleName($role_id)
    {
        $roleMap = \Config::get('role.role_map');
        
        return isset($roleMap[$role_id]) ? $roleMap[$role_id]['name'] : 'Unkone';
    }
}



