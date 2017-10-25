# 查询 Api

#### 请求地址

https://www.dhpay.com/merchant/api/query/{binvoice_no}

#### HTTP head 参数

|      参数       |  必需  |         示例          |                    描述                    |
| :-----------: | :--: | :-----------------: | :--------------------------------------: |
| Content-Type  |  是   |  application/json   | 指定提交参数和返回值格式 application/json application/xml |
| Authorization |  是   | Basic  33323:xxxxxx | 商户认证 merchant_id 加 secret_key，冒号分割。经过 base64 编码。注意开头的 `Basic` 后有空格 |

- 请求参数

无

- 返回值 

|       参数       |  类型  |  长度  |          示例           |                 描述                 |
| :------------: | :--: | :--: | :-------------------: | :--------------------------------: |
|   mechant_id   |  字符  |  30  |        595300         |           商户号，对应商户提交的商户号           |
|   invoice_id   |  字符  |  50  |   14067123275514607   |           交易号，对应商户提交的交易号           |
|   trans_type   |  字符  |  10  |         sale          |       交易类型 sale/refund/void        |
|    order_no    |  字符  |  50  |      1462197131       |            订单号，对应交易的订单号            |
|     ref_no     |  字符  |  50  | 20140818161430_152634 |             DHpay 参考号              |
|     status     |  字符  |  2   |          01           |      交易状态 00 处理中 01 成功 02 失败       |
| failure_reason |  字符  | 100  |                       | 如果交易状态为“失败”，则会有相应的失败原因,    见失败原因字典 |
|    currency    |  字符  |  3   |                       |     交易币种 amount 数字 20,2  交易币种      |
|     amount     |  数字  | 20,2 |                       |                交易币种                |
|   trans_date   |  字符  |  8   |       20140725        |                交易日期                |
|   trans_time   |  字符  |  6   |        182511         |                交易时间                |
|   trans_time   |  字符  |  6   |        182511         |                交易时间                |
|      hash      |  字符  | 150  |                       |         参数签名,  详细签名方式请见附录          |


#### 示例
请求URL

https://www.dhpay.com/merchant/api/query/1300000129

返回

```json
{
    "status": "01",
    "failure_reason": null,
    "merchant_id": "200000000000000",
    "invoice_id": "1300000129",
    "order_no": "001000000003",
	"ref_no": "20150105195142_989988",
    "amount": "1.01",
    "currency": "USD",
    "trans_date": "20150105",
    "trans_time": "195142",
    "trans_type": "sale",
    "hash": "5FA7131D2754A0E721B87ACA395E38354618A1B97188AD90E9D9820DD9B66F77"
}
```

