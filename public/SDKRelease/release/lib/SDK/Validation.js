define(['Utils'],function(Utils) {
    var iceValidator = function(inputData,options){
        var configuration = {
            'data' : inputData,
//            'rules',
            'messages' : {
                'accessKey' : {
                    'required' : '请填写开发者 AccessKey '
                }
            },
//            'attributes',
//            'valueNames',
//            'config',
            'methods' : {
                'getAttributeValue' : function(key){
                    return Utils.array_get(inputData,['fields',key,'value'],'');
                },
                'getAttributeName' : function(key){
                    return Utils.array_get(inputData,['fields',key,'name'],key);
                },
                'getCustomErrorMessage' : function(key,rule){
                    return Utils.array_get(configuration,['messages',key,rule]);
                },
            },
            'extend' : function(configs) {
                for ( var i in configs) {
                    if (configs[i] instanceof Object) {
                        for ( var j in configs[i]) {
                            if (configs[i][j] instanceof Object) {
                                for ( var k in configs[i][j]) {
                                    this[i] = this[i] ? this[i] : {};
                                    this[i][j] = this[i][j] ? this[i][j] : {};
                                    this[i][j][k] = configs[i][j][k];
                                }
                            } else {
                                this[i] = this[i] ? this[i] : {};
                                this[i][j] = configs[i][j];
                            }
                        }
                    } else {
                        this[i] = configs[i] ;
                    }
                }
            }
        };
        
        options ? configuration.extend(options) : '';
        
        console.log(configuration.methods.getAttributeName('accessKey'));
        
        function validator (rule,paramArray,value){
            // 1、简单的正则
            // 2、function ，  可能需要其他字段的值，做 same confirm 等验证，required_if 等需要多个其他字段的值
            //    将整个数据内容 作为参数构建 验证器 ，可设置 get/set 方法来获取 表单数据值 ，名称，自定义错误信息，字段描述等信息 
            // 3、验证 消息获取 效果展示分离，作为 opts 传入，加入自定义统一处理器和允许单独制定处理器
            var validators = {
                'alpha' : '/^[a-zA-Z]+$/',
                'alpha_dash' : '/^\w+$/',
                'alpha_num' :  '/^[a-zA-Z0-9]+$/',
                'between_numeric' : function(value,parameters){
                    var v = parseInt(value);
                    return v > parameters[0] && v < parameters[1];
                },
                'between_string' : function(value,parameters){
                    var v = value.length;
                    return v > parameters[0] && v < parameters[1];
                },
                'boolean' : function(value,parameters){
                    return typeof value == 'boolean';
                },
                'confirmed' : function(value,parameters){
                    var confirmedKey = parameters[0];
                    return value == configuration.methods.getAttributeValue(confirmedKey)
                },
                'date' : '/^\d{4}-\d{1,2}-\d{1,2}$/',
                'date_format' : '{0} 的格式必须为 {1}。',
                'different' : '{0} 和 {1} 必须不同。',
                'digits' : function(value,parameters){
                    var regex = new RegExp("/^[1-9]\d{"+(parameters[0] - 1)+"}$/");
                    return regex.text(value);
                },
                'digits_between' : function(value,parameters){
                    var regex = new RegExp("/^[1-9]\d*$/");
                    var length = (value + '').length;
                    return regex.text(value) && ( length > parameters[0] && length < parameters[1]);
                },
                'email' : '/^(\w)+(\.\w+)*@(\w)+((\.\w{2,3}){1,3})$/',
                'filled' : '/^[^\s]+$/',
                'in' : function(value,parameters){
                    for(var i in parameters){
                        if(value == parameters[i]){
                            return true;
                        }
                    }
                    return false;
                },
                'integer' : '/^[1-9]\d*$/',
                'ip' : '/^\d+\.\d+\.\d+\.\d+$/',
                'max_numeric' : '{0} 不能大于 {1}。',
                'max_string' : '{0} 不能大于 {1} 个字符。',
                'min_numeric' : '{0} 必须大于等于 {1}。',
                'min_string' : '{0} 至少为 {1} 个字符。',
                'not_in' : function(value,parameters){
                    for(var i in parameters){
                        if(value == parameters[i]){
                            return false;
                        }
                    }
                    return true;
                },
                'numeric' : '{0} 必须是一个数字。',
                'regex' : '{0} 格式不正确。',
                'required' : '{0} 不能为空。',
                'required_if' : '当 {1} 为 {2} 时 {0} 不能为空。',
                'required_unless' : 'The {0} field is required unless {1} is in {2}.',
                'required_with' : '当 {1} 存在时 {0} 不能为空。',
                'required_with_all' : '当 {1} 存在时 {0} 不能为空。',
                'required_without' : '当 {1} 不存在时 {0} 不能为空。',
                'required_without_all' : '当 {1} 都不存在时 {0} 不能为空。',
                'same' : '{0} 和 {1} 必须相同。',
                'size_string' : '{0} 必须是 {1} 个字符。',
                'url' : '/^[a-zA-z]+://[^\s]* 或 ^http://([\w-]+\.)+[\w-]+(/[\w-./?%&=]*)?$/',
            };
            var vdt ;
            if(vdt = validators[rule]){
                if(typeof vdt == 'function'){
                    // 函数 rule,paramArray,value
                    return validators[rule](value,paramArray);
                }else{
                    // 正则
                    return (new RegExp(validators[rule])).test(value);
                }
            }
            throw Error('Rule ' + rule + ' Not Found!');
        }
        
        function renderErrorMessage (rule,params,message){
            
            // 字段名替换 、valueName 替换、验证规则参数替换
            // params 0 = attr 1 - ~ rule config
            var validateMessage = {
                'alpha' : '{0} 只能由字母组成。',
                'alpha_dash' : '{0} 只能由字母、数字和斜杠组成。',
                'alpha_num' : '{0} 只能由字母和数字组成。',
                'between_numeric' : '{0} 必须介于 {1} - {2} 之间。',
                'between_string' : '{0} 必须介于 {1} - {3} 个字符之间。',
                'boolean' : '{0} 必须为布尔值。',
                'confirmed' : '{0} 两次输入不一致。',
                'date' : '{0} 不是一个有效的日期。',
                'date_format' : '{0} 的格式必须为 {1}。',
                'different' : '{0} 和 {1} 必须不同。',
                'digits' : '{0} 必须是 {1} 位的数字。',
                'digits_between' : '{0} 必须是介于 {1} 和 {2} 位的数字。',
                'email' : '{0} 不是一个合法的邮箱。',
                'filled' : '{0} 不能为空。',
                'in' : '已选的属性 {0} 非法。',
                'integer' : '{0} 必须是整数。',
                'ip' : '{0} 必须是有效的 IP 地址。',
                'max_numeric' : '{0} 不能大于 {1}。',
                'max_string' : '{0} 不能大于 {1} 个字符。',
                'min_numeric' : '{0} 必须大于等于 {1}。',
                'min_string' : '{0} 至少为 {1} 个字符。',
                'not_in' : '已选的属性 {0} 非法。',
                'numeric' : '{0} 必须是一个数字。',
                'regex' : '{0} 格式不正确。',
                'required' : '{0} 不能为空。',
                'required_if' : '当 {1} 为 {2} 时 {0} 不能为空。',
                'required_unless' : 'The {0} field is required unless {1} is in {2}.',
                'required_with' : '当 {1} 存在时 {0} 不能为空。',
                'required_with_all' : '当 {1} 存在时 {0} 不能为空。',
                'required_without' : '当 {1} 不存在时 {0} 不能为空。',
                'required_without_all' : '当 {1} 都不存在时 {0} 不能为空。',
                'same' : '{0} 和 {1} 必须相同。',
                'size_string' : '{0} 必须是 {1} 个字符。',
                'url' : '{0} 格式不正确。',
            };
            if(undefined !== message){
                for(var i in params){
                    message.replace('{' + i + '}',params[i]);
                }
                return message;
            }
            var msg = 'Some Thing Is Wrong Here!';
            if(undefined !== (msg = validateMessage[rule])){
                for(var i in params){
                    msg.replace('{' + i + '}',params[i]);
                }
            }
            return msg;
        }
        
        
        function doValidate(key,value,validateConfigs,onError,onSuccess){
            var rules = validateConfigs.split('|');
            // bail 写在开头表示遇到错误 停止继续验证
            var bail = validateConfigs.indexOf('bail') === 0 ? true : false;
            var success = true;
            for(var rule in rules){
                var segs = rules[rule].split(':');
                var params = [];
                if(segs.length == 2){
                    params = segs[1].split(',');
                }
                
                var ret = validator (segs[0],params,value)
                
                if(false === ret){
                    // getErrorMsg 
                    var renderParams = params;
                    // 加入字段名称
                    renderParams.unshift(key);
                    var errorMsg = renderErrorMessage(segs[0],renderParams);
                    // showErrorMsg 显示错误信息
                    success = false;
                    if(true === bail){
                        // 遇到错误停止验证
                        
                        break;   
                    }
                }
            }
            return success;
        }
        
//      validateFormData : function(){
//      var validateData = {};
//      for(var key in this.fields){
//          var validateConfig = this.fields[key]['validate'];
//          if(undefined !== validateConfig){
//              var valid = iceValidator.doValidate(key,this.fields[key]['value'],validateConfig['rules']);
//          }
//      }
//  },
        
        
        return {
            doValidate : doValidate
        };
    };
    return iceValidator;
});
