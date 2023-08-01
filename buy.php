<?php
    require_once './helpers/MemberDAO.php';
    require_once './helpers/CartDAO.php';
    require_once './helpers/SaleDAO.php';

    //セッションを開始する
    session_start();

    //未ログインの場合
    if(isset($member)){
        //ログインページにリダイレクトする
        header('Location: login.php');
        exit;
    }

    //「購入する」ボタンをクリックせずにこのページを表示した場合はcart.phpにリダイレクトする
    if($_SERVER['REQUEST_METHOD']!=='POST'){
        header('Location: cart.php');
        exit;
    }

    //ログイン中の会員データを取得
    $member=$_SESSION['member'];

    //会員のカートデータを取得
    $cartDAO=new CartDAO();
    $cart_list=$cartDAO->get_cart_by_memberid($member->memberid);   

    //カートの商品をSaleテーブルに登録する
    $saleDAO=new SaleDAO();
    $saleDAO->insert($member->memberid,$cart_list);

    //会員のカートデータをすべて削除する
    $cartDAO->delete_by_memberid($member->memberid);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>購入完了</title>
    </head>
    <body>
        <?php include "header.php" ?>
        <p>購入が完了しました</p>
        <a href="index.php">
            <p>トップページへ</p>
        </a>
    </body>
</html>