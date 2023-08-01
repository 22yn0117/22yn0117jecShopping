<?php
require_once 'DAO.php';

class Goods
{
    public string $goodscode;   // 商品コード
    public string $goodsname;   // 商品名
    public int $price;  // 価格
    public string $detail;  // 商品詳細
    public int $groupcode;  // 商品グループコード
    public bool $recommend; // おすすめフラグ
    public string $goodsimage;  // 商品画像
}

// Goodsテーブルアクセス用クラス
class GoodsDAO
{
    // おすすめ商品を取得するメソッド
    public function get_recommend_goods()
    {
        // DBに接続する
        $dbh=DAO::get_db_connect();

        // Goodsテーブルからおすすめの商品を取得する
        $sql="SELECT * FROM Goods WHERE recommend = 1";

        $stmt=$dbh->prepare($sql);

        // SQLを実行する
        $stmt->execute();

        // 取得したデータを配列にする
        $data=[];
        while($row=$stmt->fetchObject('Goods')){
            $data[]=$row;
        }

        return $data;
    }

    public function get_goods_by_groupcode(int $groupcode)
    {
        // DBに接続する
        $dbh=DAO::get_db_connect();

        $sql="SELECT * FROM goods WHERE groupcode = :groupcode ORDER BY recommend DESC";

        $stmt=$dbh->prepare($sql);

        // SQLに変数の値を当てはめる
        $stmt->bindValue(':groupcode',$groupcode,PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();

        // 取得したデータをGoodsクラスの配列にする
        $data=[];
        while($row=$stmt->fetchObject('Goods')){
            $data[]=$row;
        }

        return $data;
    }

    // 引数で指定した商品コードの商品を取得する
    public function get_goods_by_goodscode(string $goodscode)
    {
        // DBに接続する
        $dbh=DAO::get_db_connect();

        $sql="SELECT * FROM goods WHERE goodscode = :goodscode";

        $stmt=$dbh->prepare($sql);

        // SQLに変数の値を当てはめる
        $stmt->bindValue(':goodscode',$goodscode,PDO::PARAM_STR);

        // SQLを実行する
        $stmt->execute();

        // 1件分のデータをGoodsクラスのオブジェクトとして取得する
        $goods=$stmt->fetchObject('Goods');
        return $goods;
    }

    public function get_goods_by_keyword(string $keyword)
    {
        // DBに接続する
        $dbh=DAO::get_db_connect();

        $sql="SELECT * FROM goods WHERE goodsname LIKE :keyword1 OR detail LIKE :keyword2";

        $stmt=$dbh->prepare($sql);

        // SQLに変数の値を当てはめる
        $stmt->bindValue(':keyword1','%'.$keyword.'%',PDO::PARAM_STR);
        $stmt->bindValue(':keyword2','%'.$keyword.'%',PDO::PARAM_STR);

        // SQLを実行する
        $stmt->execute();

        $data=[];
        while($row=$stmt->fetchObject('Goods')){
            $data[]=$row;
        }

        return $data;
    }
}