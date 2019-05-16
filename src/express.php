<?php
namespace Fictioner\Express;

class express{

    protected $hostExpressName; //获取快递服务商
    protected $hostExpressNo;   //获取快递运单流转信息
    private $key;
    private $customer;

    public function __construct(){
        $this->hostExpressName = 'http://www.kuaidi100.com/autonumber/autoComNum?text=';
        $this->hostExpressNo = 'http://poll.kuaidi100.com/poll/query.do';
    }

    /**
     * 设置key
     * @param string $no
     * @return string
     */
    public function setKeyCustomer($key, $customer){
        $this->key=$key;
        $this->customer=$customer;
    }

    /**
     * 获取快递商家
     * @param string $no
     * @return string
     */
    public function getExpressNameByNo($no){
        $url = $this->hostExpressName . $no;
        $content = self::curlHttpGet($url);
        $content = json_decode($content, true);
        return $content['auto'][0]['comCode'];
    }

    /**
     * 获取快递信息
     * @param $no
     * @return string
     */
    public function getExpressInfoByNo($no){
        $expressName = $this->getExpressNameByNo($no);

        $key = $this->key;						//客户授权key
        $customer = $this->customer;					//查询公司编号
        $param = array (
            'com' => $expressName,			//快递公司编码
            'num' => $no,	//快递单号
//            'phone' => '',				//手机号
//            'from' => '',				//出发地城市
//            'to' => '',					//目的地城市
//            'resultv2' => '1'			//开启行政区域解析
        );

        //请求参数
        $post_data = array();
        $post_data["customer"] = $customer;
        $post_data["param"] = json_encode($param);
        $sign = md5($post_data["param"].$key.$post_data["customer"]);
        $post_data["sign"] = strtoupper($sign);

        $url = $this->hostExpressNo;	//实时查询请求地址

        $params = "";
        foreach ($post_data as $k=>$v) {
            $params .= "$k=".urlencode($v)."&";		//默认UTF-8编码格式
        }

        $post_data = substr($params, 0, -1);

        $content = self::curlHttpGet($url,1, $post_data);

        return $content;

    }

    /**
     * curl GET 请求
     * @param $url
     * @param $timeOut
     * @param bool $ssl
     * @param array $header
     * @return mixed
     */
    public static function curlHttpGet($url, $ifpost=false, $post_data='', $timeOut = 5, $ssl = false, $header = [])
    {
        $curl = curl_init();
        if($ifpost){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        }

        curl_setopt($curl, CURLOPT_URL, $url); //设置链接
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeOut);
        if ($ssl) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        $response = curl_exec($curl);
        $response = str_replace("\"", '"', $response );
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            //app('log')->info('HttpUtil_curlHttpGet', ['url' => $url, 'error' => $err]);
        }else {
            //app('log')->info('HttpUtil_curlHttpGet', ['url' => $url, 'response' => $response]);
            return $response;
        }

    }
}