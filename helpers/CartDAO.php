<?php
require_once 'DAO.php';

class cart
{
    public int $memberid;       // 会員ID
    public string $goodscode;   // 商品コード
    public string $goodsname;   // 商品名
    public int $price;          // 価格
    public string $detail;      // 商品詳細
    public string $goodsimage;  // 商品画像
    public int $num;            // 数量
}
class CartDAO
{
    //会員のカートデータを取得する
    public function get_cart_by_memberid(int $memberid)
    {
        //DBに接続する
        $dbh=DAO::get_db_connect();

        $sql="SELECT memberid,Cart.goodscode,goodsname,price,detail,goodsimage,num 
              FROM Cart INNER JOIN Goods ON Cart.goodscode = Goods.goodscode 
              WHERE memberid=:memberid;";

        $stmt=$dbh->prepare($sql);

        //SQLの変数に値を当てはめる
        $stmt->bindValue(':memberid',$memberid,PDO::PARAM_INT);
        $stmt->execute();

        //取得したデータをCartクラスの配列にする
        $data=[];
        while($row=$stmt->fetchObject('Cart')){
            $data[]=$row;
        }

        return $data;
    }

    //指定した商品がカートテーブルに存在するか確認する
    public function cart_exists(int $memberid,string $goodscode)
    {
        //DBに接続する
        $dbh=DAO::get_db_connect();

        $sql="SELECT *
            FROM Cart
            WHERE memberid=:memberid AND goodscode=:goodscode;";
        
        $stmt=$dbh->prepare($sql);

        //SQLに変数の値を当てはめる
        $stmt->bindValue(':memberid',$memberid,PDO::PARAM_INT);
        $stmt->bindValue(':goodscode',$goodscode,PDO::PARAM_STR);

        //SQLを実行する
        $stmt->execute();

        if($stmt->fetch() !== false){
            return true; //カートに商品が存在する
        }
        else{
            return false; //カートに商品が存在しない
        }
    }

    //カートテーブルに商品を追加する
    public function insert(int $memberid,string $goodscode,int $num)
    {
        $dbh=DAO::get_db_connect();

        //カートテーブルに同じ商品がないとき
        if(!$this->cart_exists($memberid,$goodscode)){
            //カートテーブルに商品に登録する
            $sql="INSERT INTO Cart(memberid,goodscode,num)
                  VALUES(:memberid,:goodscode,:num);";
            
            $stmt=$dbh->prepare($sql);
            $stmt->bindValue(':memberid',$memberid,PDO::PARAM_INT);
            $stmt->bindValue(':goodscode',$goodscode,PDO::PARAM_STR);
            $stmt->bindValue(':num',$num,PDO::PARAM_STR);
            $stmt->execute();
        }
        //カートテーブルに同じ商品があるとき
        else{
            //カートテーブルに商品個数を加算する
            $sql="UPDATE Cart SET num+=:num 
                  WHERE memberid=:memberid AND goodscode=:goodscode;";
            $stmt=$dbh->prepare($sql);

            $stmt->bindValue(':memberid',$memberid,PDO::PARAM_INT);
            $stmt->bindValue(':goodscode',$goodscode,PDO::PARAM_STR);
            $stmt->bindValue(':num',$num,PDO::PARAM_STR);
            $stmt->execute();
        }
    }
    
    //カートテーブルの商品個数を変更する
    public function update(int $memberid,string $goodscode,int $num)
    {
        $dbh=DAO::get_db_connect();

        $sql="UPDATE Cart SET num=:num
              WHERE memberid=:memberid AND goodscode=:goodscode;";
        
        $stmt=$dbh->prepare($sql);

        $stmt->bindValue(':memberid',$memberid,PDO::PARAM_INT);
        $stmt->bindValue(':goodscode',$goodscode,PDO::PARAM_STR);
        $stmt->bindValue(':num',$num,PDO::PARAM_STR);
        $stmt->execute();
    }

    //カートテーブルから商品を削除する
    public function delete(int $memberid,string $goodscode){
        $dbh=DAO::get_db_connect();

        $sql="DELETE FROM Cart
              WHERE memberid=:memberid AND goodscode=:goodscode;";
        
        $stmt=$dbh->prepare($sql);

        $stmt->bindValue(':memberid',$memberid,PDO::PARAM_INT);
        $stmt->bindValue(':goodscode',$goodscode,PDO::PARAM_STR);
        $stmt->execute();
    }

    //会員のカード情報を全て削除する
    public function delete_by_memberid(int $memberid){
        $dbh=DAO::get_db_connect();

        $sql="DELETE FROM Cart WHERE memberid=:memberid";

        $stmt=$dbh->prepare($sql);

        $stmt->bindValue(':memberid',$memberid,PDO::PARAM_INT);

        $stmt->execute();
    }
}

