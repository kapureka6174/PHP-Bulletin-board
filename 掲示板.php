<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>掲示板</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        table {
            margin: 0 auto;
            padding: 20px 0 100px 0;
            background: #f1f1f2;
            height: 100%;
            text-align: center;
            table-layout: fixed;
            width: 650px;
        }

        tr {
            background: #fff;
            height: 30px;
        }


        td {
            padding: 10px;
            height: 20px;
        }

        .forms {
            width: 650px;
            margin: 60px auto 20px auto;
        }

        input {
            border: 2px solid #f1f1f1;
            border-radius: 5px;
            height: 30px;
            margin: 0 5px 5px 0;
        }

        input[type="submit"] {
            width: 70px;
            height: 30px;
        }
    </style>
</head>

<body>

    <?php

    // DB接続
    function dbConnect()
    {
        $dsn = 'データベース名';
        $user = 'ユーザー名';
        $password = 'パスワード';

        try {
            $dbh = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
            ]);
        } catch (PDOException $e) {
            echo "DBへの接続が失敗しました" . $e->getMessege();
            exit();
        }

        return $dbh;
    }


    // 全データ取得
    function getAllData()
    {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM keijiban';
        $stmt = $dbh->query($sql);
        $results = $stmt->fetchAll();
        return $results;
    }


    // 編集処理
    if (!empty($_POST["editNum"]) && !empty($_POST["password"])) {
        $dbh = dbConnect();
        $id = $_POST["editNum"];
        $password = $_POST["password"];
        $sql = "SELECT * FROM keijiban WHERE id=:id AND password=:password";
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->bindValue(":password", $password);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $id = $result["id"];
        $name = $result["name"];
        $comment = $result["comment"];
        $buttonValue = "編集";
    } else {
        $name = "名前";
        $comment = "コメント";
        $buttonValue = "投稿";
    }

    ?>

    <div class="forms">

        <!--新規投稿or編集フォーム-->
        <form action="" method="post">
            <?php
            if (!empty($id)) {
                echo "<input type='number' name='editPostNum' value=$id hidden>";
            }
            ?>
            <input type="text" name="name" value=<?= $name ?>>
            <input type="textarea" name="comment" value=<?= $comment ?>>
            <?php
            if (empty($id)) {
                echo '<input type="password" name="password" placeholder="パスワード">';
            }
            ?>
            <input type="submit" name="submit" value=<?= $buttonValue ?>>
        </form>

        <!--削除フォーム-->
        <form action="" method="post">
            <input type="number" name="deleteNum" placeholder="削除番号">
            <input type="password" name="password" placeholder="パスワード">
            <input type="submit" name="delete" value="削除">
        </form>

        <!--編集申請フォーム-->
        <form action="" method="post">
            <input type="number" name="editNum" placeholder="編集番号">
            <input type="password" name="password" placeholder="パスワード">
            <input type="submit" name="edit" value="編集">
        </form>

    </div>


    <?php
    // 新規投稿
    if (!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["password"])) {
        $dbh = dbConnect();
        $sql = $dbh->prepare("INSERT INTO keijiban (name, comment, created_at, password) VALUES (:name, :comment, :created_at, :password)");
        $sql->bindParam(':name', $name, PDO::PARAM_STR);
        $sql->bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql->bindParam(':created_at', $created_at, PDO::PARAM_STR);
        $sql->bindParam(':password', $password, PDO::PARAM_STR);
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $created_at = date("Y/m/d H:i:s");
        $password = $_POST["password"];
        $sql->execute();
    }


    // 編集処理
    if (!empty($_POST["editPostNum"]) && !empty($_POST["name"]) && !empty($_POST["comment"])) {
        $dbh = dbConnect();
        $id = $_POST["editPostNum"];
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $sql = "UPDATE keijiban SET name=:name,comment=:comment WHERE id=:id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }


    // 削除処理
    if (!empty($_POST["deleteNum"]) && !empty($_POST["password"])) {
        $dbh = dbConnect();
        $id = $_POST["deleteNum"];
        $password = $_POST["password"];
        $sql = 'delete from keijiban where id=:id AND password=:password';
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->execute();
    }


    $allData = getAllData();
    ?>

    <table>
        <tr>
            <th width="30px">No</th>
            <th width="100px">投稿者</th>
            <th width="230px">内容</th>
            <th width="160px">投稿日時</th>
        </tr>


        <?php foreach ($allData as $row) : ?>
            <tr>
                <td class="id"><?php echo $row['id'] ?></td>
                <td class="name"><?php echo $row['name'] ?></td>
                <td class="comment"><?php echo $row['comment'] ?></td>
                <td class="time"><?php echo $row['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>
