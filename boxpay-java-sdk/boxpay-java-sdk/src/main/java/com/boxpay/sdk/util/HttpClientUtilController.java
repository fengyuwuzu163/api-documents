package com.boxpay.sdk.util;

import java.io.*;
import java.net.URL;
import javax.net.ssl.SSLSession;
import javax.net.ssl.SSLContext;
import javax.net.ssl.TrustManager;
import org.apache.commons.io.IOUtils;
import javax.net.ssl.HttpsURLConnection;
import javax.net.ssl.HostnameVerifier;
import javax.net.ssl.X509TrustManager;

public class HttpClientUtilController {

    /**
     * 跳过https的 post请求
     *
     * @param url
     * @param params
     * @return
     */
    public static String doHttpsPost(String url, String params) throws Exception {
        URL myURL = new URL(url);
        HttpsURLConnection httpURLConnection = (HttpsURLConnection) myURL.openConnection();

        TrustManager[] trustAllCerts = new TrustManager[] { new X509TrustManager() {
            public java.security.cert.X509Certificate[] getAcceptedIssuers() {
                return null;
            }

            public void checkClientTrusted(java.security.cert.X509Certificate[] certs, String authType) {
            }

            public void checkServerTrusted(java.security.cert.X509Certificate[] certs, String authType) {
            }
        } };
        SSLContext sc;
        sc = SSLContext.getInstance("TLS");
        sc.init(null, trustAllCerts, new java.security.SecureRandom());
        httpURLConnection.setSSLSocketFactory(sc.getSocketFactory());
        HostnameVerifier hv = new HostnameVerifier() {
            @Override
            public boolean verify(String urlHostName, SSLSession session) {
                return true;
            }
        };
        // System.out.println("===========5===========");
        httpURLConnection.setHostnameVerifier(hv);

        // System.out.println("===========6===========");
        httpURLConnection.setRequestMethod("POST");
        httpURLConnection.setRequestProperty("User-Agent",
                "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; CIBA)");
        httpURLConnection.setRequestProperty("Accept-Encoding", "gzip");
        httpURLConnection.setRequestProperty("Referer", "no-referrer");
        httpURLConnection.setRequestProperty("Content-Type", "application/x-www-form-urlencoded");
        httpURLConnection.setConnectTimeout(9000000);
        httpURLConnection.setReadTimeout(9000000);
        if (params != null) {
            httpURLConnection.setDoOutput(true);
            DataOutputStream out = new DataOutputStream(httpURLConnection.getOutputStream());
            out.write(params.getBytes("UTF-8"));
            out.close();
        }
        // 连接
        httpURLConnection.connect();
        // 得到响应状态码的返回值 responseCode
        int code = httpURLConnection.getResponseCode();
        ;
        // 如果返回值正常，数据在网络中是以流的形式得到服务端返回的数据
        // System.out.println(file.exists()+" "+file.getPath()+" "+imgUrl+" \n
        // "+code);
        StringBuffer buffer = new StringBuffer();
        if (code == 200) { // 正常响应
            InputStream inputStream = httpURLConnection.getInputStream();

            byte[] bytes = IOUtils.toByteArray(inputStream);

            // 检查图片是否完整
            inputStream = new ByteArrayInputStream(bytes);
            InputStreamReader inputReader = new InputStreamReader(inputStream, "UTF-8");
            BufferedReader reader = new BufferedReader(inputReader);
            String line;
            while ((line = reader.readLine()) != null) {
                buffer.append(line);
            }
        }
        return buffer.toString();
    }
}
