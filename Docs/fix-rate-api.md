# 保值支付汇率 Api

#### 请求地址

https://www.dhpay.com/merchant/api/fix_rate/query/{baseCurrency}_{targetCurrency}

#### HTTP head 参数

|      参数       |  必需  |         示例          |                    描述                    |
| :-----------: | :--: | :-----------------: | :--------------------------------------: |
| Content-Type  |  是   |  application/json   | 指定提交参数和返回值格式 application/json application/xml |
| Authorization |  是   | Basic  33323:xxxxxx | 商户认证 merchant_id 加 secret_key，冒号分割。经过 base64 编码 |

- 请求参数

无

- 返回值 

|       参数       |  类型  |  长度  |          示例           |                 描述                 |
| :------------: | :--: | :--: | :-------------------: | :--------------------------------: |
|   mechant_id   |  字符  |  30  |        595300         |           商户号，对应商户提交的商户号           |
|     status     |  字符  |  2   |          01           |     01 成功 02 失败       |
|     failure_reason     |  字符  |  100   |          "target currency not supported"           |     如果查询汇率失败，描述失败原因       |
|   baseCurrency   |  字符  |  3  |   USD   |           基础货币，目前支持：USD           |
|   targetCurrency   |  字符  |  3  |         GBP          |       交易币种        |
|   timeStamp   |  数字  |  20,0  |     1499150539256  |      1970-1-1 00:00:00.000 经过的毫秒数       |
|   rate    |  字符  |  数字  |      20,6       |            对应汇率            |
|      hash      |  字符  | 150  |                       |         参数签名,  详细签名方式请见附录          |


#### 示例
请求URL

https://www.dhpay.com/merchant/api/fix_rate/query/USD_GBP

返回

```json
{
    "status": "01",
    "failure_reason": null,
    "merchant_id": "200000000000000",
    "baseCurrency": "USD",
    "targetCurrency": "GBP",
	"rate": "1.293700",
    "timeStamp": "1499150539256",
    "hash": "5FA7131D2754A0E721B87ACA395E38354618A1B97188AD90E9D9820DD9B66F77"
}
```

