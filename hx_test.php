 <?php
 class Hxchat{
    
    private $app_key = 'xxxxx#testchat';
    private $client_id = 'YXA69G8H4IVgExxxxxHGMpBBCHQ';
    private $client_secret = 'YXA6W7jOachvUAcTxxxxxtP_wGybvAU';
    private $url = "https://a1.easemob.com/xxxxx/testchat";
	private $token='';
	private $header=array();

    /*
     * 获取APP管理员Token
     */
    public function __construct(){

		//设置缓存
        $cache_temp_file='./.cach.json';
        if(!file_exists($cache_temp_file)){
            touch($cache_temp_file);
        }

        $this->token=file_get_contents($cache_temp_file);

        $time=filemtime($cache_temp_file)+(3600*24);

        //判断文件是否超时(缓存24小时)
        if( $time < time() || $this->token==''){

            $url = $this->url . "/token";

            $data = array(
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->client_id,
                'client_secret' => $this->client_secret
            );

            $rs=$this->curl($url, $data);

            $this->token = $rs['access_token'];

            file_put_contents($cache_temp_file,$this->token);
        }
		
		//请求头
        $this->header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        );
    }

    /*
     * 开放注册IM用户[单个]
     */
    public function register($username, $password, $nickname){
        $url = $this->url . "/users";

        $data = array(
            'username' => $username,
            'password' => $password,
            'nickname' => $nickname
        );
		
        return $this->curl($url, $data, array($this->header[0]), "POST");
    }

    /*
     * 注册IM用户[单个](授权注册)
     */
    public function auth_register($username, $password, $nickname){
        $url = $this->url . "/users";

        $data = array(
            'username' => $username,
            'password' => $password,
            'nickname' => $nickname
        );
        return $this->curl($url, $data, $this->header, "POST");
    }

    /*
     * 注册IM用户[批量]批量注册的用户数量不要过多, 建议在20-60之间
     *
     *@param  $data   二维数组
     */
    public function more_register($data){
        $url = $this->url . "/users";

        return $this->curl($url, $data, $this->header, "POST");
    }

    /*
     * 获取IM用户[单个]
     */
    public function get_single_user($username){
        $url = $this->url."/users/${username}";
        return $this->curl($url, "", array($this->header[1]), "GET");
    }

    /*
     * 获取IM用户[批量]可分页
     */
    public function get_more_user($limit,$page=''){
        $url = $this->url . "/users?limit=".$limit;

        if($page!=''){
            $url.='&cursor='.$page;
        }

        return $this->curl($url, "", array($this->header[1]), "GET");
    }

    /*
     * 删除IM用户[单个]
     */
    public function delete_single_user($username){
        $url = $this->url . "/users/${username}";

        return $this->curl($url, "", array($this->header[1]), "DELETE");
    }

    /*
     * 删除IM用户[批量]  删除最开始的n条
     */
    public function delete_more_user($limit,$page=''){

        $url = $this->url . "/users/?limit=".$limit;

        if($page !=''){
             $url.='&cursor='.$page;
        }
        return $this->curl($url, "", array($this->header[1]), "DELETE");
    }
    
    /*
     * 重置IM用户密码
     */
    public function update_user_password($username, $newpassword){
        $url = $this->url . "/users/${username}/password";

        $data['newpassword'] = $newpassword;

        return $this->curl($url, $data, array($this->header[1]), "PUT");
    }


    /*
     * 修改用户昵称
     */
    public function update_user_nickname($username, $nickname){
        $url = $this->url . "/users/${username}";

        $data['nickname'] = $nickname;

        return $this->curl($url, $data, array($this->header[1]), "PUT");
    }


    /*
     * 给IM用户的添加好友
     * 给owner_username用户添加friend_username用户
     */
    public function add_friend_contacts($owner_username, $friend_username){
        $url = $this->url . "/users/${owner_username}/contacts/users/${friend_username}";

        return $this->curl($url, "", array($this->header[1]), "POST");
    }

    /*
     * 解除IM用户的好友关系
     * 给owner_username解除friend_username关系
     */
    public function delete_user_contacts($owner_username, $friend_username){
        $url = $this->url . "/users/${owner_username}/contacts/users/${friend_username}";
        return $this->curl($url, "",  array($this->header[1]), "DELETE");
    }

    /*
     *
     *查看好友  查看某个IM用户的好友信息
     */
    public function get_contacts_user($owner_username){
        $url = $this->url . "/users/${owner_username}/contacts/users";
        return $this->curl($url, "",  array($this->header[1]), "GET");
    }

    /*
     *
     *获取用户黑名单
     */
    public function get_user_blockslist($owner_username,$data){
        $url = $this->url . "/users/${owner_username}/blocks/users";
        return $this->curl($url, $data,  array($this->header[1]), "GET");
    }

    /*
     *
     *给某个用户添加黑名单
     */
    public function add_user_blockslist($owner_username,$data){
        $url = $this->url . "/users/${owner_username}/blocks/users";

        return $this->curl($url, $data,  array($this->header[1]), "POST");
    }
    
    /*
     *
     *从IM用户的黑名单中减人
     */
    public function delete_user_blockslist($owner_username,$blocked_username){
        $url = $this->url . "/users/${owner_username}/blocks/users/${blocked_username}";

        return $this->curl($url, '', array($this->header[1]), "DELETE");
    }

    /*
     *
     *查看用户在线状态
     */
    public function is_line($username){
        $url = $this->url . "/users/${username}/status";

        $header = array(
            'Content-Type:application/json',
            'Authorization: Bearer ' . $this->token
        );

        return $this->curl($url, '', $header, "GET");
    }

    /*
     *
     *查看用户查询离线消息数 获取一个IM用户的离线消息数
     */
    public function offline_msg_count($owner_username){
        $url = $this->url . "/users/${owner_username}/offline_msg_count";

        $header = array(
            'Authorization: Bearer ' . $this->token
        );

        return $this->curl($url, '', $header, "GET");
    }

    /*
     *
     *通过离线消息的id查看用户的该条离线消息状态
     */
    public function offline_msg_status($username,$msg_id){
        $url = $this->url . "/users/${username}/offline_msg_status/${msg_id}";

        $header = array(
            'Content-Type : application/json',
            'Authorization: Bearer ' . $this->token
        );

        return $this->curl($url, '', $header, "GET");
    }

    /*
     *
     *禁用某个IM用户的账号，禁用后该用户不可登录，下次解禁后该账户恢复正常使用。
     */
    public function forbidden_user($username){
        $url = $this->url . "/users/${username}/deactivate";

        $header = array(
            'Content-Type : application/json',
            'Authorization: Bearer ' . $this->token
        );

        return $this->curl($url, '', $header, "POST");
    }

    /*
     *
     *解除对某个IM用户账号的禁用，解禁后用户恢复正常使用。
     */
    public function unforbidden_user($username){
        $url = $this->url . "/users/${username}/activate";

        $header = array(
            'Content-Type : application/json',
            'Authorization: Bearer ' . $this->token
        );

        return $this->curl($url, '', $header, "POST");
    }

    /*
     *
     *强制用户下线如果某个IM用户已经登录环信服务器，强制其退出登录
     */
    public function user_disconnect($username){
        $url = $this->url . "/users/${username}/disconnect";

        $header = array(
            'Content-Type : application/json',
            'Authorization: Bearer ' . $this->token
        );

        return $this->curl($url, '', $header, "GET");
    }

    /*
     *
     *导出聊天记录
     */
    public function download_chatmessages($where=''){
        $url = $this->url . "/chatmessages";

        if($where!=''){
            $url.='?ql=select+*+where+from=abc345';
            $url=urlencode($url);
        }

        $header = array(
            'Content-Type : application/json',
            'Authorization: Bearer ' . $this->token
        );

        return $this->curl($url, '', $header, "GET");
    }


    /* 
    发送文本消息
    */ 
    public function send_text_message($sender, $receiver, $msg){
        $url = $this->url . "/messages";
       
        $header = array(
            'Content-Type : application/json',
            'Authorization: Bearer ' . $this->token
        );

        $data = array(
            'target_type' => 'users',
            'target' => array(
                '0' => $receiver
            ),
            'msg' => array(
                'type' => "txt",
                'msg' => $msg
            ),
            'from' => $sender,
            'ext' => array(
                'attr1' => 'v1',
                'attr2' => "v2"
            )
        );
        return $this->curl($url, $data, $header, "POST");
    }
    
    /* 
    获取app中所有的群组
    */ 
    public function get_all_chatgroups(){
        $url = $this->url . "/chatgroups";
       
        $header = array(
            'Authorization: Bearer ' . $this->token
        );

        return $this->curl($url, '', $header, "GET");
    }
	
	/**
	* 下载图片
	*/
	public function down_load_image($img_url){

		//快速下载
		$str=file_get_contents($img_url);
		$img='./'.time().rand(1000, 9999).'.jpg';
		file_put_contents($img,$str);
		return substr($img, 1);

	   /*
		官方用例
        $header = array(
			'thumbnail: true',
			'share-secret: DRGM8OZrEeO1vafuJSo2IjHBeKlIhDp0GCnFu54xOF3M6KLr',
            'Authorization: Bearer ' . $this->token,
			'Accept: application/octet-stream'
        );
		
        //return $this->curl($img_url, '', $header, "GET"); //POST? 没看懂
		*/
	}
	
    /*
     *
     * curl请求
     */
    private function curl($url, $data, $header = false, $method = "POST"){

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $ret = curl_exec($ch);

        return json_decode($ret,true);
    }
}
//调试函数
function P($content){
	echo '<pre>';
	print_r($content);
	echo '</pre>';
}

//实例化
$rs = new Hxchat();

/*****************下载图片*************/
$img_url="https://a1.easemob.com/koy64go/nuanxunchat/chatfiles/7d9c2290-9da4-11e5-8f74-ab49718afde6";
echo $rs->down_load_image($img_url);


/*************获取app中所有的群组********
$content=$rs->get_all_chatgroups();
P($content);
*/

/*************导出聊天记录********
$content=$rs->download_chatmessages();
P($content);
*/

/*************发送聊天信息********  345 给 789 发消息
$content=$rs->send_text_message('abc345','abc789','bbbbbbbbbbbb');
P($content);
*/

/**************开放注册的单个用户***
$content=$rs->register('abc_username', 'abc_password', 'abc_nick_name' );
P($content);
*/

/**************授权注册的单个用户***
$content=$rs->auth_register('abc_username1', 'abc_password1', 'abc_nick_name1' );
P($content);
*/

/**************注册IM用户[批量]***
$data = array(
     array('username' => 'abc345','password' => 'abc','nickname' => 'abc'),
     array('username' => 'abc789','password' => 'abc2','nickname' => 'abc2')
);
$content=$rs->more_register($data);
P($content);
*/

/**************获取单个IM用户***
$content=$rs->get_single_user('abc345');
P($content);
*/

/**************获取IM用户[批量]***
$content=$rs->get_more_user(3);
//分页
$content=$rs->get_more_user(3,'LTU2ODc0MzQzOm9KRXktb1hGRWVXc2ctZTZxR3A3THc');
P($content);
*/

/**************删除IM用户[单个]***
$content=$rs->delete_single_user('abc345');
P($content);
*/

/**************删除IM用户[多个] 并没有指定删除的具体用户,默认从先注册的用户开始删除指定的记录条数  也可以翻页删除
$content=$rs->delete_more_user(2);
$content=$rs->delete_more_user(2,'LTU2ODc0MzQzOm9KRXktb1hGRWVXc2ctZTZxR3A3THc');
P($content);
*/

/*************修改用户密码********
$content=$rs->update_user_password('tvy27015536','1a6e2f73a5ccc3439b70d8b0ffd2247f');
P($content);
*/

/*************修改用户昵称********
$content=$rs->update_user_nickname('abc345','abc345');
P($content);
*/

/*************给用户添加好友********
$content=$rs->add_friend_contacts('abc345','abc789');
P($content);
*/

/*************给用户解除好友********
$content=$rs->delete_user_contacts('abc345','abc789');
P($content);
*/

/*************查看好友 查看某个IM用户的好友信息********
$content=$rs->get_contacts_user('abc345');
P($content);
*/

/*************获取IM用户的黑名单********
$content=$rs->get_user_blockslist('abc345',array('data'=>array('abc789')));
P($content);
*/

/*************往IM用户的黑名单中加人********
$content=$rs->add_user_blockslist('abc345',array('usernames'=>array('abc789')));
P($content);
*/

/*************删除用户黑名单中********
$content=$rs->delete_user_blockslist('abc345','abc789');
P($content);
*/

/*************查看一个用户的在线状态********
$content=$rs->is_line('abc789');
P($content);
*/

/*************获取一个IM用户的离线消息数********
$content=$rs->offline_msg_count('abc789');
P($content);
*/

/*************通过离线消息的id查看用户的该条离线消息状态********
$content=$rs->offline_msg_status('abc789',1121212);
P($content);
*/

/*************用户账号禁用********
$content=$rs->forbidden_user('abc789');
P($content);
*/

/*************用户账号解禁********
$content=$rs->unforbidden_user('abc789');
P($content);
*/


/*************强制用户下线如果某个IM用户已经登录环信服务器，强制其退出登录********
$content=$rs->user_disconnect('abc789');
P($content);
*/

