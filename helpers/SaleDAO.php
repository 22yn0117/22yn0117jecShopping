<?php
require_once 'DAO.php';
require_once 'CartDAO.php';
require_once 'SaleDetailDAO.php';

class SaleDAO
{
    //Saleテーブルから最新のSaleNoを取得する
    private function get_saleno()
    {
        $dbh=DAO::get_db_connect();

        //Saleテーブルから、最新の販売番号を取得するSQL
        $sql="SELECT IDENT_CURRENT('Sale') AS saleno";

        //SQLを実行する
        $stmt=$dbh->query($sql);

        $row=$stmt->fetchObject();
        return $row->saleno;    //最新のsalenoを返す
    }

    //DBに購入っデータを追加する
    public function insert(int $memberid, Array $cart_list)
    {
        //DBに接続する
        $dbh=DAO::get_db_connect();

        //Saleテーブルに購入情報を追加する
        $sql="INSERT INTO Sale(saledate,memberid) VALUES (:saledate,:memberid)";

        $stmt=$dbh->prepare($sql);

        //現在時刻を取得する
        $saledate=date('Y-m-d H:i:s');

        //SQLに変数の値を当てはめる
        $stmt->bindValue(':saledate',$saledate,PDO::PARAM_STR);
        $stmt->bindValue(':memberid',$memberid,PDO::PARAM_INT);

        //SQLを実行する
        $stmt->execute();

        //最新のsalenoを返す
        $saleno=$this->get_saleno();
        $saleDetailDAO=new SaleDetailDAO();

        //カートの商品をSaleDetailテーブルに追加する
        foreach($cart_list as $cart){
            $saleDetail=new SaleDetail();

            $saleDetail->saleno=$saleno;                //販売番号
            $saleDetail->goodscode=$cart->goodscode;    //カートの商品コード
            $saleDetail->num=$cart->num;                //カートの数量

            $saleDetailDAO->insert($saleDetail,$dbh);
        }
    }
}