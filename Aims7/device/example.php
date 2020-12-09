<?php
use PEAR2\Net\RouterOS;
require_once 'PEAR2/Autoload.php';

//header('Content-Type: text/plain');
$ip = '103.131.157.34';
$user = 'admin';
$password = 'ad9090';
$port = '8728';

$client = new RouterOS\Client($ip,$user,$password, null, false, 3);
/*
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <title>RouterOS log</title>
        <style type="text/css">
            table, td, th {border: 1px solid black;}
            td span {outline: 1px dotted black;}
        </style>
    </head>
    <body>
        <?php
        try {
            $util = new RouterOS\Util($client = new RouterOS\Client($ip,$user,$password,$port));
        ?><table>
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Topics</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($util->setMenu('/log')->getAll() as $entry) { ?>
                <tr>
                    <td><?php echo $entry('time'); ?></td>
                    <td>
                    <?php foreach (explode(',', $entry('topics')) as $topic) { ?>
                        <span><?php echo $topic; ?></span>
                    <?php } ?>
                    </td>
                    <td><?php echo $entry('message'); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } catch (Exception $e) { ?>
            <div>Unable to connect to RouterOS.</div>
        <?php } ?>
    </body>
</html>


*/ ?>