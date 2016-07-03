<?php
echo "This is a simple statistics page for imported data <hr/><br/>"; 

$db = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
mysql_select_db ( SAE_MYSQL_DB, $db );

echo "投标时间, Count, Amount<br/>";
$sql = "SELECT YEAR(`SucDate`), MONTH(`SucDate`),count(*),sum(`BidAmount`) FROM `ImportData` group by YEAR(`SucDate`), MONTH(`SucDate`)";
$result = mysql_query ( $sql );
while ( $row = mysql_fetch_array ( $result, MYSQL_NUM ) ) 
{
    $amount = $row[3];
    echo "$row[0]/$row[1], $row[2], $amount<br/>";
}
echo "<br/>";

echo "标当前状态, Count, Amount<br/>";
$sql = "SELECT CurrentStatus,count(*),sum(`BidAmount`) FROM `ImportData` group by CurrentStatus";
$result = mysql_query ( $sql );
while ( $row = mysql_fetch_array ( $result, MYSQL_NUM ) ) 
{
    echo "$row[0], $row[1], $row[2]<br/>";
    
}
echo "<br/>";

echo "Credited, Count, Amount<br/>";
$sql = "SELECT Credited,count(*),sum(`BidAmount`) FROM `ImportData` group by Credited";
$result = mysql_query ( $sql );
while ( $row = mysql_fetch_array ( $result, MYSQL_NUM ) ) 
{
    echo "$row[0], $row[1], $row[2]<br/>";
    
}
echo "<br/>";

echo "期限, Count, Amount<br/>";
$sql = "SELECT Months,count(*),sum(`BidAmount`) FROM `ImportData` group by Months";
$result = mysql_query ( $sql );
while ( $row = mysql_fetch_array ( $result, MYSQL_NUM ) ) 
{
    echo "$row[0], $row[1], $row[2]<br/>";
    
}
echo "<br/>";

echo "Rate, Count, Amount<br/>";
$sql = "SELECT Rate,count(*),sum(`BidAmount`) FROM `ImportData` group by Rate";
$result = mysql_query ( $sql );
while ( $row = mysql_fetch_array ( $result, MYSQL_NUM ) ) 
{
    echo "$row[0], $row[1], $row[2]<br/>";
    
}
echo "<br/>";

echo "Province, Count, Amount<br/>";
$sql = "SELECT Province,count(*),sum(`BidAmount`) FROM `ImportData` group by Province";
$result = mysql_query ( $sql );
while ( $row = mysql_fetch_array ( $result, MYSQL_NUM ) ) 
{
    echo "$row[0], $row[1], $row[2]<br/>";    
}
echo "<br/>";

echo "Type, Count, Amount<br/>";
$sql = "SELECT Type,count(*),sum(`BidAmount`) FROM `ImportData` group by Type";
$result = mysql_query ( $sql );
while ( $row = mysql_fetch_array ( $result, MYSQL_NUM ) ) 
{
    echo "$row[0], $row[1], $row[2]<br/>";    
}
echo "<br/>";

mysql_free_result ( $result );

