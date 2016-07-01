<?php

//请替换自己的appid和appPrivateKey, 申请地址：http://open.ppdai.com/
$appid = 'xxx';
$appPrivateKey= '-----BEGIN RSA PRIVATE KEY-----
xxx
-----END RSA PRIVATE KEY-----'; //Note: 需要换行

$pi_key =  openssl_pkey_get_private($appPrivateKey);//这个函数可用来判断私钥是否是可用的，可用返回资源id Resource id   

//Global DB Object
$db = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
mysql_select_db ( SAE_MYSQL_DB, $db );

////DB//////
function GetActiveToken()
{
    $token = "";
    $sql = "SELECT Id,Token FROM `User`  Order by `Id` asc  LIMIT 1";
    $result = mysql_query ( $sql );
    
    while ( $row = mysql_fetch_array ( $result, MYSQL_NUM ) ) 
    {
        $userId = $row[0];
        $token = $row[1];
        break;
    }
    
    return $token;
}

function GetTokenByUserId($userId)
{
    $token = "";
    $sql = "SELECT Id,Token FROM `User` Where `Id`=$userId Order by `Id`";
    $result = mysql_query ( $sql );
    //echo $sql;
    //var_dump($result);
    while ( $row = mysql_fetch_array ( $result, MYSQL_NUM ) ) 
    {
        $userId = $row[0];
        $token = $row[1];
        break;
    }
    
    return $token;
}

//////////////////////////////////////////////////////////////////////////////////////////
//ac.ppdai.com/oauth2 API
//////////////////////////////////////////////////////////////////////////////////////////


//Refesh token
function RefreshToken($openId,$refreshToken)	//Not Tested yet, need to modify [User] Table
{
    global $appid;
    $request =  '{"AppID":"'.$appid .'","OpenID":"'.$openId .'","RefreshToken":"'.$refreshToken.'"}';
    
    $url= "https://ac.ppdai.com/oauth2/refreshtoken"; 
    //echo "$request, $url";
    return SendAuthRequest($url, $request);
}
    
//get auth object by code
function GetAuth($code)
{    
    global $appid;
    $request =  '{"AppID":"'.$appid .'","code":"'.$code .'"}';
    $url= "https://ac.ppdai.com/oauth2/authorize"; 
    
	return SendAuthRequest($url, $request);
}

function SendAuthRequest($url, $request)
{
    $curl = curl_init ( $url );  
    $header = array ();  
    $header [] = 'Content-Type:application/json;charset=UTF-8';  
    //echo "<br> Request: $request <br>";
              
    curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header );  
    curl_setopt ( $curl, CURLOPT_POST, 1);  
    curl_setopt ( $curl, CURLOPT_POSTFIELDS, $request );  
    curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1); 
    $result = curl_exec ( $curl );  
    curl_close ( $curl );  
    //var_dump($result); 
        
    $auth = json_decode($result,true);
    //var_dump($auth);
    if($auth == NULL || $auth==false)
    {   
        return $result;
    }
    return $auth;
}

//////////////////////////////////////////////////////////////////////////////////////////
//gw.open.ppdai.com API
//////////////////////////////////////////////////////////////////////////////////////////

function GetLoanDebtList($accessToken)
{
	global $appid, $appPrivateKey;
    $url = "http://gw.open.ppdai.com/invest/BidproductlistService/LoanDebtList";
    
    $request = '{
          "PageIndex": 1, 
          "PageSize": 1500
        }';       
    $result = SendRequest($url, $request,$appid, $appPrivateKey,$accessToken);
    //var_dump($result);
    echo "<br>Total Records:".$result["TotalRecord"]."<br>";
    return $result["LoanList"];
}


function BuyDebt($debtDealId, $accessToken)
{
	global $appid, $appPrivateKey;
    $url = "http://gw.open.ppdai.com/invest/BidService/BuyDebt";
    
    $request = '{
  "debtDealId": '.$debtDealId.'
}';       
    $result = SendRequest($url, $request,$appid, $appPrivateKey,$accessToken);
    // var_dump($result);
    if($result["Result"]==0)
        return true;
    else
    {
        echo "<br>".$result["ResultMessage"]."<br>";
        return false;
    }
}

//包装好的投标函数
function QueryBalance( $accessToken)	
{
    global $appid, $appPrivateKey;

 	$url = "http://gw.open.ppdai.com/balance/balanceService/QueryBalance";
    $request = '{}';       
    $result = SendRequest($url, $request,$appid, $appPrivateKey,$accessToken);
    $amount= 0;
    foreach($result["Balance"] as $k)
    {
        if($k["AccountCategory"]=="用户备付金.用户现金余额")
            $amount = $k["Balance"];
    }
    return $amount;
}
 
function GetMyLoanList($token, $startdate='2016-04-18',$enddate='2016-04-28')
{
     global $appid, $appPrivateKey;
    
     $url = "http://gw.open.ppdai.com/invest/BidService/BidList";
     $request = '{
      "ListingId": 0, 
      "StartTime": "'. $startdate .'", 
      "EndTime": "'. $enddate.'", 
      "PageIndex": 1, 
      "PageSize": 5000
    }';
        
    $result = SendRequest($url, $request,$appid, $appPrivateKey,$token);
    //var_dump($result);
    echo $result["TotalPages"]. ' pages, '.$result["TotalRecord"]. ' records, page 1 <hr>';
    return $result["BidList"];
}

//包装好的LoanList函数
function GetLoanList($token)
{
    global $appid, $appPrivateKey;
    
    $url = "http://gw.open.ppdai.com/invest/BidproductlistService/LoanList";
    $timestamp = gmdate("M d Y H:i:s",time()); //date('Y-m-d H:i:s',time());
    $request =  '{"timestamp": "'.$timestamp.'"}';
    
    $j = SendRequest($url, $request, $appid, $appPrivateKey, $token);
    $loanList= $j["LoanList"];     
    
    //var_dump($loanList);
    
    return $loanList;
    
    /*//sort
    foreach ( $loanList as $key => $row ){
             $num1[$key] = $row ['CreditCode'];
    } 
    array_multisort($num1, SORT_ASC, $loanList); */
}   

//包装好的投标函数
function Bid($listingId, $amount, $appID, $appPrivateKey, $accessToken)
{
 		$Bid_URL = "http://gw.open.ppdai.com/invest/BidService/Bidding";
        $bidRequest = '{
            "ListingId": '.$listingId.',
             "Amount": '.$amount.'
            }';       
        //var_dump($bidRequest); echo '<br>';
        $result = SendRequest($Bid_URL, $bidRequest,$appID, $appPrivateKey,$accessToken);
    //var_dump($result);
    
    	if($result["Result"]==0)
            return true;
    	else return false;
}
       
//包装好的发送请求函数
function SendRequest($url, $request, $appID, $appPrivateKey, $accessToken)
{    
	 	$curl = curl_init ( $url );  
        
        $timestamp = gmdate("M d Y H:i:s",time()); //UTC format            
        openssl_sign($appID.$timestamp,$Sign_request,$appPrivateKey);
        $Sign_request = base64_encode($Sign_request);
    
        openssl_sign($request,$Sign,$appPrivateKey);
        $Sign = base64_encode($Sign);
    
    //echo "Data: $request,$Sign,$appPrivateKey";
        
        $header = array ();  
        $header [] = 'Content-Type:application/json;charset=UTF-8';  
    
	    $header [] = 'X-PPD-TIMESTAMP:'.$timestamp;
        $header [] = 'X-PPD-TIMESTAMP-SIGN:'.$Sign_request;

        $header [] = 'X-PPD-APPID:' . $appID;
        $header [] = 'X-PPD-SIGN:'. $Sign;  
    	if($accessToken!=null)
        	$header [] = 'X-PPD-ACCESSTOKEN:'.$accessToken;
		        
    //var_dump($header);
    //echo '<br>';
    
        curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header );  
        curl_setopt ( $curl, CURLOPT_POST, 1);  
        curl_setopt ( $curl, CURLOPT_POSTFIELDS, $request );  
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1); 
        
        $result = curl_exec ( $curl );  
        curl_close ( $curl );  
        
        $j = json_decode($result,true);
    
    	if($j["Message"]=='令牌校验失败：‘用户无效或令牌已过有效期！’')
    	{
            var_dump($j);
    	}
    //var_dump($j);    
   		return  $j;
}
