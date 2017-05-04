<?php
class redis_common {  
      
    private $redis; //redis对象
	private $redis_allow = true;//初始化类
	private $read_link = NULL;   // 读资源 
    private $write_link = NULL;   // 写资源
	private $redis_prefix = '';//redis缓存前缀
 	
	public function connect($redis_config = array()) {
		if(class_exists('Redis', false)){
			$link = new Redis();
					
			$config = array();
			
			if(empty($redis_config['host'])){
				$config['server'] = '127.0.0.1';
			}else{
				$config['server'] = $redis_config['host'];
			}
			
			if(empty($redis_config['port'])){
				$config['port'] = '6379';
			}else{
				$config['port'] = $redis_config['port'];
			}
			
			if($link -> connect($config['server'], $config['port']))//连 redis
			{
				$this -> redis_allow = true;
				return $link;
			}else{
				$this -> redis_allow = false;
				return false;	
			}
		}else{
			$this -> redis_allow = false;
			return false;
		}
	}
    /** 
     * 初始化Redis 
     * $config = array( 
     *  'server' => '127.0.0.1' 服务器 
     *  'port'   => '6379' 端口号 
     * ) 
     * @param array $config 
     */  
	public function redis_read() {//从，只读
		if( $this -> read_link === NULL ){
			$this -> read_link = $this -> connect($GLOBALS['site_configure']['redis']['redis_read']);
		}
    	$this->redis = $this -> read_link;
        return $this;
	}
	
	public function redis_write() {//主，只写
		if( $this -> write_link === NULL ){
			$this -> write_link = $this -> connect($GLOBALS['site_configure']['redis']['redis_write']);
		}
		$this->redis = $this -> write_link;
        return $this;
	}    
	
	/**
	 * 获取缓存默认配置
	 */
	public function redis_cache($cache=array()) {//主，只写
		$cache_new = array();
		if( isset($cache['cache_name']) ){	//key值
			$this -> redis_change_prefix($cache);//获取redis缓存前缀
			$cache_new['cache_name'] = $this -> redis_prefix . $cache['cache_name'];
		}
		if( isset($cache['cache_value']) ){ //value值
			$cache_new['cache_value'] = serialize($cache['cache_value']);
		}
		if( isset($cache['cache_index']) ){ //hash类型的field域
			$cache_new['cache_index'] = $cache['cache_index'];
		}
		if( isset($cache['cache_name_karray'])){ //获取多个key的名称数组(string类型用)
			$this -> redis_change_prefix($cache);//获取redis缓存前缀
			foreach($cache['cache_name_karray'] as $key=>$value){
				 $cache_new['cache_name_karray'][] = $this -> redis_prefix.$value;
			}		
		}
		if( isset($cache['cache_name_varray'])){//获取多个key的值数组(string类型用)
			$this -> redis_change_prefix($cache);//获取redis缓存前缀
			foreach($cache['cache_name_varray'] as $key=>$value){
				 $cache_new['cache_name_varray'][$this -> redis_prefix.$key] = $value;
			}		
		}
		
		if( isset($cache['cache_field_karray'])){ //获取哈希表多个域的名称数组(hash类型用)
			/*foreach($cache['cache_field_karray'] as $key=>$value){
				 $cache_new['cache_field_karray'][] =  $value;
			}*/
			$cache_new['cache_field_karray'] =  $cache['cache_field_karray'];	
			
		}
		if( isset($cache['cache_field_varray'])){//获取哈希表多个域的值数组(hash类型用)
			/*foreach($cache['cache_field_varray'] as $key=>$value){
				 $cache_new['cache_field_varray'][$key] = $value;
			}*/
			//$cache_new['cache_field_varray'] = $cache['cache_field_varray'];
			$cache_new['cache_field_varray'] = array_map("serialize", $cache['cache_field_varray']);
		}
		
		if( isset($cache['cache_time']) && is_numeric($cache['cache_time']) ){	//生存时间
			$cache_new['cache_time'] = $cache['cache_time'];
		}else{
			$cache_new['cache_time'] = 14400;//4小时
		}
		if( isset($cache['cache_death_time']) ){	//unix时间戳定义消亡时间点
			$cache_new['cache_death_time'] = strval($cache['cache_death_time']);
		}
		
		if( isset($cache['cache_inc_size']) && is_numeric($cache['cache_inc_size']) ){	//自定义增长幅度大小
			$cache_new['cache_inc_size'] = $cache['cache_inc_size'];
		}
		return $cache_new;
	}
	
	/**
	 * Key类型开始
	 */
	 
	/*
	 * del（删除指定的key）
	 * @author LS  2014-07-10 11:21:21
	 */
	public function redis_del($cache=array()){	
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			$result = $this -> redis -> del($cache['cache_name']);
			if($result>0){
				return true;	
			}else{
				return false;	
			}
		}else{
			return false;
		}
	}
	
	/**
	 * mdel（删除多个给定的key,返回删除成功个数,都不成功返回false）
	 * @author LS  2014-07-10 11:21:21
	 */
	public function redis_mdel($cache=array()){	
		if( $this -> redis_allow && is_array($cache['cache_name_karray']) ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			$result = $this -> redis -> del($cache['cache_name_karray']);
			if($result>0){
				return $result;
			}else{
				return false;	
			}
		}else{
			return false;
		}
	}
	
	/**
	 * exist（判断key是否存在）
	 * @author LS  2014-07-10 11:21:21
	 */
	public function redis_exist($cache=array()){	
		if( $this -> redis_allow && $cache['cache_name']!='' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return $this -> redis ->exists($cache['cache_name']);
		}else{
			return false;	
		}	
	}
	
	/**
	 * type（返回指定key的类型）
	 * @author LS  2014-07-10 11:21:21
	 * @rerurn $result_string 类型名称 
	 */
	public function redis_type($cache=array()){	
		if( $this -> redis_allow && $cache['cache_name']!='' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			$result = $this -> redis->type($cache['cache_name']);
			/*
			none(key不存在) int(0)
			string(字符串) int(1)
			list(列表) int(3)
			set(集合) int(2)
			zset(有序集) int(4)
			hash(哈希表) int(5)
			*/
			switch($result){
				case 0: $result_string='none';break;
				case 1: $result_string='string';break;	
				case 2: $result_string='list';break;	
				case 3: $result_string='set';break;	
				case 4: $result_string='zset';break;	
				case 5: $result_string='hash';break;
				default:  $result_string='none';
			}
			return $result_string;
		}else{
			return false;	
		}	
	}
	
	/**
	 * keys（返回符合表达式的key名称）
	 * @author LS  2014-07-10 11:21:21
	 */
	public function redis_keys($search_string=''){
		if( $this -> redis_allow && $search_string!='' ){
			return $this -> redis->keys($search_string);
		}else{
			return false;	
		}	
	}
	
	/**
	 * rename（key重命名）
	 * @param boolean $is_cover 是否覆盖 默认情况下，重命名时若新名称已存在，旧值将会被覆盖，若设置为false，旧值将不会被覆盖，操作会失败
	 * @author LS  2014-07-10 11:21:21
	 */
	public function redis_rename($cache=array(),$new_name='',$is_cover=true){	
		if( $this -> redis_allow && $cache['cache_name']!='' && $new_name!='' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			if($is_cover){
				return $this -> redis ->rename($cache['cache_name'],$this -> redis_prefix . $new_name);
			}else{
				return $this -> redis ->renamenx ($cache['cache_name'],$this -> redis_prefix . $new_name);
			}
		}else{
			return false;	
		}	
	}
	
	/**
	 * expire（设置指定key的生存时间）
	 * @author LS  2014-07-10 11:21:21
	 */
	public function redis_expire($cache=array()){	
		if( $this -> redis_allow && $cache['cache_name']!=''){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return  $this -> redis ->expire ($cache['cache_name'],$cache['cache_time']);
		}else{
			return false;	
		}	
	}
	
	/**
	 * expireat（unix时间戳设置指定key的生存时间）
	 * @author LS  2014-07-10 11:21:21
	 */
	public function redis_expireat($cache=array()){	
		if( $this -> redis_allow && $cache['cache_name']!='' && $cache['cache_death_time']!=''){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return $this -> redis ->expireat ($cache['cache_name'],$cache['cache_death_time']);
		}else{
			return false;	
		}	
	}
	
	/**
	 * PERSIST(移除给定key的生存时间)
	 * @author LS  2014-07-10 11:21:21
	 */
	public function redis_persist($cache=array()){	
		if( $this -> redis_allow && $cache['cache_name']!=''){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return $this -> redis ->persist ($cache['cache_name']);
		}else{
			return false;	
		}	
	}
	
	/**
	 * ttl（返回指定key的生存时间）
	 * @author LS  2014-07-10 11:21:21
	 */
	public function redis_ttl($cache=array()){	
		if( $this -> redis_allow && $cache['cache_name']!=''){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			$result = $this -> redis ->ttl ($cache['cache_name']);
			if($result>0){
				return $result;	
			}else{
				return false; //三种情况：key不存在，未设置过期时间，已过期
			}	
		}else{
			return false;	
		}	
	}
	
	/**
	 * flushall（清空所有的key）
	 * @author LS  2014-07-10 11:21:21
	 */
	public function redis_flushall(){	
		if( $this -> redis_allow ){
			return $this -> redis ->flushall();
		}else{
			return false;	
		}	
	}
	
	/**
	 * Key类型结束
	 */
	
	/**
	 * String 类型开始
	 */
	
	/**
	 * get(获取指定key的值)
	 * @author LS  2014-07-10 11:21:21
	 */
	public function string_get($cache=array()){	
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return unserialize($this -> redis -> get($cache['cache_name']));
		}else{
			return false;
		}
	}
	
	/**
	 * mget（获取多个key的值）
	 * @author LS  2014-07-10 11:21:21
	 */
	public function string_mget($cache=array()){	
		if( $this -> redis_allow && is_array($cache['cache_name_karray']) ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return $this -> redis -> mget($cache['cache_name_karray']);
	
		}else{
			return false;
		}
	}
	
	/**
	 * set（设置指定key的值）
	 * @param boolean $is_cover 是否覆盖 默认情况，如果已存在指定key，旧值将被覆盖，若设置为false，旧值不会被覆盖，操作失败
	 * @author LS  2014-07-10 11:21:21
	 */
	public function string_set($cache=array(),$is_cover=true){	
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			if($is_cover){
				return $this -> redis -> set($cache['cache_name'],$cache['cache_value']);
			}else{
				return $this -> redis -> setnx($cache['cache_name'],$cache['cache_value']);
			}
		}else{
			return false;
		}
	}
	
	/**
	 * mset（设置多个key的值）
	 * @author LS  2014-07-10 11:21:21
	 */
	public function string_mset($cache=array()){	
		if( $this -> redis_allow && is_array($cache['cache_name_varray']) ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return $this -> redis -> mset($cache['cache_name_varray']);
		}else{
			return false;
		}
	}
	
	/**
	 * setex（同时设置指定key的值和消亡时间,若key存在，key值会被覆盖）
	 * @author LS  2014-07-10 11:21:21
	 */
	public function string_setex($cache=array()){	
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return $this -> redis -> setex($cache['cache_name'],$cache['cache_time'],$cache['cache_value']);
		}else{
			return false;
		}
	}
	
	/**
	 * getset（将给定key的值设为value，并返回key的旧值,若key不存在，返回false，但是新值被设置）
	 * @author LS  2014-07-10 11:21:21
	 */
	public function string_getset($cache=array()){	
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return $this -> redis -> getset($cache['cache_name'],$cache['cache_value']);
		}else{
			return false;
		}
	}
	
	/**
	 * append（将value追加到指定key值之后，若key不存在则创建key）
	 * @author LS  2014-07-10 11:21:21
	 */
	public function string_append($cache=array()){	
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			$result = $this -> redis -> append($cache['cache_name'],$cache['cache_value']); //key中字符串长度
			if($result>0){
				return true;	
			}else{
				return false;	
			}
		}else{
			return false;
		}
	}
	
	/**
	 * strlen（返回key所储存的字符串值的长度）
	 * @author LS  2014-07-10 11:21:21
	 */
	public function string_strlen($cache=array()){	
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			$result = $this -> redis -> strlen($cache['cache_name']);
			if($result>0){
				return $result;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	/**
	 * incrby（将key所储存的值加上增量increment,返回key值，若不是数字类型，返回false）
	 * @author LS  2014-07-10 11:21:21
	 */
	public function string_incrby($cache=array()){	
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			if(!isset($cache['cache_inc_size'])){
				$cache['cache_inc_size'] = 1;
			}
			return $this -> redis -> incrby($cache['cache_name'],$cache['cache_inc_size']);
			/*
			if($cache['cache_inc_size']>0)
			{
				return $this -> redis -> incrby($cache['cache_name'],$cache['cache_inc_size']);
			}else{
				return $this -> redis -> decrby($cache['cache_name'],abs($cache['cache_inc_size']));
			}
			*/
		}else{
			return false;
		}
	}
	
	/**
	 * String 类型结束
	 */
	
	
	/**
	 * Hash 类型开始
	 */
	
	/**
	 * 返回哈希表key中指定的field的值，若查找不到，返回false (√)
	 * @author LS  2014-07-10 11:21:21
	 */
	public function hash_get($cache=array()){	
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return unserialize($this -> redis -> hget($cache['cache_name'], $cache['cache_index']));
		}else{
			return false;
		}
	}
	
	/**
	 * hmget(获取哈希表key中多个域的值)
	 * @author LS  2014-07-10 11:21:21
	 */
	public function hash_mget($cache=array()){	
		if( $this -> redis_allow && $cache['cache_name']!='' && is_array($cache['cache_field_karray']) ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			$result = $this -> redis -> hmget($cache['cache_name'],$cache['cache_field_karray']);
			if(!empty($result)){
				$result = array_map("unserialize",$result);
			}
			return $result;
			//return 
		}else{
			return false;
		}
	}
	
	/**
	 * 返回哈希表key中所有的field的值，若查找不到或key中没有field，返回false (√)
	 * @author LS  2014-07-10 11:21:21
	 */
	public function hash_getall($cache=array()){	
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			$result = $this -> redis -> hgetall($cache['cache_name']);
			if(count($result)>0){
				return $result;
			}else{
				return false;	
			}
		}else{
			return false;
		}
	}
	
	/**
	 * 将哈希表key中的域field的值设为value。如果key不存在，一个新的哈希表被创建并进行相应操作。
	 * @param boolean $is_cover 是否覆盖 默认情况，如果域field已经存在于哈希表中，旧值将被覆盖，若设置为false，旧值不会被覆盖，操作将失败
	 * @author LS  2014-07-10 11:21:21
	 */
	public function hash_set($cache=array(),$is_cover=true){
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			if($is_cover){
				return $this -> redis -> hset($cache['cache_name'], $cache['cache_index'],$cache['cache_value']);
			}else{
				return $this -> redis -> hsetnx($cache['cache_name'], $cache['cache_index'],$cache['cache_value']);
			}
		}else{
			return false;
		}
	}
	
	/**
	 * hmset(设置哈希表key中多个域的值,已存在的域的值会被覆盖)
	 * @author LS  2014-07-10 11:21:21
	 */
	public function hash_mset($cache=array()){	
		if( $this -> redis_allow && $cache['cache_name']!='' && is_array($cache['cache_field_varray'])){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return $this -> redis -> hmset($cache['cache_name'],$cache['cache_field_varray']);
		}else{
			return false;
		}
	}
	
	/**
	 * 删除哈希表key中的指定域field,成功返回true，失败返回false
	 * @author LS  2014-07-10 11:21:21
	 */
	public function hash_del($cache=array()){
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			$result = $this -> redis -> hdel($cache['cache_name'], $cache['cache_index']);
			if($result>0){
				return true;	
			}else{
				return false;	
			}
		}else{
			return false;
		}
	}
	
	/**
	 * 判断哈希表key中的指定域field是否存在,存在返回true，不存在返回false
	 * @author LS  2014-07-10 11:21:21
	 */
	public function hash_exists($cache=array()){
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return $this -> redis -> hexists($cache['cache_name'], $cache['cache_index']);
		}else{
			return false;
		}
	}
	
	/**
	 * 返回哈希表key中的域数量 
	 * @author LS  2014-07-10 11:21:21
	 */
	public function hash_len($cache=array()){
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			$length = $this -> redis -> hlen($cache['cache_name']);
			if($length>0){
				return $length;	
			}
			else{
				return false;	
			}
		}else{
			return false;
		}
	}
	
	/**
	 * 对哈希表key的指定域（field）的值进行累加(默认加1)，若初始值不存在时，默认为0;
	 * 返回结果：若累加成功，则返回key值，若不是数字类型，则返回false
	 * @author LS  2014-07-10 11:21:21
	 */
	public function hash_incrby($cache=array()){
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			if(!isset($cache['cache_inc_size'])){
				$cache['cache_inc_size'] = 1;
			}
			return $this -> redis -> hincrby($cache['cache_name'], $cache['cache_index'],$cache['cache_inc_size']);
		}else{
			return false;
		}
	}
	
	/**
	 * hkeys 返回hash表中的所有的域（field）的名称
	 * @author LS  2014-07-10 11:21:21
	 */
	public function hash_keys($cache=array()){
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			$result = $this -> redis -> hkeys($cache['cache_name']);
			if(count($result)>0){
				return $result;	
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	/**
	 * hkeys 返回hash表中的所有的域（field）的值
	 * @author LS  2014-07-10 11:21:21
	 */
	public function hash_vals($cache=array()){
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			$result = $this -> redis -> hvals($cache['cache_name']);
			if(count($result)>0){
				return $result;	
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	/**
	 * Hash 类型结束
	 */
	
	/**
	 * List 类型开始
	 */
	
	/**
	 * 返回列表key中，下标为index的元素
	 * 下标(index)参数start和stop都以0为底，也就是说，以0表示列表的第一个元素，以1表示列表的第二个元素，以此类推。
	 * 你也可以使用负数下标，以-1表示列表的最后一个元素，-2表示列表的倒数第二个元素，以此类推。
	 * 如果key不是列表类型，返回一个错误。
	 * @author GNL 2014-07-09 18:01:55
	 */
	public function list_get($cache=array()) {
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return $this -> redis -> lindex($cache['cache_name'], $cache['cache_index']);
		}else{
			return false;
		}
	}
	
    /** 
     * 设置列表key中，下标为index的元素的值(超出索引，返回错误)
	 * 下标(index)参数start和stop都以0为底，也就是说，以0表示列表的第一个元素，以1表示列表的第二个元素，以此类推。
	 * 你也可以使用负数下标，以-1表示列表的最后一个元素，-2表示列表的倒数第二个元素，以此类推。
     */  
	public function list_set($cache=array()){
		if( $this -> redis_allow && $cache['cache_name'] != '' && isset($cache['cache_value']) ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return $this -> redis -> lset($cache['cache_name'], $cache['cache_index'],$cache['cache_value']);
		}else{
			return false;
		}
	}
	
	/*
	 *  在指定Key所关联的List Value的尾部插入参数中给出的所有Values。
	 *  默认	情况下，如果该Key不存在，该命令将在插入之前创建一个与该Key关联的空链表，之后再将数据从链表的尾部插入,若$is_cover设置为false,默认不会创建链表也不执行任何操作
	 *  成功返回插入后链表中元素的数量，失败返回false
	 */
	public function list_rpush($cache=array(),$is_cover=true){
		if( $this -> redis_allow && $cache['cache_name'] != '' && isset($cache['cache_value']) ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			if($is_cover){
				return $this -> redis -> rpush($cache['cache_name'],$cache['cache_value']);
			}else{
				return $this -> redis -> rpushx($cache['cache_name'],$cache['cache_value']);
			}
		}else{
			return false;
		}	
	}
	
	/*
	 *  在指定Key所关联的List Value的头部插入参数中给出的Values。
	 *  默认	情况下，如果该Key不存在，该命令将在插入之前创建一个与该Key关联的空链表，之后再将数据从链表的头部插入,若$is_cover设置为false,默认不会创建链表也不执行任何操作
	 *  成功返回插入后链表中元素的数量，失败返回false
	 */
	public function list_lpush($cache=array(),$is_cover=true){
		if( $this -> redis_allow && $cache['cache_name'] != '' && isset($cache['cache_value']) ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			if($is_cover){
				return $this -> redis -> lpush($cache['cache_name'],$cache['cache_value']);
			}else{
				return $this -> redis -> lpushx($cache['cache_name'],$cache['cache_value']);
			}
		}else{
			return false;
		}
	}
	
	/*
	 *  返回并弹出指定Key关联的链表中的第一个元素，即头部元素。如果该Key不存在，返回false
	 */
	public function list_lpop($cache=array()){
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return $this -> redis -> lpop($cache['cache_name']);
		}else{
			return false;
		}
	}
	
	/*
	 *  返回并弹出指定Key关联的链表中的最后一个元素，即尾部元素。如果该Key不存在，返回false
	 */
	public function list_rpop($cache=array()){
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return $this -> redis -> rpop($cache['cache_name']);
		}else{
			return false;
		}
	}
	
	/* 
	 * 将第一个链表的尾部元素弹出，同时再插入到第二个链表的头部(原子性的完成这两步操作) 
	 * 可以source和destination设为同一键，将链表中的尾部元素移到其头部
	 */
	public function list_rpoplpush($cache=array(),$new_list_name){
		if( $this -> redis_allow && $cache['cache_name']!='' && $new_list_name!='' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return $this -> redis ->rpoplpush($cache['cache_name'],$this -> redis_prefix . $new_list_name);
		}else{
			return false;
		}
	}
	
	/*
	 * 返回指定Key关联的链表中元素的数量，如果该Key不存在或类型不是链表，返回false
	 */
	public function list_len($cache=array()){
		if( $this -> redis_allow && $cache['cache_name'] != '' ){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			$length = $this -> redis -> llen($cache['cache_name']);
			if($length>0){
				return $length;
			}
			else{
				return false;
			}
		}else{
			return false;
		}
	}
	/* 
	 * 返回队列中一个区间的元素 
	 * 下标(index)参数start和end都以0为底，也就是说，以0表示列表的第一个元素，以1表示列表的第二个元素，以此类推。
	 * 你也可以使用负数下标，以-1表示列表的最后一个元素，-2表示列表的倒数第二个元素，以此类推。
	 */
	public function list_range($cache=array(),$start=0,$end=-1){
		if( $this -> redis_allow && $cache['cache_name'] != ''){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return $this -> redis -> lrange($cache['cache_name'],$start,$end);
		}else{
			return false;
		}
	}

	/*
	 * 左起保留索引值内的内容
	 * 下标(index)参数start和end都以0为底，也就是说，以0表示列表的第一个元素，以1表示列表的第二个元素，以此类推。
	 * 你也可以使用负数下标，以-1表示列表的最后一个元素，-2表示列表的倒数第二个元素，以此类推。
	*/
	public function list_ltrim($cache=array()){
		if( $this -> redis_allow && $cache['cache_name'] != '' && $cache['cache_index_start']!='' && $cache['cache_index_end']!=''){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			return $this -> redis -> ltrim($cache['cache_name'],$cache['cache_index_start'],$cache['cache_index_end']);
		}else{
			return false;
		}
	}
	
	/* 	
	 * 在指定Key关联的链表中，删除前count个值等于value的元素。
	 * 如果count大于0，从头向尾遍历并删除，如果count小于0，则从尾向头遍历并删除。
	 * 如果count等于0，则删除链表中所有等于value的元素。如果指定的Key不存在，则直接返回0。
 	 */	
 	public function list_lrem($cache=array(),$count){
		if( $this -> redis_allow && $cache['cache_name'] != ''){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			$this -> redis -> lrem($cache['cache_name'],$count,$cache['cache_value']);
		}else{
			return false;
		}
	}
	
	/* 
	 * 该命令的功能是在指定元素（$item）的前面或后面插入参数中的元素value。如果Key不存在，该命令将不执行任何操作。 
	 * 本方法插入成功返回true,插入失败返回false(redis自带方法结果：成功插入后链表中元素的数量，如果没有找到指定元素，返回-1，如果key不存在，返回0）
	 * @param string $item 指定元素的value值
	 * @param boolen $is_before true表示在指定元素之前插入，false表示在指定元素之后插入,默认在之前插入
	 */
	public function list_insert($cache=array(),$item,$is_before=true){
		if( $this -> redis_allow && $cache['cache_name'] != '' && $item!=''){
			$cache = $this -> redis_cache($cache);//获取缓存参数，格式化
			if($is_before){	
				$result = $this -> redis -> linsert($cache['cache_name'],'before',$item,$cache['cache_value']);
			}else{
				$result = $this -> redis -> linsert($cache['cache_name'],'after',$item,$cache['cache_value']);
			}
			if($result>0){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	/**
	 * List 类型结束
	 */
      
    /** 
     * 返回redis对象 
     * redis有非常多的操作方法，我们只封装了一部分 
     * 拿着这个对象就可以直接调用redis自身方法 
     */  
    public function redis() {  
        return $this->redis;  
    }
	
	/**
	 * 内存缓存redis修改缓存key前缀
	 * 
	 * @param  boolean $site_only 是否只是本站缓存
	 * @param  boolean $language_only 是否只是本语言缓存
	 * @author GNL 2013-11-14 09:55:20
	 */
	private function redis_change_prefix($cache=array())
	{
		$this -> redis_prefix = '';
		if( isset($cache['site_only']) ){
			if($cache['site_only'] !== false){
				$this -> redis_prefix = $this -> redis_prefix . $GLOBALS['_CFG']['site_from'];
			}
		}else{
			$this -> redis_prefix = $this -> redis_prefix . $GLOBALS['_CFG']['site_from'];//默认是单站缓存
		}
		
		if( isset($cache['language_only']) ){
			if($cache['language_only'] !== false){
				$this -> redis_prefix = $this -> redis_prefix . $GLOBALS['_CFG']['language_flag'];
			}
		}else{
			$this -> redis_prefix = $this -> redis_prefix . $GLOBALS['_CFG']['language_flag'];//默认是单站缓存
		}
		
		$this -> redis_prefix = $this -> redis_prefix . $GLOBALS['site_configure']['redis']['redis_site_prefix'];
	}
}  

?>