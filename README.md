# leifengdai - 雷锋贷

拍拍贷开放平台 http://open.ppdai.com/ 的演示站点PHP版本，包装了相应的调用。

1. 申请一个站点，得到appid和key
2. 在ppdai_api.php中替换
3. 写程序调用

注：
本演示在SAE上运行，所以数据库相关，一些MySQL调用和一般略有不同

ppdai_api.php 基类代码，供include

oAuth.php 调用oAuth获得Token\OpenId, 调用ppdai_api.php中函数，实现查询余额、获得投标中列表、债务转移列表等功能

excel.php 输出Table,可以Excel连接

autobidAll.php 按一些策略自己投标

DB.sql MySQL表


importcsv.php  将ppdai提供下载的CSV文件导入数据库

csvAnalysis.php	对于导入的CSV数据进行分析统计
