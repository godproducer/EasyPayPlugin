<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>EasyPay api</title>
    </head>
    <body>
        
        <?php     
            if (isset($_POST['inUser']) && isset($_POST['inPassword'])) {
                include 'EasyPayApi.php';
                $eap = new EasyPayApi($_POST['inUser'],$_POST['inPassword']);   
                $result = $eap->renderGetWallets();
                echo $result; 
                } else { ?>
        <form action="#" method="POST" enctype="multipart/form-data">
             <table border="1">
                    <thead>
                        <tr>
                            <th>Dark EasyPay</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Номер:<input type="text" name="inUser" value="" /></td>
                        </tr>
                        <tr>
                            <td>Пароль:<input type="password" name="inPassword" value="" /></td>
                        </tr>
                        <tr>
                            <td><input type="submit" value="Get info" /></td>
                        </tr>
                    </tbody>
                </table>
        </form>      
        <?php    }
        ?>
    </body>
</html>
