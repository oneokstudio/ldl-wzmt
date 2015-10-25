<?php
/**
 * Created by PhpStorm.
 * User: meepo
 * Date: 15/10/25
 * Time: 下午1:28
 */


if (isset($_GET['uid'])) {
    try {
        $db = new PDO('mysql:host=127.0.0.1;dbname=wzmt', 'root', 'zxc');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $db->prepare("select code from user_code where uid = :uid");
        $stmt->bindParam(':uid', $_GET['uid'], PDO::PARAM_INT);
        $stmt->execute();

        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stmt = $db->prepare("select barcode from codes where ex_code = :ex_code");
            $stmt->bindParam(':ex_code', $user['code'], PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $db = null;
            header('Location: /codes/' . $result['barcode'] . '.png');
        } else {
            $db = null;
            echo json_encode(['code' => '200']);
        }
    } catch (PDOException $e) {
        echo json_encode(['code' => '500', 'msg' => '服务器繁忙，请稍后重试']);
        die();
    }
} else {
    echo json_encode(['code' => '400', 'msg' => '请求不合法']);
}