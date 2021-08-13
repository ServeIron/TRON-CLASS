<?php 
class Tron {
/*
Her metot özelleştirildiği için açıklamalar metot için olabilir, servisin farklı opsiyonları için salt çalıştır.
*/ 
   #cüzdana ait transfer bilgileri döndürür

    public function getTransactions($wallet) { 
       return $this->curlPost(null,"https://api.trongrid.io/v1/accounts/$wallet/transactions",'GET');
    }
    #girilen cüzdanın tüm bilgilerini verir, trc20 protokolüne kadar.
    public function getInfoByAddress($wallet) { 
       return $this->curlPost(null,"https://api.trongrid.io/v1/accounts/$wallet",'GET');    
    }
    #rand hesap oluşturur
     public function generateAddress() { 
        return $this->curlPost(null,"https://api.shasta.trongrid.io/wallet/generateaddress",'GET');    
     }
    public function getBalance($hex){
        $data = [
            'address' => $hex,
        ];
        return $this->curlPost($data,'https://api.trongrid.io/wallet/getaccount','POST');
    }
    #girilen cüzdanın  hexini verir
    public function getInfoByAddressPrivate($wallet) { 
        $request =  $this->curlPost(null,"https://api.trongrid.io/v1/accounts/$wallet",'GET');     
        return $request['data'][0]['address'];
    }
    #cüzdanın trc20 bilgilerini döndürür
    public function getTransactionsTRC20($wallet) { 
      return $this->curlPost(null,"https://api.trongrid.io/v1/accounts/$wallet/transactions/trc20",'GET');
    }

    #adres varlığı kontrol eder
    public function validateAddress($wallet){ 
        $data = [
            'address' => $wallet,
        ];
        return $this->curlPost($data,'https://api.trongrid.io/wallet/validateaddress','POST');
    }
    # tx id sorgular
    public function getTransactionById($txId){         
        $data = [
            'value' => $txId
        ];
        return $this->curlPost($data,'https://api.trongrid.io/wallet/gettransactionbyid','POST');       
    }
  #hesap oluşturur. [bilerek test ortamında servis canlı ortama müsade etmiyor]
    public function createAddress($pass){ 
        $data = [
            'value' => $pass
        ];
       return $this->curlPost($data,'https://api.shasta.trongrid.io/wallet/createaddress');
    }
  

    private function curlPost($param = null,$url,$method = "POST") { 
      $header = [
            'Content-type: application/json',
            'TRON-PRO-API-KEY: <<KEY>>'                  
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);       
        $method == "POST" ? curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($param, JSON_FORCE_OBJECT)) : " ";
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if($status != 201 && $status !=200) {
           
            die("Error: Hata kodu $status, response $response, curl_error " . curl_error($ch) . ", curl_errno " . curl_errno($ch));
        }      
            return json_decode($response,true);      
        curl_close($ch);
    }
}
