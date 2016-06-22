<?php
use App\Extensions\MultiUserAuth\SpecificRbacConst;

return [
    
    /**
     * 权限组-分布图
     *
     * @var unknown
     */
    'group_map' => [
        [
            'name' => '人事服务',
            'child' => [
                [
                    'name' => '员工管理',
                    'code' => SpecificRbacConst::GROUP_EMPLOYEE_MANAGE
                ],
                [
                    'name' => '福豆管理',
                    'code' => SpecificRbacConst::GROUP_FUDOU_MANAGE
                ]
            ]
        ],
        [
            'name' => '设置',
            'child' => [
                [
                    'name' => '操作员管理',
                    'code' => SpecificRbacConst::GROUP_OPERATOR_MANAGE
                ],
                [
                    'name' => '系统管理',
                    'code' => SpecificRbacConst::GROUP_SYSTEM_MANAGE
                ]
            ]
        ],
        [
            'name' => '工资代发',
            'child' => [
                [
                    'name' => '薪资发放',
                    'code' => SpecificRbacConst::GROUP_SALARY_MANAGE
                ],
                [
                    'name' => '薪资报表',
                    'code' => SpecificRbacConst::GROUP_SALARY_STATISTICS
                ]
            ]
        ]
    ],
    
    /**
     * 角色-权限分布
     *
     * @var unknown
     */
    'role_map' => [
        SpecificRbacConst::ROLE_FINANCE => [
            'name' => '财务',
            'actions' => [
                SpecificRbacConst::GROUP_SALARY_MANAGE,
                SpecificRbacConst::GROUP_SALARY_STATISTICS
            ]
        ],
        SpecificRbacConst::ROLE_HR => [
            'name' => 'HR',
            'actions' => [
                SpecificRbacConst::GROUP_EMPLOYEE_MANAGE,
                SpecificRbacConst::GROUP_FUDOU_MANAGE
            ]
        ],
        SpecificRbacConst::ROLE_MANAGER => [
            'name' => '管理员',
            'actions' => [
                SpecificRbacConst::GROUP_OPERATOR_MANAGE,
                SpecificRbacConst::GROUP_SYSTEM_MANAGE
            ]
        ]
    ],
    'sidebar' => [
        [
            'name' => '企业概况',
            'icon' => 'icon-overview',
            'child' => [
                [
                    'name' => '账户总览',
                    'url' => 'overview',
                    'icon' => 'icon-account'
                ],
                [
                    'name' => '修改密码',
                    'url' => 'changepwd',
                    'icon' => 'icon-pwd'
                ],
                [
                    'name' => '管理员管理',
                    'url' => 'operator_list',
                    'icon' => 'fa-cog'
                ] // 'icon-operator'

            ]
        ],
        [
            'name' => '人事服务',
            'icon' => ' fa-user',
            'child' => [
                [
                    'name' => '员工管理',
                    'url' => 'employee_list',
                    'icon' => 'fa-users'
                ],
                [
                    'name' => '福豆管理',
                    'url' => 'fudou',
                    'icon' => 'fa-users'
                ]
            ]
        ],
        [
            'name' => '薪资发放',
            'icon' => 'icon-finance',
            'child' => [
                [
                    'name' => '薪资发放管理',
                    'url' => 'wage',
                    'icon' => 'icon-salary'
                ],
                [
                    'name' => '薪资报表',
                    'url' => 'statistics',
                    'icon' => 'fa-bar-chart'
                ]
            ]
        ]
    ]
];