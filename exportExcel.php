<?php
header("Content-type: text/html; charset=utf-8");

include 'ppdai_api.php';

$token=GetActiveToken();
//$amount =  QueryBalance($token);
//echo "<br> $amount<br>";

$loanList= GetLoanList($token);   
//var_dump($loanList);

//sort
        //arsort($loanList); 
        foreach ( $loanList as $key => $row ){
             $num1[$key] = $row ['CreditCode'];
        } 
        array_multisort($num1, SORT_ASC, $loanList);
    // var_dump($loanList);


echo"
<table>
  <tr>
    <th>ListingId</th>
    <th>CreditCode</th>
	<th>Amount</th>
    <th>Rate</th>
    <th>Months</th>
    <th>Degree</th>
    <th>Sex</th>
    <th>PerSuccessTimes</th>
    <th>MobileRealnameValidate</th>
    <th>VideoValidate</th>
    <th>BankCreditValidate</th>
    <th>CertificateValidate</th>
    <th>LeftAmount</th>    
  </tr>";
 

foreach($loanList as $k)
    {  
    echo"
<tr>
    <td>".$k["ListingId"]."</td>
    <td>".$k["CreditCode"]."</td>
 	<td>".$k["Amount"]."</td>
    <td>".$k["Rate"]."</td>
    <td>".$k["Months"]."</td>
    <td>".$k["Degree"]."</td>
    <td>".$k["Sex"]."</td>
    <td>".$k["PerSuccessTimes"]."</td>
    <td>".$k["MobileRealnameValidate"]."</td>
    <td>".$k["VideoValidate"]."</td>
    <td>".$k["BankCreditValidate"]."</td>
    <td>".$k["CertificateValidate"]."</td>
    <td>".$k["LeftAmount"]."</td>     
  </tr>";

}


echo "</table>";


