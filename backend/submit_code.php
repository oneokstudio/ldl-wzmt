<?php
/**
 * Created by PhpStorm.
 * User: meepo
 * Date: 15/10/25
 * Time: 下午1:59
 */

if (isset($_POST['uid']) && isset($_POST['code'])) {
    try {
        $db = new PDO('mysql:host=127.0.0.1;dbname=wzmt', 'root', 'zxc');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $db->prepare("select code from user_code where uid = :uid");
        $stmt->bindParam(':uid', $_POST['uid'], PDO::PARAM_STR);
        $stmt->execute();

        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stmt = $db->prepare("select barcode from codes where ex_code = :ex_code");
            $stmt->bindParam(':ex_code', $user['code'], PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $db = null;
            $url = dirname('http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']) . '/codes/' . $result['barcode'] . '.png';
            echo json_encode(['code' => '200', 'claimed' => 'true',
                              'codeUrl' => $url], JSON_UNESCAPED_SLASHES);
        } else {
            $stmt = $db->prepare("select * from codes where ex_code = :ex_code");
            $stmt->bindParam(':ex_code', $_POST['code'], PDO::PARAM_STR);
            $stmt->execute();

            if ($codeInfo = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($codeInfo['used']) {
                    $db = null;
                    echo json_encode(['code' => '400', 'msg' => '该兑换码已被使用']);
                } else {
                    $stmt = $db->prepare("update codes set used = 1 where ex_code = :ex_code");
                    $stmt->bindParam(':ex_code', $_POST['code'], PDO::PARAM_STR);
                    $stmt->execute();
                    $db = null;
                    $url = dirname('http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']) . '/codes/' . $codeInfo['barcode'] . '.png';
                    echo json_encode(['code' => '200', 'codeurl' => $url], JSON_UNESCAPED_SLASHES);
                }
            } else {
                $db = null;
                echo json_encode(['code' => '400', 'msg' => '兑换码不存在!']);
            }
        }

    } catch (PDOException $e) {
        echo json_encode(['code' => '500', 'msg' => '服务器繁忙，请稍后重试']);
        die();
    }
} else {
    echo json_encode(['code' => '400', 'msg' => '请求不合法']);
}