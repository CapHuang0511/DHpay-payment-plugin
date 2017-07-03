# invoice_id、order_no、ref_no 有什么区别
- invoice_id 是商户自己的交易参考号,在商户端该参考号必须是唯一的。它是 DHpay 系统 中每个商户每笔交易的唯一标识码。
- order_no 是 商户自己的订单号。
- ref_no 是 DHpay 系统内部的交易参考号,由 DHpay 在交易返回中提供。 如果商户要查询交易,请向我们提供 ref_no (优先) 或 invoice_id + merchantID。