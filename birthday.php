<?php
     /*
     *
     *      phpbb 3.0.7 - script for sending email to users with birthdays
     *      place script outside public_html, run once a day as a cron job
     *
     *
     *       CONFIG
     *         set path to phpbb's database config: config.php, example: $config = "C:\\wamp\\www\\phpBB3\\config.php";         
     *         set subject
     *         set from
     *         set message
     */
    
    $config  = "";     // database config
    $subject = "";     // email subject
    $from    = "";     // from
    $message = "";     // email message
    
    require_once($config);
    $dsn      = 'mysql:host='.$dbhost.';dbname='.$dbname.';';
    $options  = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'");
    try {
        $pdo = new PDO($dsn, $dbuser, $dbpasswd, $options);
    }
    catch(Exception $e) {
        throw new PDOException('Database connection failed.');
    }
    $table = $table_prefix . "users";
    $sql = "SELECT username,user_email,user_birthday,user_type FROM $table";
    $sth = $pdo->prepare($sql);
    $sth->execute();
    $res = $sth->fetchAll();
    $day = trim((string)date("d"));
    $month = trim((string)date("m"));    
    foreach($res as $row)
    {
        if($row['user_type'] == 0 || $row['user_type'] == 3)
        {
            if(strlen($row['user_birthday'])>5)
            {
                if($day[0] === "0")
                    $day = $day[1];
                if($month[0] === "0")
                    $month = $month[1];
                $bdate = explode("-",$row['user_birthday']);
                if($day === trim((string)$bdate[0]) &&  $month === trim((string)$bdate[1]) )
                    sendmail($row['username'] ,$row['user_email'],$subject,$message,$from);
            }
        }
    }
    exit;
    
    function sendmail($username,$email,$subject,$message,$from)
    {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
        $headers .= $from . "\r\n";
        mail($email, $subject, $message, $headers);
    }
