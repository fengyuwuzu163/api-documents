package com.boxpay.sdk.web;

import java.util.Map;
import java.util.HashMap;
import java.util.Enumeration;
import com.boxpay.sdk.config.BoxPayConfigController;
import org.springframework.stereotype.Controller;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.ResponseBody;
import com.alibaba.fastjson.JSONObject;
import com.boxpay.sdk.util.BoxUtilController;
import com.boxpay.sdk.util.HttpClientUtilController;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;


@Controller
public class PaymentController {

    @RequestMapping(value = "/payment/boxpay")
    public @ResponseBody String boxpay() throws Exception{

        HashMap<String, String> resultMap = new HashMap<>();


        String boxMchId = BoxPayConfigController.getMchId();
        String pay_channel_id = "201";
        String out_trade_no = "101"+BoxUtilController.time();
        String total_fee = "100";
        String notify_url = "https://demo.234k.cn/notify.php";
        String body = BoxUtilController.time();
        String attach = BoxUtilController.time();
        String return_url = "https://demo.234k.cn/return.php";

        resultMap.put("mch_id", boxMchId);
        resultMap.put("out_trade_no", out_trade_no);
        resultMap.put("pay_channel_id", pay_channel_id);
        resultMap.put("total_fee", total_fee);
        resultMap.put("notify_url", notify_url);
        resultMap.put("body", body);
        resultMap.put("attach", attach);
        resultMap.put("return_url", return_url);
        resultMap.put("version", "2");

        System.out.println(resultMap);

        String sign = BoxUtilController.makeSign(resultMap, BoxPayConfigController.getMchKey());
        resultMap.put("sign", sign);

        System.out.println(BoxUtilController.toQueryString(resultMap));

        String response = HttpClientUtilController.doHttpsPost(BoxPayConfigController.getPayUrl(), BoxUtilController.toQueryString(resultMap));
        JSONObject jsonObject = JSONObject.parseObject(response);

        System.out.println(jsonObject);

        String pay_url = "";
        String order_sn = "";
        String error_msg = "";

        if(jsonObject.getString("msg").equals("success")){
            pay_url = jsonObject.getJSONObject("data").getString("code_url");
            order_sn = jsonObject.getJSONObject("data").getString("order_sn");
        }else{
            error_msg = jsonObject.getString("msg");
        }

        return pay_url;
    }

    @RequestMapping(value = "/payment/query")
    public @ResponseBody String query() throws Exception{

        HashMap<String, String> resultMap = new HashMap<>();

        String boxMchId = BoxPayConfigController.getMchId();

        resultMap.put("mch_id", boxMchId);
        resultMap.put("order_sn", "20210417170710509311");
        resultMap.put("version", "2");

        System.out.println(resultMap);

        String sign = BoxUtilController.makeSign(resultMap, BoxPayConfigController.getMchKey());
        resultMap.put("sign", sign);

        String response = HttpClientUtilController.doHttpsPost(BoxPayConfigController.getQueryUrl(), BoxUtilController.toQueryString(resultMap));
        JSONObject jsonObject = JSONObject.parseObject(response);

        System.out.println(jsonObject);

        String order_status = "";
        String order_sn = "";
        String error_msg = "";

        if(jsonObject.getString("msg").equals("success")){
            order_status = jsonObject.getJSONObject("data").getString("status");
            order_sn = jsonObject.getJSONObject("data").getString("order_sn");
        }else{
            error_msg = jsonObject.getString("msg");
        }

        return order_status;
    }

    @RequestMapping(value = "/payment/notify")
    public @ResponseBody String notify(HttpServletRequest request, HttpServletResponse response) throws Exception{

        try {

            Enumeration<String> paramKeys = request.getParameterNames();
            Map<String, String> notifyMap = new HashMap<>();
            while (paramKeys.hasMoreElements()) {
                String key = paramKeys.nextElement();
                notifyMap.put(key, request.getParameter(key));
            }

            //验证签名
            boolean isTrue = BoxUtilController.checkSign(notifyMap,BoxPayConfigController.getMchKey());


            String status = notifyMap.get("status");
            if (isTrue && "2".equals(status)) {
                // 签名正确,且状态为成功
                // 进行处理。
                // 注意特殊情况：订单已经退款，但收到了支付结果成功的通知，不应把商户侧订单状态从退款改成支付成功
                //获取交易记录

                //商户订单号
                String out_trade_no = notifyMap.get("out_trade_no");

                //第三方支付订单号
                String trade_no = notifyMap.get("order_sn");

                //订单状态
                String order_status = notifyMap.get("status");




                return "SUCCESS";
            } else {
                // 签名错误，如果数据里没有sign字段，也认为是签名错误
                System.out.println("boxpay sign is error");
            }
        }catch (Exception e){
            System.out.println(e);
        }
        return "FAIL";
    }




}
