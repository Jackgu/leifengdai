<?php

//将ppdai提供下载的CSV文件导入数据库
//基于SAE

//创建Table
/*
CREATE TABLE `ImportData` ( 
  `id` int(11) NOT NULL auto_increment, 
  `UserId` int(11) NOT NULL,
  `ListingId` varchar(50) NOT NULL, 
  `Amount` int(10) NOT NULL, 
  `Months` smallint(3) NOT NULL default '0', 
  `Rate` float(10) NOT NULL, 
  `SucDate` datetime NOT NULL, 
  `Credited` nvarchar(3) NOT NULL, 
  `Type` nvarchar(10) NOT NULL, 
  `IsFirst` nvarchar(4) NOT NULL,
  `RegisterDate` datetime NOT NULL,   
  `Zhangling` int(10) NOT NULL, 
  
  `Age` int(10) NOT NULL, 
  `Sex` nvarchar(3) NOT NULL, 

  `Province` nvarchar(10) NOT NULL, 
  `Id6` nvarchar(6) NOT NULL, 
  `Marriage` nvarchar(10) NOT NULL, 
  `Edu` nvarchar(10) NOT NULL, 
  `MobileRealnameValidate` nvarchar(10) NOT NULL, 
  `HukouValidate` nvarchar(10) NOT NULL, 
  `VideoValidate` nvarchar(10) NOT NULL, 
  `CertificateValidate` nvarchar(10) NOT NULL, 
  `BankCreditValidate` nvarchar(10) NOT NULL, 
  `TaobaoValidate` nvarchar(10) NOT NULL, 
  `PreSuccBorrow` int(10) NOT NULL, 
  `PreSuccAmount` float(10) NOT NULL, 
  `PrePayPrincipal` float(10) NOT NULL, 
  `TotalToPayPrincipal` float(10) NOT NULL, 
  `PreSuccPayback` int(10) NOT NULL, 
  `PreSuccOntimePayback` int(10) NOT NULL, 
  `PreSuccLatePayback` int(10) NOT NULL, 
  `MaxDefaultDays` int(10) NOT NULL, 
  `DebtRate` float(10) NOT NULL, 
  `BidAmount` int(10) NOT NULL, 
  `CurrentNumber` int(10) NOT NULL,
  `CurrentPaidNumber` int(10) NOT NULL,
  `PaidPrincipal` float(10) NOT NULL, 
  `PaidInterest` float(10) NOT NULL, 
  `ToPayPrincipal` float(10) NOT NULL, 
  `ToPayInterest` float(10) NOT NULL, 
  `CurrentDefaultDays` int(10) NOT NULL, 
  `CurrentStatus` nvarchar(10) NOT NULL, 
  `LastPaymentDate` datetime NOT NULL, 
  `LastPayPrincipal` float(10) NOT NULL, 
  `LastPayInterest` float(10) NOT NULL, 
  `NextPaymentDate` datetime NOT NULL, 
  `NextPayPrincipal` float(10) NOT NULL, 
  `NextPayInterest` float(10) NOT NULL, 

  `recorddate` datetime NOT NULL, 

  PRIMARY KEY  (`id`) 
) ENGINE=MyISAM  DEFAULT CHARSET=utf8; 

create index IDX_CurrentStatus on `ImportData`  (`CurrentStatus`);  
create index IDX_Credited on `ImportData`  (`Credited`);  
create index IDX_Months on `ImportData`  (`Months`);  
create index IDX_Rate on `ImportData`  (`Rate`)
*/

$db = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
mysql_select_db ( SAE_MYSQL_DB, $db );

$userId = 100;  

$action = $_GET['action']; 
if ($action == 'import') 
{ //导入CSV 
    $filename = $_FILES['file']['tmp_name'];     
    $Data = str_getcsv(file_get_contents($filename), "\n") ;

	  $i=0;
    $recorddate='';
    
    foreach($Data as &$Row) 
    {
        $data = str_getcsv($Row, ","); //parse the items in rows 
        //print_r($data);
        
        $num = count($data); 

        $i++;
        if($i==1) continue;
        if(strlen($data[2])<1) continue;
            
        if(strlen($recorddate)<1) 
        {
            $recorddate=$data[36];
        
            $sql = "SELECT *  FROM `ImportData` WHERE `recorddate`='$recorddate' AND UserId=$userId limit 2";
            $result1 = mysql_query($sql);
            $num1 = mysql_num_rows($result1);
            
            if($num1>0)            
            {
                echo "Already in";
                return;            
            }
        }
        
        $data1="($userId,";
        for ($j=0; $j<$num; $j++)
        {
            $d = iconv('gb2312', 'utf-8',$data[$j]);
            $data1 .="'$d',";
        }
        $data1 = substr($data1,0,-1); //去掉最后一个逗号 
        
        $data_values = $data1.")";
        
         $sql = "insert into ImportData (UserId, ListingId,Amount,Months,Rate,SucDate,Credited,Type,`IsFirst`, `RegisterDate` ,`Zhangling`, Age,Sex,
        `Province`, `Id6`,
        Marriage,
      `Edu`, 
      `MobileRealnameValidate` , 
      `HukouValidate` , 
      `VideoValidate` , 
      `CertificateValidate`, 
      `BankCreditValidate` , 
      `TaobaoValidate` , 
      `PreSuccBorrow`, 
      `PreSuccAmount`,
      `PrePayPrincipal` , 
      `TotalToPayPrincipal` , 
      `PreSuccPayback` ,
      `PreSuccOntimePayback` , 
      `PreSuccLatePayback` ,
      `MaxDefaultDays` , 
      `DebtRate` ,
    
      `BidAmount` , 
      `CurrentNumber` , 
      `CurrentPaidNumber`,
      `PaidPrincipal` , 
      `PaidInterest`  ,
      `ToPayPrincipal` , 
      `ToPayInterest` , 
      `CurrentDefaultDays` , 
      `CurrentStatus` , 
      `LastPaymentDate` , 
      `LastPayPrincipal` , 
      `LastPayInterest` , 
      `NextPaymentDate` , 
      `NextPayPrincipal`, 
      `NextPayInterest` , 
        
          `recorddate` 
            ) values $data_values";
        
        $query = mysql_query($sql);//批量插入数据表中 
        //echo "<hr>$sql<hr>";
        
        if($query){ 
                //echo 'Successful!'; 
            }else{ 
                echo "<hr>failed: $sql";
            } 
        
     }
 
echo "<br>Succusefully import $i items";
    
mysql_free_result ( $result );
    
}

?>
<br><br>

<form action="importcsv.php?action=import" method="post"
enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="file" id="file" />
<br />
<input type="submit" name="submit" value="Submit" />
</form>

<br>
<hr><a href=csvAnalysis.php>CSV Analysis</a>
