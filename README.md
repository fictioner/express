# express
#### 快递查询（基于快递100）
##### 可以自动根据订单号识别快递公司，需购买后再使用

<pre><code>$exp=new \Fictioner\Express\express();
$exp->setKeyCustomer($key, $customer);
$data=$exp->getExpressInfoByNo($logistics_number);
$data=json_decode($data);
</code></pre>