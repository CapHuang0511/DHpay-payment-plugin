# 退款 Api

#### POST请求地址

https://www.dhpay.com/merchant/api/refund/

#### HTTP 请求 参数

- head参数

|      参数       |  必需  |         示例          |                    描述                    |
| :-----------: | :--: | :-----------------: | :--------------------------------------: |
| Content-Type  |  是   |  application/json   | 指定提交参数和返回值格式 application/json application/xml |
| Authorization |  是   | Basic  33323:xxxxxx | 商户认证 merchant_id 加 secret_key，冒号分割。经过 base64 编码。注意开头的 `Basic ` 后有空格 |

- 请求参数

| 参数                 | 类型   | 长度   | 示例                   | 描述                |
| ------------------ | ---- | ---- | -------------------- | ----------------- |
| merchant_id        | 字符   | 30   | 505172               | 商户号               |
| invoice_id         | 字符   | 50   | a25087546043183910   | 商户退款请求ID          |
| orginal_invoice_id | 字符   | 30   | a25043183910         | 对应的商户成功交易ID       |
| refund_amount      | 字符   | 20,2 | 12.12                | 退款金额              |
| currency           | 字符   | 3    | USD                  | 退款币种              |
| remark             | 字符   | 60   | incorrect operations | 备注                |
| hash               | 字符   | 150  |                      | 参数签名,  详细签名方式请见附录 |



- 返回值 

|       参数       |  类型  |  长度  |        示例         |                 描述                 |
| :------------: | :--: | :--: | :---------------: | :--------------------------------: |
|   mechant_id   |  字符  |  30  |      595300       |           商户号，对应商户提交的商户号           |
|   invoice_id   |  字符  |  50  | 14067123275514607 |         交易号，对应商户提交的退款请求ID          |
|    order_no    |  字符  |  50  |    1462197131     |           订单号，退款对应的交易ID            |
|     status     |  字符  |  2   |        01         |      交易状态  01 成功;00 处理中；02 失败      |
| failure_reason |  字符  | 100  |       M0023       | 如果交易状态为“失败”，则会有相应的失败原因,    见失败原因字典 |
|   trans_date   |  字符  |  8   |     20140725      |                交易日期                |
|   trans_time   |  字符  |  6   |      182511       |                交易时间                |
|      hash      |  字符  | 150  |                   |         参数签名,  详细签名方式请见附录          |


#### 示例
请求URL

https://www.dhpay.com/merchant/api/refund/

返回

```json
{
    "status": "02",
    "failure_reason": "M0029",
    "merchant_id": "200000000167187",
    "invoice_id": "a25087546043183910",
    "order_no": "123456",
    "trans_date": "20140725",
    "trans_time": "182511",
    "hash": "2ACEA0E6DBF705E55A135E89D99E78FD505AB950C8099121B5E2713DCC263A1F"
}
```

