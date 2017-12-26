# 错误响应码
|  序号  |  错误码  |                   原因说明                   | 英文说明                                     |
| :--: | :---: | :--------------------------------------: | ---------------------------------------- |
|  1   | M0001 |                  商户号错误                   | Merchant id is wrong                     |
|  2   | M0002 |                  交易号错误                   | invoice id is wrong                      |
|  3   | M0003 |                  订单号错误                   | order no is wrong                        |
|  4   | M0004 |                  卡号不合法                   | card number is illegal                   |
|  5   | M0005 |                  过期年不合法                  | card expires year illegal                |
|  6   | M0006 |                  过期月不合法                  | card expires month illegal               |
|  7   | M0007 |                 cvv 不合法                  | cvv illegal                              |
|  8   | M0008 |              firstname 不合法               | first name illegal                       |
|  9   | M0009 |               lastname 不合法               | last name illegal                        |
|  10  | M0010 |                  币种不合法                   | currency illegal                         |
|  11  | M0011 |           金额不合法，格式为数字且必须带两位小数            | amount illegal                           |
|  12  | M0012 |                买家 ip 不合法                 | buyer ip illegal                         |
|  13  | M0013 |                 买家邮箱不合法                  | buyer email illegal                      |
|  14  | M0014 |                  备注不合法                   | remark illegal                           |
|  15  | M0015 |                  签名不能为空                  | hash can't be null                       |
|  16  | M0016 |                  签名验证失败                  | hash validate failed                     |
|  17  | M0017 |               单笔交易小于商户最小限额               | Amount less than the minimum amount      |
|  18  | M0018 |               单笔交易大于商户最大限额               | Amount greater than the minimum amount   |
|  19  | M0019 |                 TOKEN 过长                 | token too long                           |
|  20  | M0020 |                 返回地址不合法                  | address illegal                          |
|  21  | M0021 |                 货运国家不合法                  | shipping country illegal                 |
|  22  | M0022 |              鉴权商户号与提交商户号不符               | Validate merchent id not equals to submit merchant id |
|  23  | M0023 |                  交易号重复                   | duplicate transaction no                 |
|  24  | M0024 |                 未匹配到原始交易                 | Not matched original transaction         |
|  25  | M0025 |                线上退款不支持该卡种                | Online refund does not support the card  |
|  26  | M0026 |               原始交易存在处理中的退款               | The original transaction has a refund in processing |
|  27  | M0027 |                 原始交易被拒付                  | The original transaction was chargebacked |
|  28  | M0028 |             AE 卡支付记录未登记汇率信息              | AE card payment records unregistered exchange rate information |
|  29  | M0029 |                  可退金额不足                  | Insufficient amount for refund           |
|  30  | M0030 |                  退款通道异常                  | refund channel exception                 |
|  31  | M0031 |                  参数校验失败                  | parameters validate  failed              |
|  32  | M0032 |                 查询交易不存在                  | can't find this transaction              |
|  33  | M0033 |                  账户余额不足                  | account balance is insufficient          |
|  34  | M0034 |                  账户处理失败                  | account processing failed                |
|  35  | M0035 |                 退款提交银行异常                 | refunds submitted to bank exception      |
|  36  | M0036 |                  获取汇率失败                  | failed to get exchange rate              |
|  37  | M0037 |                 信用卡年月过期                  | credit card expires                      |
|  38  | M0038 |                 账单地址不合法                  | billing address illegal                  |
|  39  | M0039 |                 账单国家不合法                  | billing country illegal                  |
|  40  | M0040 |                  账单州不合法                  | billing states illegal                   |
|  41  | M0041 |                 账单城市不合法                  | billing city illegal                     |
|  42  | M0042 |                 账单邮编不合法                  | billing post code illegal                |
|  43  | M0043 |                 账单电话不合法                  | billing telephone illegal                |
|  44  | M0044 |                  调用网关超时                  | gateway timeout                          |
|  45  | M0045 |                  系统内部错误                  | system error                             |
|  46  | M0046 |           TOKEN 验证失败，重复 TOKEN            | duplicated token                         |
|  47  | M0047 |           TOKEN 验证失败，TOKEN 不一致           | token can't match                        |
|  48  | M0048 |           TOKEN 验证失败，TOKEN 失效            | token expires                            |
|  49  | M0049 |                TOKEN 验证失败                | token validate failed                    |
|  50  | M0150 |                  字段不能为空                  | field can't be empty                     |
|  51  | M0151 |                 商户状态不正常                  | merchant status abnormal                 |
|  52  | M0152 |                 字段长度不正确                  | field length incorrect                   |
|  53  | M0153 |                商户平台订单号重复                 | duplicate merchant order id              |
|  54  | M0154 |                Ip 受限，交易拒绝                | ip banned                                |
|  55  | M0155 |      交易金额、运费等金额信息必须为数字型，且小数点后只能保留两位      | amount must be float                     |
|  56  | M0156 |                 交易种类不合法                  | transaction type illegal                 |
|  57  | M0157 |                 卡类输入不合法                  | card type illegal                        |
|  58  | M0158 |                 支付卡号不合法                  | card number illegal                      |
|  59  | M0159 |                支付卡安全码不合法                 | payment card cvv illegal                 |
|  60  | M0160 |                信用卡有效年不合法                 | card expires year illegal                |
|  61  | M0161 |                信用卡有效月不合法                 | card expires month illegal               |
|  62  | M0162 |                email 不合法                 | email illegal                            |
|  63  | M0163 |                 固定电话不合法                  | telephone illegal                        |
|  64  | M0164 |                 移动电话不合法                  | mobile illegal                           |
|  65  | M0165 |                  国家不合法                   | country illegal                          |
|  66  | M0166 |                 产品代码不合法                  | product no illegal                       |
|  67  | M0167 |                  城市不合法                   | city illegal                             |
|  68  | M0168 |             防钓鱼非法字符输入，字段不合法              | field illegal                            |
|  69  | M0169 | 订单金额、运费以及产品单价数量之间的关系检查失败（gate 不用考虑）70 M0170 支付方式检查失败 | payment method validate failed           |
|  71  | M0171 |               交易金额必须大于最小费用               | amount must greater than min fee         |
|  72  | M0172 |        输入不符合格格式或解释子订单串出错（例如非分号分隔）        | unsupported filed format                 |
|  73  | M0173 |                  订单重复提交                  | transaction re-submit                    |
|  74  | M0174 |                商户提交参数不合法                 | fileds illegal                           |
|  75  | M0175 |                 商户订单号不合法                 | merchant order id illegal                |
|  76  | M0176 |                  商户号不合法                  | merchant id illegal                      |
|  77  | M0177 |                  商户不存在                   | merchant id don't exist                  |
|  78  | M0178 |                  人名不合法                   | firstname illegal                        |
|  79  | M0179 |                  人姓不合法                   | lastname illegal                         |
|  80  | M0180 |                  州省不合法                   | states illegal                           |
|  81  | M0281 |               持卡人超过交易限制次数                | cardholder exceeds the trade limit       |
|  82  | M5555 |                 交易被发卡行拒绝                 | reject by  issuer bank                   |
|  83  | M1083 |                  信用卡已过期                  | card                                     |
|  84  | M1084 |                   系统错误                   | credit card has expired                  |
|  85  | M1085 |                 3D 验证失败                  | three D validate failed                  |
|  86  | M1086 |                   交易超时                   | transaction timeout                      |
|  87  | M2087 |                  高风险交易                   | high risk trade                          |
|  88  | M2088 |                   无效卡号                   | invalid card number                      |
|  89  | M0091 |                 商品名称不合法                  | product name illegal                     |
|  90  | M0092 |                 商品单价不合法                  | product price illegal                    |
|  91  | M0093 |                 商品数量不合法                  | product quantity illegal                 |
|  92  | M0271 |               交易金额需大于 1 美金               | amount must greater than 1 dollar        |
|  93  | M0186 |          银联标识卡不支持（国内信用卡只支持单标卡）           | The card with China Union Pay logo is not accepted. |

# 新增错误响应码
|  序号  |                   错误码                    |
| :--: | :--------------------------------------: |
|  99  | M0050=Refund capital account service degradation |
| 100  | ML1001=Please enter your email and password |
| 101  |      ML1002=Please enter your email      |
| 102  |    ML1003=Please enter your password     |
| 103  |      ML1004=Email or password error      |
| 104  | ML1005=Your password has been wrong more than six times in a row. Please try again in 30 Minutes |
| 105  |      ML1006=Email or password error      |
| 106  |     ML0010=Account information error     |
| 107  | ML0011=Account is not active, not account payment |
| 108  | ML0012=Only personal account can pay to account |
| 109  |     ML0013=Account is not available      |
| 110  |    ML1014=Account is not operational     |
| 111  | M0282 =Account name or password is incorrect |
| 112  |            M1007 =cvv illegal            |
| 113  |     M1023 =Repeat transaction number     |
| 114  | M1081 =Limit the number of cardholders over transactions |
| 115  |        M2086 =Transaction timeout        |
| 116  |           M2084 =System error            |
| 117  | M2011  =The  amount  of  illegal,  the  format  with  two  decimal figures and must |
| 118  |     M2023 =Repeat transaction number     |
| 119  |      M2083 =Credit card has expired      |
| 120  | M2081 =Limit the number of cardholders over transactions |
| 121  |            M2007 =cvv illegal            |
| 122  | M3011  =The  amount  of  illegal,  the  format  with  two  decimal figures and must |
| 123  |     M3023 =Repeat transaction number     |
| 124  |           M3084 =System error            |
| 125  |      M3083 =Credit card has expired      |
| 126  |        M3085 =3D validation fails        |
| 127  |        M3086 =Transaction timeout        |
| 128  |     M0055 =VOID refund must be fully     |
