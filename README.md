php 调用环信接口案例
=============
官方没有给出何种语言的调用方式，而给的是linux下CURL命令行方式
--------------------
    如：
    curl -X GET -H "Authorization: Bearer YWMtP_8IisA-EeK-a5cNq4Jt3QAAAT7fI10IbPuKdRxUTjA9CNiZMnQIgk0LAAA" -i "https://a1.easemob.com/easemob-demo/chatdemoui/chatgroups?limit=2"
    得知
      *get方式请求,
      *请求头为 "Authorization: Bearer YWMtP_8IisA-EeK-a5cNq4Jt3QAAAT7fI10IbPuKdRxUTjA9CNiZMnQIgk0LAAA"
        *token为 YWMtP_8IisA-EeK-a5cNq4Jt3QAAAT7fI10IbPuKdRxUTjA9CNiZMnQIgk0LAAA
      *地址：https://a1.easemob.com/easemob-demo/chatdemoui/chatgroups?limit=2
    
