<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?
include 'ppdai_api.php';

$code = $_GET['code'];

if($code ==null)  
{
    $appId = "xxxx";  //替换你自己的AppId
    $callbackUrl = "http://xxx.applinzi.com/oAuth.php";   //替换你自己的AppId
    echo "<br><br><a href='https://ac.ppdai.com/oauth2/login?AppID=$appId&ReturnUrl=$callbackUrl'>跳转到拍拍贷授权登录</a>";  //Redirect to get code page
        return;
}

$j = GetAuth($code);
$token =$j["AccessToken"];
if($token==null)
{
	echo '<br>token error<br>';
    return;
}
$openId =$j["OpenID"];
$RefreshToken = $j["RefreshToken"];
$ExpiresIn= $j["ExpiresIn"];    


$amount = QueryBalance($token);

echo "<br>User $openId, 余额: $amount<br>";

$loanList= GetLoanList($token);        
$i=0;
echo "<br/><br/><br/>所有列表(共有列表：".count($loanList).")<hr/><br/>";
foreach($loanList as $k)
{
    echo '<a target="_blank" href="http://www.ppdai.com/list/'.$k["ListingId"].'">'.$k["ListingId"]."</a> - ".$k["CreditCode"]." - ".$k["Amount"]." - ".$k["Rate"]."%<br/>"; 
    //.$k["BorrowerName"]."- " 
    $i++; if($i==10) break;
}


$debtList = GetLoanDebtList($token);
$i=0;
echo "<br/><br/><br/>所有债转列表(共有列表：".count($debtList).")<hr/><br/>";
foreach($debtList as $k)
{
    echo '<a target="_blank" href="http://www.ppdai.com/list/'.$k["ListingId"].'">'.$k["ListingId"]."</a> - ".$k["CreditCode"]." - ".$k["Amount"]." - ".$k["PriceforSaleRate"]."%<br/>"; 
    //.$k["BorrowerName"]."- " 
    $i++; if($i==10) break;
}



