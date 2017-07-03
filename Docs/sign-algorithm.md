# 签名算法

#### 商户提交支付请求参数中 hash 字段的签名方法

商户提交支付请求时,对以下字段进行签名校验:商户号、交易号、币种、金额, 方法如 下:

- 使用 map 键值对组织需要提交的参数。
- 按键升序排列值。
- 将值合成一串,排除空值。
- 将商户密钥放在值串的最前面。
- 签名算法使用 sha256(SHA-256)算法,对最终的值串进行签名。

#### java代码示例:
```java
import java.security.MessageDigest; import java.util.ArrayList;
import java.util.Collections;
import java.util.Iterator;
import java.util.List; import java.util.Map;
public class DigestUtil {
    /**
     * 签名方法
     * @param values 需要进行签名的参数 Map * @param key 商户秘钥,由 DHpay 颁发
     * @return
     */
    public static String hash(Map<String, String> values, String key) { List<String> fieldNames = new ArrayList<String>(values.keySet()); Collections.sort(fieldNames);
        StringBuffer buf = new StringBuffer();
        buf.append(key);
        for (Iterator<String> itr = fieldNames.iterator(); itr.hasNext();) {
            String fieldName = (String) itr.next();
            String fieldValue = (String) values.get(fieldName);
            if (null != fieldValue && !fieldValue.trim().equals(""))
                buf.append(fieldValue);
        }
        return hash(buf.toString(), "SHA-256"); }
    private static String hash(String s, String algorithm) {
        MessageDigest md5 = null; byte ba[] = (byte[]) null; try {
            md5 = MessageDigest.getInstance(algorithm);
            ba = md5.digest(s.getBytes("ISO-8859-1")); } catch (Exception exception) {
        }
        return hex(ba);
    }
    private static String hex(byte input[]) {
        StringBuffer sb = new StringBuffer(input.length * 2);
        for (int i = 0; i < input.length; i++) { sb.append(HEX_TABLE_UPPER[input[i] >> 4 & 0xf]); sb.append(HEX_TABLE_UPPER[input[i] & 0xf]);
        }
        return sb.toString(); }
    private static final char[] HEX_TABLE_UPPER= { '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F'};
}

```

#### 商户接收支付响应参数中 hash 字段的签名方法

商户接收 DHpay 发送的支付响应时,DHpay 系统会对商户提交支付请求后,返回给商户的“Web 回调返回参数”的除 description 之外的全部字段进行签名,方法如下:

- 使用 map 键值对组织需要提交的参数。
- 按键升序排列值。
- 将值合成一串,排除空值。
- 将商户密钥放在值串的最前面。

签名算法使用 sha256(SHA-256)算法,对最终的值串进行签名。

