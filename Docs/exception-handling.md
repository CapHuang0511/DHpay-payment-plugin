除了正常成功的交易以外,本部分还说明了可能发生的其它各种交易。

# 返回链接失败

可能的原因是:
- 返回链接 URL 错误; 或 DHpay 与商户服务器之间断开连接 ; 或任何一方的服务器 不能正确处理返回链接。

既然银行已确定交易状态,交易结束。商户可通过以下两方面确认交易状态: 

 1. 登录商户自服务平台在“我的收款-交易信息”中获取交易数据的状态
 2. 通过使用查询 API 询问交易状态。

# 对 returnUrl 重定向向失败
可能的原因是

1. 集成中设置了错误 /不合理的被返回 URL; 或
2. 客户与商户的服务器之间断开连接 ; 或
3. 客户的计算机死机/重启/断电 既然银行已确定交易状态,交易结束。商户应告知客户当此类事情发生时与商户联系并 确认交易状态。

#客户进行的不完整 3D 认证交易

可能的原因是:

当要求客户在发卡银行网页上输入 3D 认证信息时,客户关闭了浏览器; 或由于各种原因,客户不能进入发卡银行的 3D 认证页面,如关闭 cookie 功能。交易状态保持在“处 理中”。商户可以通过查询 API 查询一段时间内仍然处于“处理中”的交易的最终状态。