<?php
return array(
    
      /* 默认设定 */
    'DEFAULT_M_LAYER'       =>  'Model', // 默认的模型层名称
    'DEFAULT_C_LAYER'       =>  'Action', // 默认的控制器层名称
    'DEFAULT_APP'           => '@',     // 默认项目名称，@表示当前项目
    'DEFAULT_LANG'          => 'zh-cn', // 默认语言
    'DEFAULT_THEME'         => '',	// 默认模板主题名称
   // 'DEFAULT_GROUP'         => 'Home',  // 默认分组
    'DEFAULT_MODULE'        => 'Rest', // 默认模块名称
    'DEFAULT_ACTION'        => 'index', // 默认操作名称
    'DEFAULT_CHARSET'       => 'utf-8', // 默认输出编码
    'DEFAULT_TIMEZONE'      => 'PRC',	// 默认时区
    'DEFAULT_AJAX_RETURN'   => 'JSON',  // 默认AJAX 数据返回格式,可选JSON XML ...
    'DEFAULT_JSONP_HANDLER' => 'jsonpReturn', // 默认JSONP格式返回的处理方法
    'DEFAULT_FILTER'        => 'htmlspecialchars', // 默认参数过滤方法 用于 $this->_get('变量名');$this->_post('变量名')...
    
     /* SESSION设置 */
    'SESSION_AUTO_START'    => false,    // 是否自动开启Session
    
     /* 错误设置 */
    'ERROR_MESSAGE'         => '页面错误！请稍后再试～',//错误显示信息,非调试模式有效
    'ERROR_PAGE'            => 'Common:error',	// 错误定向页面
    'SHOW_ERROR_MSG'        => false,    // 显示错误信息
    'TRACE_EXCEPTION'       => false,   // TRACE错误信息是否抛异常 针对trace方法 
    
    /* 模板引擎设置 */
    'TMPL_CONTENT_TYPE'     => 'text/html', // 默认模板输出类型
    'TMPL_ACTION_ERROR'     => 'Common:error', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   => THINK_PATH.'Tpl/dispatch_jump.tpl', // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE'   => THINK_PATH.'Tpl/think_exception.tpl',// 异常页面的模板文件
    'TMPL_DETECT_THEME'     => false,       // 自动侦测模板主题
    'TMPL_TEMPLATE_SUFFIX'  => '.html',     // 默认模板文件后缀
    'TMPL_FILE_DEPR'        =>  '/', //模板文件MODULE_NAME与ACTION_NAME之间的分割符
    
     /* URL设置 */
    'URL_CASE_INSENSITIVE'  => true,   // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'             => 2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
    // 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE  模式); 3 (兼容模式)  默认为PATHINFO 模式，提供最好的用户体验和SEO支持
    'URL_PATHINFO_DEPR'     => '/',	// PATHINFO模式下，各参数之间的分割符号
    'URL_PATHINFO_FETCH'    =>   'ORIG_PATH_INFO,REDIRECT_PATH_INFO,REDIRECT_URL', // 用于兼容判断PATH_INFO 参数的SERVER替代变量列表
    'URL_HTML_SUFFIX'       => '',  // URL伪静态后缀设置
    'URL_PARAMS_BIND'       =>  true, // URL变量绑定到Action方法参数
    'URL_404_REDIRECT'      =>  '', // 404 跳转页面 部署模式有效
    
     /* 数据库设置 */
    'DB_TYPE'               => 'mysql',     // 数据库类型
    'DB_HOST'               => 'localhost', // 服务器地址
    'DB_NAME'               => 'ordering',          // 数据库名
    'DB_USER'               => 'root',      // 用户名
    'DB_PWD'                => '',          // 密码
  //  'DB_PORT'               => '',        // 端口
    'DB_PREFIX'             => 'o_',    // 数据库表前缀
    'DB_FIELDTYPE_CHECK'    => false,       // 是否进行字段类型检查
    'DB_FIELDS_CACHE'       => true,        // 启用字段缓存
    'DB_CHARSET'            => 'utf8',      // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'        => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'        => false,       // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'         => 1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO'           => '', // 指定从服务器序号
    'DB_SQL_BUILD_CACHE'    => false, // 数据库查询的SQL创建缓存
    'DB_SQL_BUILD_QUEUE'    => 'file',   // SQL缓存队列的缓存方式 支持 file xcache和apc
    'DB_SQL_BUILD_LENGTH'   => 20, // SQL缓存的队列长度
    'DB_SQL_LOG'            => false, // SQL执行日志记录
    
    /* 错误设置 */
    'ERROR_MESSAGE'         => '页面错误！请稍后再试～',//错误显示信息,非调试模式有效
    'ERROR_PAGE'            => '',	// 错误定向页面
    'SHOW_ERROR_MSG'        => false,    // 显示错误信息
    'TRACE_EXCEPTION'       => false,   // TRACE错误信息是否抛异常 针对trace方法 
    "SHOW_PAGE_TRACE" => false,
    
    /* Cookie设置 */
    'COOKIE_EXPIRE'         => 3600 * 24 * 365,    // Coodie有效期
    'COOKIE_DOMAIN'         => '',      // Cookie有效域名
    'COOKIE_PATH'           => '/',     // Cookie路径
    'COOKIE_PREFIX'         => '',      // Cookie前缀 避免冲突

    /* 日志设置 */
    'LOG_RECORD'            => false,   // 默认不记录日志
    'LOG_TYPE'              => 3, // 日志记录类型 0 系统 1 邮件 3 文件 4 SAPI 默认为文件方式
    'LOG_DEST'              => '', // 日志记录目标
    'LOG_EXTRA'             => '', // 日志记录额外信息
    'LOG_LEVEL'             => 'EMERG,ALERT,CRIT,ERR',// 允许记录的日志级别
    'LOG_FILE_SIZE'         => 2097152,	// 日志文件大小限制
    'LOG_EXCEPTION_RECORD'  => false,    // 是否记录异常信息日志

    'SHOW_PAGE_TRACE' => false
);
?>