<?php

include 'ppdai_api.php';
   
$userId = 0; 
$totalCount = 0;
$totalAmount = 0;

$sql = "SELECT Id,Token FROM `User` Where IsAutoBid=1 Order by `Id` asc  LIMIT 30";

$result = mysql_query ( $sql );

while ( $row = mysql_fetch_array ( $result, MYSQL_NUM ) ) 
{
    $userId = $row[0];
    $token = $row[1];       
    $Leftamount = QueryBalance($token);

    echo "<hr>UserID:$userId, Amount: $Leftamount<hr>";//, Token:$token" ;

    if($Leftamount<100)	continue; //not enough money

    echo "Begin Bid...<br>";
    //Get Loan List
    if($loanList ==null)
    {
        echo"<br>Get LoanList...<br>";
        $loanList= GetLoanList($token);
        //sort
        foreach ( $loanList as $key => $row ){
             $num1[$key] = $row ['CreditCode'];
        } 
        array_multisort($num1, SORT_ASC, $loanList);
    }
    
    $numOfError=0;
 
    //投标策略
    foreach($loanList as $k)
    {
        $ruleId = 0;
        
        //Core logic
        if( $k["LeftAmount"]!=$k["Amount"] && 
            $k["Amount"]<15000 && $k["Degree"]!="初中及以下"	&&  $k["Degree"]!="中专" && 
           (        
            ($k["CreditCode"] == 'B' && $k["Rate"]>=18 && $k["PerSuccessTimes"]>=1)
           || ($k["CreditCode"] == 'A' && $k["Rate"]>12 && $k["PerSuccessTimes"]>=1 )
            || ($k["CreditCode"] == 'C' && $k["Rate"]>20 && $k["Rate"]<25 && $k["PerSuccessTimes"]>1)
           || ($k["CreditCode"] == 'AA' && $k["Rate"]>11)
            )
           )
        {
            //魔镜中等级明显高的标
            $ruleId=1;
        }
        if( $k["LeftAmount"]!=$k["Amount"] && $k["Sex"]=="女" &&
            $k["Amount"]<15000 && $k["Degree"]=="本科" && $k["CertificateValidate"] ==1 && $k["MobileRealnameValidate"] ==1 
            &&  $k["Rate"]>12 &&
           !($k["CreditCode"] == 'D' && $k["Rate"]<21) &&
           !($k["CreditCode"] == 'E' && $k["Rate"]<22) &&
           !($k["CreditCode"] == 'C' && $k["Rate"]<20) 
        )
        {
                //本科手机女
                $ruleId=2;
        }
        if( $k["LeftAmount"]!=$k["Amount"] &&
                $k["Amount"]<15000 && $k["Degree"]=="研究生及以上" && $k["CertificateValidate"] ==1 
            &&  $k["Rate"]>12 &&
           !($k["CreditCode"] == 'D' && $k["Rate"]<21) &&
           !($k["CreditCode"] == 'E' && $k["Rate"]<22) &&
           !($k["CreditCode"] == 'C' && $k["Rate"]<20) 
        )
        {
                //研究生及以上
                $ruleId=3;
        }
        if( $k["LeftAmount"]!=$k["Amount"] && $k["CreditCode"] == 'E' && $k["Rate"]==24 && $k["BankCreditValidate"] ==1)
        {
            //E 24%
                $ruleId=4;
        }
        
        if($ruleId>0 && ($k["Amount"]-$k["LeftAmount"])>50)
        {
            //echo "<br>".$k["ListingId"];
            //check ListingId duplidates
            $sql = 'SELECT Listingid  FROM `Mybid` WHERE `ListingId`='.$k["ListingId"] . ' AND `UserId`='. $userId;
            $result1 = mysql_query($sql);
            $num = mysql_num_rows($result1);
            //echo "<br>$num";
            if($num==0 && numOfError<=2)
            {
                $amount = 50;
                if($k["CreditCode"] == 'AA' && $Leftamount>=200) 
                    $amount=200;	//Bid more for AA
                
                $bidResult = Bid($k["ListingId"],$amount,$appid, $pi_key,$token) ;
                
                if($bidResult== false)
                {
                    $numOfError++;
                    if($numOfError>2)
                        continue;
                }
                else
                {
                    echo 'bid '.$k["ListingId"] .', '.$k["Rate"].', '.$k["CreditCode"].'<br>';
                    
                    $sql = 'INSERT INTO `Mybid`(`UserId`,`Id`,`ListingId`,`CreationDate`, `Amount`, `Rate`, `CreditRank`, `RuleId`) 
                    Values('.$userId .',NULL,'.$k["ListingId"].', now(),'.$amount.','.$k["Rate"].',"'.$k["CreditCode"].'",'.$ruleId.')';
                    //echo $sql;
                    mysql_query ( $sql );  
                    
                    $totalCount++;
                    $totalAmount +=$amount;
                    
                    $Leftamount -=$amount;
                    //break;
                }
            } 
        }        
        
        if($Leftamount<50)	
            break;
    } 
}


mysql_free_result ( $result );

?>
